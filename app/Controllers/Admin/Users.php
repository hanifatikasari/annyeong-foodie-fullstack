<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\IncomingRequest;

class Users extends BaseController
{
    protected $internalGroups = ['admin', 'gudang', 'penjualan', 'kurir', 'produksi', 'pemilik'];

    public function __construct()
    {
        helper(['form', 'url']);
    }

    private function internalUserQuery()
    {
        return $this->db->table('users u')
            ->select('u.*')
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->join('groups g', 'g.id = ug.group_id')
            ->whereIn('g.name', $this->internalGroups)
            ->groupBy('u.id');
    }

    private function getInternalRoles()
    {
        return $this->auth->groups()->getResult();
    }

    private function ensurePemilik()
    {
        if (! $this->auth->inGroup('pemilik')) {
            return redirect()->to('admin/users')->with('error', 'Hanya pemilik yang boleh mengakses fitur ini.');
        }
    }

    private function ensureAdminOrPemilik()
    {
        if (! $this->auth->inGroup(['admin', 'pemilik'])) {
            return redirect()->to('admin/users')->with('error', 'Hanya admin atau pemilik yang boleh mengakses fitur ini.');
        }
    }

    public function index()
    {
        $users = $this->internalUserQuery()
            ->orderBy('u.id', 'ASC')
            ->get()
            ->getResult();

        foreach ($users as $k => $user) {
            $users[$k]->groups = $this->auth->getUsersGroups($user->id)->getResult();
        }

        $data = [
            'title'               => 'Kelola User',
            'currentAdminMenu'    => 'data_management',
            'currentAdminSubMenu' => 'user_management',
            'users'               => $users,
        ];

        return view('admin/users/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        $roles = $this->db->table('groups')
            ->whereIn('name', $this->internalGroups)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();

        return view('admin/users/form', [
            'title'    => 'Tambah Pegawai',
            'currentAdminMenu'    => 'data_management',
            'currentAdminSubMenu' => 'user_management',
            'user'     => null,
            'roles'    => $roles,
            'userGroup'=> null,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        $rules = [
            'username' => 'required|alpha_numeric|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'first_name' => 'required',
            'group'    => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            $roles = $this->db->table('groups')
                ->whereIn('name', $this->internalGroups)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResult();

            return view('admin/users/form', [
                'title'     => 'Tambah Pegawai',
                'currentAdminMenu'    => 'data_management',
                'currentAdminSubMenu' => 'user_management',
                'user'      => null,
                'roles'     => $roles,
                'userGroup' => $this->request->getPost('group'),
            ]);
        }

        $email    = strtolower($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $identityColumn = config('IonAuth')->identity;
        $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('username');

        $additionalData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'phone'      => $this->request->getPost('phone'),
        ];

        $group = [$this->request->getPost('group')];
        $created = $this->auth->register($identity, $password, $email, $additionalData, $group);

        if ($created) {
            return redirect()->to('admin/users')->with('success', 'Pegawai berhasil ditambahkan.');
        }

        return redirect()->back()->withInput()->with('error', $this->auth->errors());
    }

    public function edit(int $id)
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        $user = $this->internalUserQuery()
            ->where('u.id', $id)
            ->get()
            ->getRow();

        if (! $user) {
            return redirect()->to('admin/users')->with('error', 'User tidak ditemukan.');
        }

        $groups = $this->db->table('groups')
            ->whereIn('name', $this->internalGroups)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();

        $userGroups = $this->auth->getUsersGroups($id)->getResult();
        $userGroup = ! empty($userGroups) ? $userGroups[0]->id : null;

        return view('admin/users/form', [
            'title'     => 'Edit Pegawai',
            'currentAdminMenu'    => 'data_management',
            'currentAdminSubMenu' => 'user_management',
            'user'      => $user,
            'roles'     => $groups,
            'userGroup' => $userGroup,
        ]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        $user = $this->internalUserQuery()
            ->where('u.id', $id)
            ->get()
            ->getRow();

        if (! $user) {
            return redirect()->to('admin/users')->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'username' => 'required|alpha_numeric|min_length[3]|is_unique[users.username,id,' . $id . ']',
            'email'    => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            'first_name' => 'required',
            'group'    => 'required',
        ];

        if (! $this->validate($rules)) {
            $groups = $this->db->table('groups')
                ->whereIn('name', $this->internalGroups)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResult();

            return view('admin/users/form', [
                'title'     => 'Edit Pegawai',
                'currentAdminMenu'    => 'data_management',
                'currentAdminSubMenu' => 'user_management',
                'user'      => $user,
                'roles'     => $groups,
                'userGroup' => $this->request->getPost('group'),
            ]);
        }

        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => strtolower($this->request->getPost('email')),
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'phone'      => $this->request->getPost('phone'),
        ];

        $this->auth->update($id, $data);

        $this->auth->removeFromGroup('', $id);
        $this->auth->addToGroup($this->request->getPost('group'), $id);

        return redirect()->to('admin/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        if ($this->auth->deleteUser($id)) {
            return redirect()->to('admin/users')->with('success', 'User berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus user.');
    }

    public function activate(int $id)
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        if ($this->auth->activate($id)) {
            return redirect()->to('admin/users')->with('success', 'User berhasil diaktifkan.');
        }

        return redirect()->back()->with('error', 'Gagal mengaktifkan user.');
    }

    public function deactivate(int $id)
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        if ($this->auth->deactivate($id)) {
            return redirect()->to('admin/users')->with('success', 'User berhasil dinonaktifkan.');
        }

        return redirect()->back()->with('error', 'Gagal menonaktifkan user.');
    }

    public function resetPassword(int $id)
    {
        if ($redirect = $this->ensureAdminOrPemilik()) {
            return $redirect;
        }

        $user = $this->internalUserQuery()
            ->where('u.id', $id)
            ->get()
            ->getRow();

        if (! $user) {
            return redirect()->to('admin/users')->with('error', 'User tidak ditemukan.');
        }

        $password = substr(bin2hex(random_bytes(4)), 0, 8);
        $this->auth->update($id, ['password' => $password]);

        return redirect()->to('admin/users')->with('success', 'Password user berhasil direset. Password baru: ' . $password);
    }

    public function resetAllPasswords()
    {
        if ($redirect = $this->ensurePemilik()) {
            return $redirect;
        }

        $defaultPassword = 'Reset123!';
        $users = $this->internalUserQuery()->get()->getResult();

        foreach ($users as $user) {
            $this->auth->update($user->id, ['password' => $defaultPassword]);
        }

        return redirect()->to('admin/users')->with('success', 'Password semua user internal telah direset ke kata sandi default: ' . $defaultPassword);
    }

    public function activity()
    {
        if ($redirect = $this->ensureAdminOrPemilik()) {
            return $redirect;
        }

        $activities = $this->db->table('login_attempts')
            ->select('login_attempts.*')
            ->orderBy('time', 'DESC')
            ->limit(200)
            ->get()
            ->getResult();

        return view('admin/users/activity', [
            'title'               => 'Aktivitas User',
            'currentAdminMenu'    => 'data_management',
            'currentAdminSubMenu' => 'user_management',
            'activities'         => $activities,
        ]);
    }
}
