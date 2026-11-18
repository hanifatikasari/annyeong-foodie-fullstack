<?php namespace App\Controllers;

use App\Libraries\ActivityLogger;

class Auth extends \IonAuth\Controllers\Auth
{
     protected $auth;
    // Pastikan mengarah ke folder Views/auth di proyek E-Bisnis
    protected $viewsFolder = 'auth'; 

    public function login()
    {
        $this->data['title'] = "Login - Annyeong Foodie";
        $this->data['message'] = $this->session->getFlashdata('message');

        // Setup variabel untuk form_input() agar sesuai dengan tampilan E-Bisnis
        $this->data['identity'] = [
            'name'  => 'identity',
            'id'    => 'identity',
            'type'  => 'text',
            'class' => 'form-control', // Sesuai Bootstrap E-Bisnis
            'value' => set_value('identity'),
        ];
        $this->data['password'] = [
            'name'  => 'password',
            'id'    => 'password',
            'type'  => 'password',
            'class' => 'form-control',
        ];

        if (!$this->request->is('post')) {
            return view('auth/login', $this->data);
        }

        // Logika login sama, tapi redirect-nya sesuaikan:
        $identity = $this->request->getPost('identity');
        $password = $this->request->getPost('password');
        $remember = (bool)$this->request->getPost('remember');

        if ($this->auth->login($identity, $password, $remember)) {
            // Gabungkan keranjang guest dengan keranjang akun.
            helper('cart_helper');
            merge_guest_cart_into_user_cart();

            // Catat aktivitas login berhasil ke audit trail
            $logger = new ActivityLogger();
            $logger->log('login', 'auth', "Pengguna '{$identity}' berhasil login.");

            // JIKA ADMIN: Masuk ke Dashboard Admin 
            if ($this->auth->isAdmin()) {
                return redirect()->to('/')->with('message', 'Login Success!');
            }
            // JIKA PELANGGAN: Masuk ke Home page
            return redirect()->to('/')->with('message', 'Login Success!');
        } else {
            return redirect()->back()->withInput()->with('message', $this->auth->errors());
        }
    }

    /**
     * Override logout untuk mencatat aktivitas sebelum session dibersihkan.
     */
    public function logout()
    {
        // Catat logout SEBELUM IonAuth menghapus session
        if ($this->auth->loggedIn()) {
            $logger = new ActivityLogger();
            $userRow = $this->auth->user()->row();
            $username = $userRow ? ($userRow->username ?? $userRow->email ?? 'unknown') : 'unknown';
            $logger->log('logout', 'auth', "Pengguna '{$username}' logout.");
        }

        // Panggil logout IonAuth parent
        return parent::logout();
    }

    public function forgot_password() 
    {
        // Jika form dikirim, serahkan proses ke controller IonAuth.
        if ($this->request->is('post')) {
            return parent::forgot_password();
        }

        $this->data['title'] = "Forgot Password - Annyeong Foodie";
        
        // Setting validasi (bisa disesuaikan dengan config IonAuth)
        $this->data['type'] = config('IonAuth')->identity;
        $this->data['identity_label'] = 'Email/Username';
        
        $this->data['identity'] = [
            'name'  => 'identity',
            'id'    => 'identity',
            'class' => 'form-control',
            'placeholder' => 'Masukkan Email/Username anda'
        ];

        $this->data['message'] = $this->session->getFlashdata('message');

        // Panggil view secara manual agar path-nya benar (pakai slash /)
        return view('auth/forgot_password', $this->data);
    }

}