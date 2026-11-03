<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use IonAuth\Libraries\IonAuth;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new IonAuth();

        if (!$auth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        // Ambil user dari database.
        $userQuery = $auth->user();
        $currentUser = $userQuery ? $userQuery->row() : null;
            
        // Session ada tetapi user tidak ditemukan.
        if ($currentUser === null) {
            $auth->logout();

            return redirect()->to(site_url('auth/login'))
                ->with('message', 'Sesi login berakhir. Silakan login kembali.');
        }

        // Periksa hak akses berdasarkan group.
        if (!empty($arguments)) {
            if (!$auth->inGroup($arguments, $currentUser->id)) {
                session()->setFlashdata(
                    'errors',
                    'Akses Ditolak! Akun Anda tidak memiliki izin untuk halaman ini.'
                );
                return redirect()->to('/');
            }
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}