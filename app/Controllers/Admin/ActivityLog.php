<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

/**
 * Controller untuk halaman Audit Trail / Activity Log.
 * Hanya dapat diakses oleh role: admin dan pemilik.
 */
class ActivityLog extends BaseController
{
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->logModel = new ActivityLogModel();
    }

    /**
     * Verifikasi bahwa pengguna adalah admin atau pemilik.
     * Jika tidak, redirect dengan pesan error.
     */
    private function ensureAdminOrPemilik(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->auth->inGroup(['admin', 'pemilik'])) {
            session()->setFlashdata('errors', 'Akses Ditolak! Halaman ini hanya untuk Admin dan Pemilik.');
            return redirect()->to('admin/dashboard');
        }

        return null;
    }

    /**
     * Halaman daftar audit trail dengan filter dan pagination.
     */
    public function index()
    {
        if ($redirect = $this->ensureAdminOrPemilik()) {
            return $redirect;
        }

        $perPage  = (int) ($this->request->getGet('perPage') ?? 20);
        if (!in_array($perPage, [10, 20, 25, 50], true)) {
            $perPage = 20;
        }

        // Kumpulkan filter dari query string
        $filters = [
            'user_id'   => $this->request->getGet('user_id'),
            'module'    => $this->request->getGet('module'),
            'action'    => $this->request->getGet('action'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'search'    => $this->request->getGet('search'),
        ];

        // Ambil builder terfilter
        $builder = $this->logModel->getLogsFiltered($filters);

        // Hitung total untuk pagination manual (karena kita pakai raw builder)
        $totalBuilder = clone $builder;
        $totalRows    = $totalBuilder->countAllResults(false);

        $page       = (int) ($this->request->getGet('page') ?? 1);
        $offset     = ($page - 1) * $perPage;
        $logs       = $builder->limit($perPage, $offset)->get()->getResult();
        $totalPages = (int) ceil($totalRows / $perPage);

        // Data untuk dropdown filter
        $modules = $this->logModel->getDistinctModules();
        $actions = $this->logModel->getDistinctActions();

        // Daftar user internal untuk dropdown filter
        $db           = \Config\Database::connect();
        $internalGroups = ['admin', 'gudang', 'penjualan', 'kurir', 'produksi', 'pemilik'];
        $users        = $db->table('users u')
            ->select('u.id, u.username, u.first_name, u.last_name')
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->join('groups g', 'g.id = ug.group_id')
            ->whereIn('g.name', $internalGroups)
            ->groupBy('u.id')
            ->orderBy('u.username', 'ASC')
            ->get()
            ->getResult();

        return view('admin/activity_log/index', [
            'title'               => 'Audit Trail / Log Aktivitas',
            'currentAdminMenu'    => 'activity_log',
            'currentAdminSubMenu' => 'activity_log',
            'logs'                => $logs,
            'filters'             => $filters,
            'modules'             => $modules,
            'actions'             => $actions,
            'users'               => $users,
            'perPage'             => $perPage,
            'totalRows'           => $totalRows,
            'totalPages'          => $totalPages,
            'currentPage'         => $page,
        ]);
    }

    /**
     * Tampilkan detail satu log aktivitas.
     */
    public function detail(int $id)
    {
        if ($redirect = $this->ensureAdminOrPemilik()) {
            return $redirect;
        }

        $log = $this->logModel->getLogById($id);

        if (! $log) {
            return redirect()->to('admin/activity-log')
                ->with('error', 'Log tidak ditemukan.');
        }

        // Decode JSON untuk tampilan detail
        $oldValues = null;
        $newValues = null;

        if (! empty($log->old_values)) {
            $oldValues = json_decode($log->old_values, true);
        }
        if (! empty($log->new_values)) {
            $newValues = json_decode($log->new_values, true);
        }

        return view('admin/activity_log/detail', [
            'title'               => 'Detail Log Aktivitas #' . $id,
            'currentAdminMenu'    => 'activity_log',
            'currentAdminSubMenu' => 'activity_log',
            'log'                 => $log,
            'oldValues'           => $oldValues,
            'newValues'           => $newValues,
        ]);
    }
}
