<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    // Log bersifat append-only: tidak ada field updated_at
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id',
        'username',
        'role',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'reference_type',
        'reference_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    // Tidak menyediakan method update/delete untuk menjaga integritas log
    // Log hanya bisa ditambah, tidak bisa diubah atau dihapus melalui UI

    /**
     * Simpan satu entri log. Dipanggil oleh ActivityLogService.
     */
    public function addLog(array $data): bool
    {
        // Pastikan created_at selalu ada
        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data, false) !== false;
    }

    /**
     * Ambil log dengan filter dan pagination.
     * Mengembalikan Query Builder agar controller bisa memanggil paginate().
     */
    public function getLogsFiltered(array $filters = [])
    {
        $builder = $this->db->table('activity_logs al')
            ->select('al.*')
            ->orderBy('al.id', 'DESC');

        // Filter: user_id
        if (!empty($filters['user_id'])) {
            $builder->where('al.user_id', (int) $filters['user_id']);
        }

        // Filter: username (search)
        if (!empty($filters['username'])) {
            $builder->like('al.username', $filters['username']);
        }

        // Filter: module
        if (!empty($filters['module'])) {
            $builder->where('al.module', $filters['module']);
        }

        // Filter: action
        if (!empty($filters['action'])) {
            $builder->where('al.action', $filters['action']);
        }

        // Filter: date range
        if (!empty($filters['date_from'])) {
            $builder->where('al.created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $builder->where('al.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        // Filter: search (username atau description)
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('al.username', $filters['search'])
                    ->orLike('al.description', $filters['search'])
                    ->groupEnd();
        }

        return $builder;
    }

    /**
     * Ambil satu log berdasarkan ID untuk tampilan detail.
     */
    public function getLogById(int $id): ?object
    {
        return $this->db->table('activity_logs')
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    /**
     * Ambil daftar modul unik untuk dropdown filter.
     */
    public function getDistinctModules(): array
    {
        $rows = $this->db->table('activity_logs')
            ->select('module')
            ->distinct()
            ->orderBy('module', 'ASC')
            ->get()
            ->getResult();

        return array_column($rows, 'module');
    }

    /**
     * Ambil daftar action unik untuk dropdown filter.
     */
    public function getDistinctActions(): array
    {
        $rows = $this->db->table('activity_logs')
            ->select('action')
            ->distinct()
            ->orderBy('action', 'ASC')
            ->get()
            ->getResult();

        return array_column($rows, 'action');
    }
}
