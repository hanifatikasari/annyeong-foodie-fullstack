<?php namespace App\Controllers;

class Auth extends \IonAuth\Controllers\Auth
{
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
            // JIKA ADMIN: Masuk ke Dashboard Admin di proyek INI (E-Bisnis)
            if ($this->auth->isAdmin()) {
                return redirect()->to('/')->with('message', 'Login Success!');
            } 
            // JIKA PELANGGAN: Masuk ke Home E-Bisnis
            return redirect()->to('/')->with('message', 'Login Success!');
        } else {
            return redirect()->back()->withInput()->with('message', $this->auth->errors());
        }
    }

    public function forgot_password()
    {
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