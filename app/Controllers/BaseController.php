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
    protected $helpers = ['url', 'form', 'auth'];
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

        // Catatan: Pastikan IonAuth sudah terinstall, jika belum, baris ini mungkin akan error nanti
        $this->auth = new \IonAuth\Libraries\IonAuth();
        $this->currentUser = $this->auth->user()->row();
    
        $this->data['auth'] = $this->auth;
        $this->data['currentUser'] = $this->currentUser;

        $this->data['currentTheme'] = 'indomarket';

        $this->db = \Config\Database::connect();

        $router = service('router');
        $controllerName = $router->controllerName();

        // Cek apakah user sedang mengakses Controller di dalam folder Admin
        if (strpos($controllerName, '\Admin\\') !== false) {
            if (!$this->auth->loggedIn()) {
                return header('Location: ' . base_url('auth/login'));
                exit();
            }
        }
        // E.g.: $this->session = service('session');
    }
}
