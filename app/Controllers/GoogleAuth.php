<?php

namespace App\Controllers;

use App\Libraries\GoogleOAuth\AccountService;
use App\Libraries\GoogleOAuth\GoogleAuthException;
use App\Libraries\GoogleOAuth\OAuthClient;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

/**
 * Login dengan Google (OAuth 2.0 / OpenID Connect) di atas IonAuth.
 *
 * Endpoint:
 *   GET  auth/google            -> mulai alur, redirect ke Google
 *   GET  auth/google/callback   -> callback dari Google
 *   GET  auth/google/link       -> form konfirmasi password (email sudah terdaftar)
 *   POST auth/google/link       -> proses konfirmasi + penautan akun
 *   GET  auth/google/cancel     -> batalkan penautan
 *
 * Mewarisi BaseController agar memakai instance IonAuth ($this->auth) yang sama dengan
 * seluruh aplikasi. Controller Auth bawaan tidak diubah.
 */
class GoogleAuth extends BaseController
{
    private ?OAuthClient $oauthClient     = null;
    private ?AccountService $accountService = null;

    public function start(): RedirectResponse
    {
        if ($this->auth->loggedIn()) {
            return redirect()->to('/');
        }

        try {
            return redirect()->to($this->oauth()->buildAuthorizationUrl());
        } catch (GoogleAuthException $e) {
            return $this->failure($e->getMessage());
        }
    }

    public function callback(): RedirectResponse
    {
        if ($this->auth->loggedIn()) {
            $this->oauth()->forget();

            return redirect()->to('/');
        }

        try {
            $claims = $this->oauth()->handleCallback(
                $this->request->getGet('state'),
                $this->request->getGet('code'),
                $this->request->getGet('error')
            );

            $result = $this->accounts()->resolve($claims);

            switch ($result['action']) {
                case 'link':
                    // Email sudah terdaftar: jangan auto-link, minta konfirmasi password dulu.
                    $this->accounts()->storePendingLink($claims, $result['user']);

                    return redirect()->to('auth/google/link');

                case 'create':
                    $user = $this->accounts()->createCustomer($claims);
                    break;

                default: // 'login'
                    $user = $result['user'];
            }

            return $this->completeLogin($user);
        } catch (GoogleAuthException $e) {
            return $this->failure($e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'Google OAuth: error tak terduga di callback (' . get_class($e) . ').');

            return $this->failure('Login dengan Google gagal. Silakan coba lagi atau gunakan email dan password.');
        }
    }

    /**
     * @return RedirectResponse|string
     */
    public function linkForm()
    {
        $pending = $this->accounts()->getPendingLink();
        if ($pending === null) {
            return $this->failure('Sesi penautan akun Google sudah berakhir. Silakan klik "Continue with Google" lagi.');
        }

        return view('auth/google_link', [
            'email'   => $pending['email'],
            'message' => $this->session->getFlashdata('message'),
        ]);
    }

    public function linkSubmit(): RedirectResponse
    {
        $pending = $this->accounts()->getPendingLink();
        if ($pending === null) {
            return $this->failure('Sesi penautan akun Google sudah berakhir. Silakan klik "Continue with Google" lagi.');
        }

        $password = $this->request->getPost('password');
        if (! is_string($password) || $password === '') {
            return redirect()->to('auth/google/link')->with('message', 'Password wajib diisi.');
        }

        $existing = $this->accounts()->findById((int) $pending['user_id']);
        if ($existing === null || ! empty($existing->google_id) || ! empty($existing->deleted_at)) {
            $this->accounts()->clearPendingLink();

            return $this->failure('Akun tidak dapat dihubungkan dengan Google. Silakan login dengan email dan password.');
        }

        // Verifikasi password lewat IonAuth (hash check, status aktif, pembatasan percobaan login).
        $identityColumn = config('IonAuth')->identity;
        if (! $this->auth->login((string) $existing->{$identityColumn}, $password, false)) {
            $errors = $this->auth->errors();
            $errors = is_array($errors) ? implode(' ', $errors) : (string) $errors;
            $errors = trim(strip_tags($errors));

            return redirect()->to('auth/google/link')->with('message', $errors !== '' ? $errors : 'Password salah.');
        }

        // Pastikan session IonAuth memang milik akun yang akan ditautkan.
        if ((int) $this->session->get('user_id') !== (int) $existing->id) {
            log_message('error', 'Google OAuth: user_id session tidak cocok saat penautan akun.');
            $this->accounts()->clearPendingLink();

            return $this->failure('Penautan akun gagal. Silakan coba lagi.');
        }

        try {
            $this->accounts()->linkGoogleId((int) $existing->id, $pending['sub']);
        } catch (GoogleAuthException $e) {
            $this->accounts()->clearPendingLink();

            // Password benar sehingga user tetap login; hanya penautannya yang gagal.
            return redirect()->to('/')->with('message', 'Login Success! Namun akun Google belum dapat dihubungkan: ' . $e->getMessage());
        }

        $this->accounts()->clearPendingLink();
        // Gabungkan keranjang guest dengan keranjang akun.
        helper('cart_helper');
        merge_guest_cart_into_user_cart();
        
        return redirect()->to('/')->with('message', 'Login Success! Akun Google berhasil dihubungkan.');
    }

    public function cancel(): RedirectResponse
    {
        $this->accounts()->clearPendingLink();

        return redirect()->to('auth/login');
    }

    // ------------------------------------------------------------------

    private function completeLogin(object $user): RedirectResponse
    {
        $this->accounts()->startIonAuthSession($user);

        // Verifikasi bahwa IonAuth benar-benar mengenali session ini.
        if (! $this->auth->loggedIn()) {
            log_message('error', 'Google OAuth: session IonAuth tidak terbentuk. Periksa AccountService::startIonAuthSession().');
            $this->accounts()->clearIonAuthSession();

            return $this->failure('Login dengan Google gagal. Silakan coba lagi atau gunakan email dan password.');
        }
        // Gabungkan keranjang guest dengan keranjang akun.
        helper('cart_helper');
        merge_guest_cart_into_user_cart();

        return redirect()->to('/')->with('message', 'Login Success!');
    }

    private function failure(string $message): RedirectResponse
    {
        return redirect()->to('auth/login')->with('message', $message);
    }

    private function oauth(): OAuthClient
    {
        return $this->oauthClient ??= new OAuthClient();
    }

    private function accounts(): AccountService
    {
        return $this->accountService ??= new AccountService();
    }
}