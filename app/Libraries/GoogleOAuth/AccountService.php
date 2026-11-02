<?php

namespace App\Libraries\GoogleOAuth;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Throwable;

/**
 * Menghubungkan identitas Google dengan akun Batterbit (tabel IonAuth).
 *
 * Tanggung jawab:
 *  - mencari akun berdasarkan Google `sub` atau email
 *  - membuat akun pelanggan baru (group = IonAuth::$defaultGroup)
 *  - menyimpan/menghubungkan `users.google_id`
 *  - membentuk session yang dikenali IonAuth
 *
 * Nama tabel diambil dari Config\IonAuth::$tables, bukan di-hardcode.
 */
class AccountService
{
    private const PENDING_KEY = 'google_pending_link';
    private const PENDING_TTL = 600; // detik

    /** Key session yang dipakai IonAuth (lihat IonAuthModel::setSession / logout). */
    private const IONAUTH_SESSION_KEYS = ['identity', 'username', 'email', 'user_id', 'old_last_login', 'last_check'];

    private BaseConnection $db;
    private object $ionConfig;
    private string $usersTable;
    private string $groupsTable;
    private string $usersGroupsTable;

    public function __construct()
    {
        $this->ionConfig = config('IonAuth');

        $dbGroup  = ! empty($this->ionConfig->databaseGroupName) ? $this->ionConfig->databaseGroupName : null;
        $this->db = Database::connect($dbGroup);

        $tables                 = $this->ionConfig->tables;
        $this->usersTable       = $tables['users'];
        $this->groupsTable      = $tables['groups'];
        $this->usersGroupsTable = $tables['users_groups'];
    }

    // ------------------------------------------------------------------
    // Pencarian akun
    // ------------------------------------------------------------------

    /**
     * Menentukan apa yang harus dilakukan untuk identitas Google yang sudah terverifikasi.
     *
     * @param array{sub:string,email:string,name:string,given_name:string,family_name:string} $claims
     *
     * @return array{action:string,user:?object}  action: 'login' | 'link' | 'create'
     *
     * @throws GoogleAuthException
     */
    public function resolve(array $claims): array
    {
        // 1) Akun yang sudah terhubung dengan Google ini -> login langsung.
        $linked = $this->findByGoogleId($claims['sub']);
        if ($linked !== null) {
            $this->assertUsable($linked);

            return ['action' => 'login', 'user' => $linked];
        }

        // 2) Email sudah terdaftar tetapi belum terhubung -> WAJIB konfirmasi password (account linking aman).
        $existing = $this->findByEmail($claims['email']);
        if ($existing !== null) {
            if (! empty($existing->google_id)) {
                // Akun ini sudah dihubungkan dengan akun Google LAIN.
                throw new GoogleAuthException('Email ini sudah terdaftar dan terhubung dengan akun Google yang berbeda. Silakan login dengan email dan password.');
            }

            $this->assertUsable($existing);

            return ['action' => 'link', 'user' => $existing];
        }

        // 3) Belum ada akun sama sekali -> buat akun pelanggan baru.
        return ['action' => 'create', 'user' => null];
    }

    public function findById(int $id): ?object
    {
        return $this->db->table($this->usersTable)->where('id', $id)->get(1)->getRow();
    }

    private function findByGoogleId(string $sub): ?object
    {
        return $this->db->table($this->usersTable)->where('google_id', $sub)->get(1)->getRow();
    }

    private function findByEmail(string $email): ?object
    {
        return $this->db->table($this->usersTable)->where('email', $email)->get(1)->getRow();
    }

    /**
     * Akun harus tidak terhapus (soft delete) dan aktif.
     * Pengecekan `active` sama dengan IonAuth: nilai kosong/0 = belum aktif.
     */
    private function assertUsable(object $user): void
    {
        if (! empty($user->deleted_at)) {
            throw new GoogleAuthException('Akun tidak dapat digunakan. Silakan hubungi administrator.');
        }

        if (empty($user->active)) {
            throw new GoogleAuthException('Akun Anda belum aktif. Silakan hubungi administrator.');
        }
    }

    // ------------------------------------------------------------------
    // Pembuatan akun baru
    // ------------------------------------------------------------------

    /**
     * Membuat akun pelanggan baru dari identitas Google.
     * Password acak 256-bit yang tidak pernah disimpan/ditampilkan, di-hash bcrypt
     * (sama seperti seeder & konfigurasi IonAuth). Pengguna dapat membuat password
     * sendiri lewat fitur Forgot Password bila diperlukan.
     *
     * @param array{sub:string,email:string,name:string,given_name:string,family_name:string} $claims
     *
     * @throws GoogleAuthException
     */
    public function createCustomer(array $claims): object
    {
        $group = $this->db->table($this->groupsTable)
            ->where('name', $this->ionConfig->defaultGroup)
            ->get(1)
            ->getRow();

        if ($group === null) {
            log_message('error', 'Google OAuth: group default "' . $this->ionConfig->defaultGroup . '" tidak ditemukan di tabel groups.');
            throw new GoogleAuthException('Pendaftaran akun belum dapat diproses. Silakan hubungi administrator.');
        }

        [$firstName, $lastName] = $this->splitNames($claims);

        $randomPassword = bin2hex(random_bytes(32));
        $passwordHash   = password_hash($randomPassword, PASSWORD_BCRYPT, [
            'cost' => max(10, (int) ($this->ionConfig->bcryptDefaultCost ?? 10)),
        ]);

        $userData = [
            'ip_address' => service('request')->getIPAddress(),
            'username'   => $this->makeUsername($claims['email']),
            'password'   => $passwordHash,
            'email'      => $claims['email'],
            'active'     => date('Y-m-d H:i:s'), // kolom `active` bertipe DATETIME (lihat migration & seeder)
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'created_on' => time(),
            'google_id'  => $claims['sub'],
        ];

        $this->db->transBegin();

        try {
            if (! $this->db->table($this->usersTable)->insert($userData)) {
                throw new \RuntimeException('insert users gagal');
            }

            $userId = (int) $this->db->insertID();

            $groupInserted = $this->db->table($this->usersGroupsTable)->insert([
                'user_id'  => $userId,
                'group_id' => (int) $group->id,
            ]);
            if (! $groupInserted) {
                throw new \RuntimeException('insert users_groups gagal');
            }

            $this->db->transCommit();
        } catch (Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Google OAuth: gagal membuat akun (' . get_class($e) . ', code ' . $e->getCode() . ').');
            throw new GoogleAuthException('Akun belum dapat dibuat saat ini. Silakan coba lagi.');
        }

        $user = $this->findById($userId);
        if ($user === null) {
            throw new GoogleAuthException('Akun belum dapat dibuat saat ini. Silakan coba lagi.');
        }

        return $user;
    }

    // ------------------------------------------------------------------
    // Account linking (akun lama + konfirmasi password)
    // ------------------------------------------------------------------

    /**
     * Menyimpan permintaan penautan di session (sisi server) hingga user membuktikan
     * kepemilikan akun lama dengan password.
     *
     * @param array{sub:string,email:string,name:string,given_name:string,family_name:string} $claims
     */
    public function storePendingLink(array $claims, object $existingUser): void
    {
        session()->set(self::PENDING_KEY, [
            'sub'        => $claims['sub'],
            'email'      => $claims['email'],
            'user_id'    => (int) $existingUser->id,
            'expires_at' => time() + self::PENDING_TTL,
        ]);
    }

    /**
     * @return array{sub:string,email:string,user_id:int,expires_at:int}|null
     */
    public function getPendingLink(): ?array
    {
        $pending = session()->get(self::PENDING_KEY);

        if (! is_array($pending)
            || ! isset($pending['sub'], $pending['email'], $pending['user_id'], $pending['expires_at'])
            || (int) $pending['expires_at'] < time()
        ) {
            $this->clearPendingLink();

            return null;
        }

        return $pending;
    }

    public function clearPendingLink(): void
    {
        session()->remove(self::PENDING_KEY);
    }

    /**
     * Menyimpan google_id ke akun. Hanya berhasil jika akun belum terhubung ke Google mana pun.
     *
     * @throws GoogleAuthException
     */
    public function linkGoogleId(int $userId, string $sub): void
    {
        try {
            $this->db->table($this->usersTable)
                ->where('id', $userId)
                ->where('google_id IS NULL')
                ->update(['google_id' => $sub]);

            $affected = $this->db->affectedRows();
        } catch (Throwable $e) {
            log_message('error', 'Google OAuth: gagal menautkan google_id (' . get_class($e) . ', code ' . $e->getCode() . ').');
            throw new GoogleAuthException('Akun Google tidak dapat dihubungkan saat ini.');
        }

        if ($affected !== 1) {
            throw new GoogleAuthException('Akun Google tidak dapat dihubungkan. Akun ini mungkin sudah terhubung dengan akun Google lain.');
        }
    }

    // ------------------------------------------------------------------
    // Session IonAuth
    // ------------------------------------------------------------------

    /**
     * Membentuk session yang dikenali IonAuth (loggedIn(), user(), isAdmin(), logout()).
     * Key session sama dengan yang dibuat IonAuth saat login password.
     * Group/hak akses TIDAK diubah: IonAuth membacanya dari tabel users_groups.
     */
    public function startIonAuthSession(object $user): void
    {
        $session = session();
        $session->regenerate(true); // cegah session fixation

        $identityColumn = $this->ionConfig->identity; // 'email' pada konfigurasi saat ini

        $session->set([
            'identity'       => $user->{$identityColumn},
            'username'       => $user->username,
            'email'          => $user->email,
            'user_id'        => $user->id,
            'old_last_login' => $user->last_login ?? null,
            'last_check'     => time(),
        ]);

        $this->db->table($this->usersTable)
            ->where('id', (int) $user->id)
            ->update(['last_login' => time()]);
    }

    /**
     * Membatalkan session IonAuth yang baru saja dibuat (dipakai bila verifikasi loggedIn() gagal).
     */
    public function clearIonAuthSession(): void
    {
        session()->remove(self::IONAUTH_SESSION_KEYS);
    }

    // ------------------------------------------------------------------
    // Helper data
    // ------------------------------------------------------------------

    /**
     * @param array{given_name:string,family_name:string,name:string} $claims
     *
     * @return array{0:?string,1:?string}
     */
    private function splitNames(array $claims): array
    {
        $first = trim($claims['given_name'] ?? '');
        $last  = trim($claims['family_name'] ?? '');

        if ($first === '' && $last === '' && ! empty($claims['name'])) {
            $parts = preg_split('/\s+/u', trim($claims['name']), 2) ?: [];
            $first = $parts[0] ?? '';
            $last  = $parts[1] ?? '';
        }

        return [$this->cleanName($first), $this->cleanName($last)];
    }

    /** Nama dari Google = input pihak ketiga: buang tag/kontrol, batasi 30 karakter (sesuai kolom). */
    private function cleanName(string $value): ?string
    {
        $value = strip_tags($value);
        $value = preg_replace('/[\p{C}]+/u', '', $value) ?? '';
        $value = trim(mb_substr($value, 0, 30));

        return $value === '' ? null : $value;
    }

    private function makeUsername(string $email): string
    {
        $local = (string) strstr($email, '@', true);
        $local = preg_replace('/[^A-Za-z0-9._-]/', '', $local) ?? '';
        $local = substr($local, 0, 50);

        return $local !== '' ? $local : 'user';
    }
}