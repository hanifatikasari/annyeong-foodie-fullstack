<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 't_absensi';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'status_masuk',
        'jam_masuk',
        'jam_keluar',
        'status_keluar',
        'total_jam',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'object';

    public function getTodayAttendance($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('created_at >=', date('Y-m-d 00:00:00'))
                    ->where('created_at <=', date('Y-m-d 23:59:59'))
                    ->first();
    }
    
    public function getRiwayatUser($userId)
    {
        return $this->select('t_absensi.*')
            ->where('user_id', $userId)
            ->orderBy('jam_masuk', 'DESC')
            ->findAll();
    }

    public function getRiwayatSemua()
    {
        return $this->db->table('t_absensi')
            ->select('
                t_absensi.*,
                users.username,
                users.first_name,
                users.last_name
            ')
            ->join('users', 'users.id = t_absensi.user_id')
            ->orderBy('jam_masuk', 'DESC')
            ->get()
            ->getResult();
    }

    public function getPegawaiList()
    {
        return $this->db->table('users u')
            ->select('u.id,u.first_name,u.last_name')
            ->join('users_groups ug', 'ug.user_id=u.id')
            ->join('groups g', 'g.id=ug.group_id')
            ->whereIn('g.name', [
                'admin',
                'pemilik',
                'penjualan',
                'produksi',
                'gudang'
            ])
            ->where('u.deleted_at', null)
            ->orderBy('u.first_name')
            ->get()
            ->getResult();
    }

    public function getRiwayatFiltered($bulan, $tahun, $pegawai = null)
    {
        $builder = $this->db->table('t_absensi a');

        $builder->select('
            a.*,
            u.first_name,
            u.last_name
        ');

        $builder->join('users u', 'u.id=a.user_id');

        $builder->where('MONTH(a.jam_masuk)', $bulan);
        $builder->where('YEAR(a.jam_masuk)', $tahun);

        if (!empty($pegawai)) {
            $builder->where('a.user_id', $pegawai);
        }

        return $builder
            ->orderBy('a.jam_masuk', 'DESC')
            ->get()
            ->getResult();
    }

    public function getRiwayatUserFiltered($userId, $bulan, $tahun)
    {
        return $this->db->table('t_absensi')
            ->where('user_id', $userId)
            ->where('MONTH(jam_masuk)', $bulan)
            ->where('YEAR(jam_masuk)', $tahun)
            ->orderBy('jam_masuk', 'DESC')
            ->get()
            ->getResult();
    }
}