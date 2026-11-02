<?php

namespace App\Libraries\GoogleOAuth;

use Config\Google as GoogleConfig;
use Config\Services;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Throwable;

/**
 * Klien OAuth 2.0 / OpenID Connect untuk Google (Authorization Code Flow + PKCE).
 *
 * Tanggung jawab kelas ini HANYA protokol OAuth:
 *  - membuat URL otorisasi (state, nonce, PKCE disimpan di session)
 *  - memvalidasi callback (state, error, code)
 *  - menukar code -> token (server-to-server, memakai Client Secret)
 *  - memverifikasi ID token (tanda tangan, iss, aud, exp, nonce, email_verified)
 *
 * Kelas ini TIDAK menyentuh database maupun IonAuth.
 */
class OAuthClient
{
    private const AUTH_ENDPOINT  = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    private const JWKS_ENDPOINT  = 'https://www.googleapis.com/oauth2/v3/certs';
    private const ISSUERS        = ['https://accounts.google.com', 'accounts.google.com'];

    private const FLOW_SESSION_KEY = 'google_oauth_flow';
    private const FLOW_TTL         = 600;   // detik: batas waktu user menyelesaikan login di Google
    private const JWKS_CACHE_KEY   = 'google_oauth_jwks';
    private const JWKS_CACHE_TTL   = 3600;  // detik
    private const CLOCK_SKEW       = 60;    // detik toleransi selisih jam server

    private GoogleConfig $config;

    public function __construct(?GoogleConfig $config = null)
    {
        $this->config = $config ?? config('Google');
    }

    /**
     * Membuat URL otorisasi Google dan menyimpan state/nonce/PKCE di session.
     */
    public function buildAuthorizationUrl(): string
    {
        $this->assertConfigured();

        $state     = bin2hex(random_bytes(32));
        $nonce     = bin2hex(random_bytes(32));
        $verifier  = $this->base64Url(random_bytes(64));
        $challenge = $this->base64Url(hash('sha256', $verifier, true));

        session()->set(self::FLOW_SESSION_KEY, [
            'state'      => $state,
            'nonce'      => $nonce,
            'verifier'   => $verifier,
            'created_at' => time(),
        ]);

        return self::AUTH_ENDPOINT . '?' . http_build_query([
            'client_id'             => $this->config->clientId,
            'redirect_uri'          => $this->redirectUri(),
            'response_type'         => 'code',
            'scope'                 => 'openid email profile',
            'state'                 => $state,
            'nonce'                 => $nonce,
            'code_challenge'        => $challenge,
            'code_challenge_method' => 'S256',
            'prompt'                => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Memproses callback dari Google.
     *
     * @param mixed $state  nilai ?state= dari query string
     * @param mixed $code   nilai ?code= dari query string
     * @param mixed $error  nilai ?error= dari query string
     *
     * @return array{sub:string,email:string,name:string,given_name:string,family_name:string}
     *
     * @throws GoogleAuthException
     */
    public function handleCallback($state, $code, $error): array
    {
        $this->assertConfigured();

        // Flow bersifat sekali pakai: diambil lalu langsung dihapus dari session.
        $flow = $this->pullFlow();
        if ($flow === null) {
            throw new GoogleAuthException('Sesi login Google tidak valid atau sudah kedaluwarsa. Silakan coba lagi.');
        }

        // Validasi state (perlindungan CSRF pada callback).
        if (! is_string($state) || $state === '' || ! hash_equals($flow['state'], $state)) {
            log_message('warning', 'Google OAuth: parameter state tidak cocok.');
            throw new GoogleAuthException('Permintaan login Google tidak valid. Silakan coba lagi.');
        }

        // Pengguna menolak izin / Google mengembalikan error.
        if (is_string($error) && $error !== '') {
            $safeError = preg_replace('/[^a-z_]/i', '', substr($error, 0, 64));
            log_message('info', 'Google OAuth: callback berisi error "' . $safeError . '".');
            throw new GoogleAuthException('Login dengan Google dibatalkan atau ditolak.');
        }

        if (! is_string($code) || $code === '' || strlen($code) > 2048) {
            throw new GoogleAuthException('Permintaan login Google tidak valid. Silakan coba lagi.');
        }

        $idToken = $this->exchangeCode($code, $flow['verifier']);

        return $this->verifyIdToken($idToken, $flow['nonce']);
    }

    /**
     * Menghapus data flow OAuth dari session (mis. saat user sudah login).
     */
    public function forget(): void
    {
        session()->remove(self::FLOW_SESSION_KEY);
    }

    // ------------------------------------------------------------------
    // Internal
    // ------------------------------------------------------------------

    private function assertConfigured(): void
    {
        if ($this->config->clientId === '' || $this->config->clientSecret === '') {
            log_message('error', 'Google OAuth: GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET belum diisi di .env.');
            throw new GoogleAuthException('Login dengan Google belum tersedia. Silakan gunakan email dan password.');
        }
    }

    private function redirectUri(): string
    {
        if ($this->config->redirectUri !== '') {
            return $this->config->redirectUri;
        }

        helper('url');

        return base_url('auth/google/callback');
    }

    /**
     * @return array{state:string,nonce:string,verifier:string,created_at:int}|null
     */
    private function pullFlow(): ?array
    {
        $session = session();
        $flow    = $session->get(self::FLOW_SESSION_KEY);
        $session->remove(self::FLOW_SESSION_KEY);

        if (! is_array($flow)
            || ! isset($flow['state'], $flow['nonce'], $flow['verifier'], $flow['created_at'])
            || ! is_string($flow['state']) || ! is_string($flow['nonce']) || ! is_string($flow['verifier'])
        ) {
            return null;
        }

        if (time() - (int) $flow['created_at'] > self::FLOW_TTL) {
            return null;
        }

        return $flow;
    }

    /**
     * Menukar authorization code dengan token (server-to-server).
     * Mengembalikan id_token. Access token tidak dipakai dan tidak disimpan.
     */
    private function exchangeCode(string $code, string $verifier): string
    {
        try {
            $http = Services::curlrequest([
                'timeout'         => 10,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ], null, null, false);

            $response = $http->post(self::TOKEN_ENDPOINT, [
                'headers'     => ['Accept' => 'application/json'],
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => $this->config->clientId,
                    'client_secret' => $this->config->clientSecret,
                    'redirect_uri'  => $this->redirectUri(),
                    'grant_type'    => 'authorization_code',
                    'code_verifier' => $verifier,
                ],
            ]);
        } catch (Throwable $e) {
            // Pesan cURL tidak memuat credential. Contoh umum di Windows: "SSL certificate problem".
            log_message('error', 'Google OAuth: koneksi ke token endpoint gagal: ' . $e->getMessage());
            throw new GoogleAuthException('Tidak dapat terhubung ke Google. Silakan coba lagi beberapa saat lagi.');
        }

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200 || ! is_array($body)) {
            $googleError = is_array($body) && isset($body['error']) && is_string($body['error'])
                ? preg_replace('/[^a-z_]/i', '', substr($body['error'], 0, 64))
                : 'unknown';
            log_message('error', 'Google OAuth: penukaran code gagal (HTTP ' . $response->getStatusCode() . ', error=' . $googleError . ').');
            throw new GoogleAuthException('Login Google gagal diproses. Silakan coba lagi.');
        }

        if (empty($body['id_token']) || ! is_string($body['id_token'])) {
            log_message('error', 'Google OAuth: respons token tidak memuat id_token.');
            throw new GoogleAuthException('Login Google gagal diproses. Silakan coba lagi.');
        }

        return $body['id_token'];
    }

    /**
     * Verifikasi ID token: tanda tangan (JWKS Google), exp, iss, aud/azp, nonce,
     * sub, dan email_verified. Mengembalikan klaim yang dibutuhkan saja.
     *
     * @return array{sub:string,email:string,name:string,given_name:string,family_name:string}
     */
    private function verifyIdToken(string $idToken, string $expectedNonce): array
    {
        try {
            $keys = $this->getKeys();
            $kid  = $this->readKid($idToken);

            // Google merotasi kunci secara berkala: kid tidak dikenal -> ambil ulang JWKS sekali.
            if ($kid === null || ! isset($keys[$kid])) {
                $keys = $this->getKeys(true);
            }

            $previousLeeway = JWT::$leeway;
            JWT::$leeway    = self::CLOCK_SKEW;

            try {
                $decoded = JWT::decode($idToken, $keys); // memverifikasi signature, exp, nbf, iat
            } finally {
                JWT::$leeway = $previousLeeway;
            }

            $claims = json_decode((string) json_encode($decoded), true);
        } catch (Throwable $e) {
            log_message('error', 'Google OAuth: verifikasi ID token gagal (' . 
            get_class($e) . '): ' .
    $e->getMessage() .
    ' | PHP time: ' . gmdate('Y-m-d H:i:s') .
    ' | Unix time: ' . time());
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        if (! is_array($claims)) {
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        // iss
        if (! isset($claims['iss']) || ! in_array($claims['iss'], self::ISSUERS, true)) {
            log_message('error', 'Google OAuth: issuer ID token tidak valid.');
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }

        // aud (+ azp)
        $audList = is_array($claims['aud'] ?? null) ? $claims['aud'] : [$claims['aud'] ?? null];
        if (! in_array($this->config->clientId, $audList, true)) {
            log_message('error', 'Google OAuth: audience ID token tidak cocok dengan Client ID.');
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }
        if (isset($claims['azp']) && $claims['azp'] !== $this->config->clientId) {
            log_message('error', 'Google OAuth: azp ID token tidak cocok dengan Client ID.');
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }
        if (count($audList) > 1 && ! isset($claims['azp'])) {
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }

        // nonce (mencegah replay ID token)
        if (! isset($claims['nonce']) || ! is_string($claims['nonce']) || ! hash_equals($expectedNonce, $claims['nonce'])) {
            log_message('error', 'Google OAuth: nonce ID token tidak cocok.');
            throw new GoogleAuthException('Identitas Google tidak valid. Silakan coba lagi.');
        }

        // sub = identitas permanen akun Google
        if (! isset($claims['sub']) || ! is_string($claims['sub']) || ! preg_match('/^[\x21-\x7E]{1,255}$/', $claims['sub'])) {
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }

        // email wajib ada, valid, dan sudah diverifikasi Google
        $email = isset($claims['email']) && is_string($claims['email']) ? strtolower(trim($claims['email'])) : '';
        if ($email === '' || strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new GoogleAuthException('Akun Google Anda tidak menyediakan alamat email yang valid.');
        }
        $verified = $claims['email_verified'] ?? false;
        if ($verified !== true && $verified !== 'true') {
            throw new GoogleAuthException('Email pada akun Google Anda belum terverifikasi, sehingga tidak dapat dipakai untuk login.');
        }

        return [
            'sub'         => $claims['sub'],
            'email'       => $email,
            'name'        => isset($claims['name']) && is_string($claims['name']) ? $claims['name'] : '',
            'given_name'  => isset($claims['given_name']) && is_string($claims['given_name']) ? $claims['given_name'] : '',
            'family_name' => isset($claims['family_name']) && is_string($claims['family_name']) ? $claims['family_name'] : '',
        ];
    }

    /**
     * @return array<string, \Firebase\JWT\Key>
     */
    private function getKeys(bool $forceRefresh = false): array
    {
        $cache = Services::cache();
        $json  = $forceRefresh ? null : $cache->get(self::JWKS_CACHE_KEY);

        if (! is_string($json) || $json === '') {
            $json = $this->fetchJwks();

            try {
                $cache->save(self::JWKS_CACHE_KEY, $json, self::JWKS_CACHE_TTL);
            } catch (Throwable) {
                // Cache gagal bukan masalah fatal; JWKS diambil lagi pada request berikutnya.
            }
        }

        $set = json_decode($json, true);
        if (! is_array($set) || empty($set['keys'])) {
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        return JWK::parseKeySet($set, 'RS256');
    }

    private function fetchJwks(): string
    {
        $http     = Services::curlrequest(['timeout' => 10, 'connect_timeout' => 5, 'http_errors' => false], null, null, false);
        $response = $http->get(self::JWKS_ENDPOINT);

        if ($response->getStatusCode() !== 200) {
            log_message('error', 'Google OAuth: gagal mengambil JWKS (HTTP ' . $response->getStatusCode() . ').');
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        return (string) $response->getBody();
    }

    private function readKid(string $jwt): ?string
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }

        $header = json_decode(JWT::urlsafeB64Decode($parts[0]), true);

        return is_array($header) && isset($header['kid']) && is_string($header['kid']) ? $header['kid'] : null;
    }

    private function base64Url(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }
}