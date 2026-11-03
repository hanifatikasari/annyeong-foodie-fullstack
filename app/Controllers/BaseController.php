<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'auth', 'cart_helper'];
    protected $session;
    protected $auth; // Kita gunakan nama 'auth' agar seragam dengan template ebisnis
    protected $currentUser;
    protected $db;
    protected $data = [];
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		
        //  // --- COPY BAGIAN INI DARI TEMPLATE ---
        $this->session = \Config\Services::session();
        $this->data['session'] = $this->session;

        $this->auth = new \IonAuth\Libraries\IonAuth();
        $this->currentUser = null;

        // Periksa apakah session menyatakan pengguna sudah login.
        if ($this->auth->loggedIn()) {
            // Ambil ulang data user dari database.
            $userQuery = $this->auth->user();
            $this->currentUser = $userQuery ? $userQuery->row() : null;
            // Jika user tidak ditemukan, session sudah tidak valid.
            if ($this->currentUser === null) {
                // Logout melalui IonAuth agar session dibersihkan
                // menggunakan mekanisme bawaan library.
                $this->auth->logout();
                $this->session->setFlashdata(
                    'message',
                    'Sesi login berakhir karena akun tidak lagi tersedia. Silakan login kembali.'
                );
            }
        }

        $this->data['auth'] = $this->auth;
        $this->data['currentUser'] = $this->currentUser;

        $this->data['currentTheme'] = 'indomarket';
        $this->db = \Config\Database::connect();
    }
}
