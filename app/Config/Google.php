<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Konfigurasi Google OAuth 2.0 / OpenID Connect.
 *
 * Semua nilai dibaca dari environment (.env atau environment server),
 * tidak ada credential yang ditulis di source code.
 */
class Google extends BaseConfig
{
    /** OAuth Client ID (GOOGLE_CLIENT_ID) */
    public string $clientId = '';

    /** OAuth Client Secret (GOOGLE_CLIENT_SECRET) - RAHASIA */
    public string $clientSecret = '';

    /**
     * Redirect/callback URI (GOOGLE_REDIRECT_URI).
     * Harus PERSIS sama dengan yang didaftarkan di Google Cloud Console.
     * Jika kosong, dipakai base_url('auth/google/callback').
     */
    public string $redirectUri = '';

    public function __construct()
    {
        parent::__construct();

        $this->clientId     = trim((string) env('GOOGLE_CLIENT_ID', ''));
        $this->clientSecret = trim((string) env('GOOGLE_CLIENT_SECRET', ''));
        $this->redirectUri  = trim((string) env('GOOGLE_REDIRECT_URI', ''));
    }
}