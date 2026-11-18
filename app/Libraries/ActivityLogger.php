<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;

/**
 * ActivityLogger — Centralized Audit Trail Service
 *
 * Digunakan oleh controller untuk mencatat aktivitas operasional.
 * Identitas pengguna, IP, dan user agent diperoleh secara otomatis.
 *
 * PENGGUNAAN DASAR (di luar transaction):
 *   $logger = new \App\Libraries\ActivityLogger();
 *   $logger->log('create', 'product', 'Produk Roti Tawar dibuat', null, $newData, 'product', $productId);
 *
 * PENGGUNAAN DALAM TRANSACTION:
 *   $db->transStart();
 *   // ... operasi bisnis ...
 *   $logger->logInTransaction($db, 'create', 'produksi', 'Produksi baru', null, $data, 'produksi', $id);
 *   $db->transComplete();
 *
 * DATA SENSITIF:
 *   Service ini TIDAK mencatat password, token, session ID, atau credential.
 *   Filter sensitif diterapkan sebelum data disimpan ke database.
 */
class ActivityLogger
{
    /**
     * Field yang akan disaring (tidak dicatat ke log).
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirm',
        'old_password',
        'token',
        'session_id',
        'midtrans_token',
        'snap_token',
        'payment_token',
        'api_key',
        'secret_key',
        'remember_code',
        'forgotten_password_code',
        'forgotten_password_time',
        'ip_address',
        'user_agent',
    ];

    /**
     * Catat aktivitas (di luar database transaction).
     *
     * @param string      $action        Jenis tindakan: create, update, delete, login, logout, dll
     * @param string      $module        Modul: product, bahan_baku, produksi, penjualan, order, promo, user, auth
     * @param string      $description   Deskripsi yang mudah dipahami
     * @param array|null  $oldValues     Data sebelum perubahan (nullable)
     * @param array|null  $newValues     Data sesudah perubahan (nullable)
     * @param string|null $referenceType Jenis entitas terkait, misal 'product'
     * @param int|null    $referenceId   ID entitas terkait
     * @return bool
     */
    public function log(
        string  $action,
        string  $module,
        string  $description,
        ?array  $oldValues    = null,
        ?array  $newValues    = null,
        ?string $referenceType = null,
        ?int    $referenceId   = null
    ): bool {
        try {
            $model = new ActivityLogModel();
            $data  = $this->buildLogData(
                $action, $module, $description,
                $oldValues, $newValues,
                $referenceType, $referenceId
            );

            return $model->addLog($data);
        } catch (\Throwable $e) {
            // Gagal mencatat log tidak boleh menghentikan proses bisnis
            $this->logError('[ActivityLogger] Gagal mencatat log: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Catat aktivitas dalam konteks database transaction yang sudah berjalan.
     * Log ditulis ke koneksi DB yang sama, sehingga jika transaksi rollback,
     * log juga ikut rollback.
     *
     * @param \CodeIgniter\Database\BaseConnection $db  Koneksi DB yang sedang digunakan
     * @param string      $action
     * @param string      $module
     * @param string      $description
     * @param array|null  $oldValues
     * @param array|null  $newValues
     * @param string|null $referenceType
     * @param int|null    $referenceId
     * @return bool
     */
    public function logInTransaction(
        $db,
        string  $action,
        string  $module,
        string  $description,
        ?array  $oldValues    = null,
        ?array  $newValues    = null,
        ?string $referenceType = null,
        ?int    $referenceId   = null
    ): bool {
        try {
            $data = $this->buildLogData(
                $action, $module, $description,
                $oldValues, $newValues,
                $referenceType, $referenceId
            );

            return $db->table('activity_logs')->insert($data);
        } catch (\Throwable $e) {
            $this->logError('[ActivityLogger] Gagal mencatat log dalam transaksi: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buat array data log yang siap disimpan ke database.
     */
    protected function buildLogData(
        string  $action,
        string  $module,
        string  $description,
        ?array  $oldValues,
        ?array  $newValues,
        ?string $referenceType,
        ?int    $referenceId
    ): array {
        // Ambil identitas pengguna dari session/IonAuth secara aman
        [$userId, $username, $role] = $this->resolveIdentity();

        return [
            'user_id'        => $userId,
            'username'       => $username,
            'role'           => $role,
            'action'         => $action,
            'module'         => $module,
            'description'    => $description,
            'old_values'     => $this->encodeValues($oldValues),
            'new_values'     => $this->encodeValues($newValues),
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'ip_address'     => $this->getIpAddress(),
            'user_agent'     => $this->getUserAgent(),
            'created_at'     => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Resolve identitas pengguna yang sedang login.
     * Mengembalikan [user_id, username, role].
     * Aman dipanggil dari web request, CLI, maupun webhook.
     */
    protected function resolveIdentity(): array
    {
        $userId = null;

        try {
            if (function_exists('session')) {
                $session = session();
                if ($session && $session->has('user_id')) {
                    $userId = $session->get('user_id');
                }
            }
        } catch (\Throwable $e) {
            $userId = null;
        }

        if (empty($userId)) {
            if (PHP_SAPI === 'cli') {
                return [null, 'cli_system', 'system'];
            }
            return [null, 'guest', null];
        }

        try {
            $db       = \Config\Database::connect();
            $userRow  = $db->table('users')->where('id', $userId)->get()->getRow();
            $username = $userRow ? ($userRow->username ?? $userRow->email ?? 'unknown') : 'unknown';

            // Ambil group/role pengguna
            $groupRow = $db->table('users_groups ug')
                ->select('g.name')
                ->join('groups g', 'g.id = ug.group_id')
                ->where('ug.user_id', $userId)
                ->limit(1)
                ->get()
                ->getRow();

            $role = $groupRow ? $groupRow->name : null;

            return [(int) $userId, $username, $role];
        } catch (\Throwable $e) {
            $this->logError('[ActivityLogger] Gagal resolve identity: ' . $e->getMessage());
            return [(int) $userId, 'unknown', null];
        }
    }

    /**
     * Filter field sensitif dan encode ke JSON.
     */
    protected function encodeValues(?array $values): ?string
    {
        if ($values === null) {
            return null;
        }

        // Hapus field sensitif sebelum encoding
        $filtered = $this->filterSensitiveFields($values);

        if (empty($filtered)) {
            return null;
        }

        $encoded = json_encode($filtered, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

        return ($encoded === false) ? null : $encoded;
    }

    /**
     * Rekursif menghapus field sensitif dari array data.
     */
    protected function filterSensitiveFields(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            // Lewati field sensitif
            if (in_array(strtolower((string) $key), $this->sensitiveFields, true)) {
                continue;
            }

            if (is_array($value)) {
                $result[$key] = $this->filterSensitiveFields($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Ambil IP address dari request.
     */
    protected function getIpAddress(): ?string
    {
        if (PHP_SAPI === 'cli') {
            return '127.0.0.1';
        }

        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return null;
    }

    /**
     * Ambil User Agent dari request.
     * Dibatasi 500 karakter untuk mencegah data berlebihan.
     */
    protected function getUserAgent(): ?string
    {
        if (PHP_SAPI === 'cli') {
            return 'CLI-Environment';
        }

        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        return $ua ? substr($ua, 0, 500) : null;
    }

    /**
     * Helper log error yang aman jika environment belum fully booted.
     */
    protected function logError(string $message): void
    {
        if (defined('CI_DEBUG') && function_exists('log_message')) {
            log_message('error', $message);
        } else {
            error_log($message);
        }
    }
}
