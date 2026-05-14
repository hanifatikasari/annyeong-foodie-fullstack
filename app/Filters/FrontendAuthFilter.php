<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * FrontendAuthFilter
 *
 * FIX ISSUE #5:
 * - Simpan URL yang dituju (intended URL) ke session sebelum redirect ke login
 * - Setelah login berhasil, Ion Auth akan redirect ke 'redirect' session key
 *   jika ada — atau Anda bisa handle di Auth controller
 */
class FrontendAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new \IonAuth\Libraries\IonAuth();

        if (!$auth->loggedIn()) {
            // Simpan URL yang sedang dituju ke session
            $currentUrl = current_url();
            session()->set('redirect_url', $currentUrl);
            session()->setFlashdata('message', 'Silakan login terlebih dahulu untuk melanjutkan.');

            return redirect()->to(site_url('auth/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan setelah request
    }
}