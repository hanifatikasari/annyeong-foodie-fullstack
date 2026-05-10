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

        $currentUser = $auth->user()->row();
        
        if (!empty($arguments)) {
            if (!$auth->inGroup($arguments, $currentUser->id)) {
                // Kasih pesan error yang jelas
                session()->setFlashdata("errors", "Akses Ditolak! Akun Anda tidak memiliki izin untuk halaman ini.");
                
                // Buang ke halaman utama
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