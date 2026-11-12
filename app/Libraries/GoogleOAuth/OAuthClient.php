<?php

namespace App\Libraries\GoogleOAuth;

use Config\Google as GoogleConfig;
use Config\Services;
use Throwable;

/**
 * Klien OAuth 2.0 / OpenID Connect untuk Google (Authorization Code Flow + PKCE).
 * Menggunakan verifikasi JWT RS256 native PHP (OpenSSL) tanpa dependensi eksternal.
 */
class OAuthClient
{
    private const AUTH_ENDPOINT  = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    private const JWKS_ENDPOINT  = 'https://www.googleapis.com/oauth2/v3/certs';
    private const ISSUERS        = ['https://accounts.google.com', 'accounts.google.com'];

    private const FLOW_SESSION_KEY = 'google_oauth_flow';
    private const FLOW_TTL         = 600;   // detik
    private const JWKS_CACHE_KEY   = 'google_oauth_jwks';
    private const JWKS_CACHE_TTL   = 3600;  // detik
    private const CLOCK_SKEW       = 60;    // detik toleransi selisih jam server

    private GoogleConfig $config;

    public function __construct(?GoogleConfig $config = null)
    {
        $this->config = $config ?? config('Google');
    }

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

    public function handleCallback($state, $code, $error): array
    {
        $this->assertConfigured();

        $flow = $this->pullFlow();
        if ($flow === null) {
            throw new GoogleAuthException('Sesi login Google tidak valid atau sudah kedaluwarsa. Silakan coba lagi.');
        }

        if (! is_string($state) || $state === '' || ! hash_equals($flow['state'], $state)) {
            log_message('warning', 'Google OAuth: parameter state tidak cocok.');
            throw new GoogleAuthException('Permintaan login Google tidak valid. Silakan coba lagi.');
        }

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

    private function verifyIdToken(string $idToken, string $expectedNonce): array
    {
        try {
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                throw new GoogleAuthException('Format ID token tidak valid.');
            }

            [$headerB64, $payloadB64, $sigB64] = $parts;

            $header  = json_decode($this->base64UrlDecode($headerB64), true);
            $claims  = json_decode($this->base64UrlDecode($payloadB64), true);
            $sig     = $this->base64UrlDecode($sigB64);

            if (! is_array($header) || ! is_array($claims)) {
                throw new GoogleAuthException('Payload ID token tidak dapat dibaca.');
            }

            if (($header['alg'] ?? '') !== 'RS256' || empty($header['kid'])) {
                throw new GoogleAuthException('Algoritma atau Key ID token tidak valid.');
            }

            $kid  = $header['kid'];
            $keys = $this->getKeys();

            if (! isset($keys[$kid])) {
                $keys = $this->getKeys(true);
            }

            if (! isset($keys[$kid])) {
                throw new GoogleAuthException('Public key Google tidak ditemukan untuk kid: ' . $kid);
            }

            $verified = openssl_verify(
                $headerB64 . '.' . $payloadB64,
                $sig,
                $keys[$kid],
                OPENSSL_ALGO_SHA256
            );

            if ($verified !== 1) {
                throw new GoogleAuthException('Signature ID token tidak valid.');
            }

            $now = time();
            if (isset($claims['exp']) && ($now - self::CLOCK_SKEW) >= (int) $claims['exp']) {
                throw new GoogleAuthException('ID token sudah kedaluwarsa.');
            }
            if (isset($claims['nbf']) && ($now + self::CLOCK_SKEW) < (int) $claims['nbf']) {
                throw new GoogleAuthException('ID token belum berlaku.');
            }
            if (isset($claims['iat']) && ($now + self::CLOCK_SKEW) < (int) $claims['iat']) {
                throw new GoogleAuthException('Waktu pembuatan ID token tidak valid.');
            }
        } catch (Throwable $e) {
            log_message('error', 'Google OAuth: verifikasi ID token gagal (' . 
                get_class($e) . '): ' . $e->getMessage() .
                ' | PHP time: ' . gmdate('Y-m-d H:i:s') .
                ' | Unix time: ' . time());
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        if (! isset($claims['iss']) || ! in_array($claims['iss'], self::ISSUERS, true)) {
            log_message('error', 'Google OAuth: issuer ID token tidak valid.');
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }

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

        if (! isset($claims['nonce']) || ! is_string($claims['nonce']) || ! hash_equals($expectedNonce, $claims['nonce'])) {
            log_message('error', 'Google OAuth: nonce ID token tidak cocok.');
            throw new GoogleAuthException('Identitas Google tidak valid. Silakan coba lagi.');
        }

        if (! isset($claims['sub']) || ! is_string($claims['sub']) || ! preg_match('/^[\x21-\x7E]{1,255}$/', $claims['sub'])) {
            throw new GoogleAuthException('Identitas Google tidak valid.');
        }

        $email = isset($claims['email']) && is_string($claims['email']) ? strtolower(trim($claims['email'])) : '';
        if ($email === '' || strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new GoogleAuthException('Akun Google Anda tidak menyediakan alamat email yang valid.');
        }
        $verifiedEmail = $claims['email_verified'] ?? false;
        if ($verifiedEmail !== true && $verifiedEmail !== 'true') {
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
     * @return array<string, string> Map kid => PEM public key
     */
    private function getKeys(bool $forceRefresh = false): array
    {
        $cache = Services::cache();
        $json  = $forceRefresh ? null : $cache->get(self::JWKS_CACHE_KEY);

        if (! is_string($json) || $json === '') {
            $json = $this->fetchJwks($forceRefresh);

            try {
                $cache->save(self::JWKS_CACHE_KEY, $json, self::JWKS_CACHE_TTL);
            } catch (Throwable) {
                // Cache error diabaikan
            }
        }

        $set = json_decode($json, true);
        if (! is_array($set) || empty($set['keys'])) {
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        $parsedKeys = [];
        foreach ($set['keys'] as $keyData) {
            if (empty($keyData['kid']) || empty($keyData['n']) || empty($keyData['e'])) {
                continue;
            }

            $pem = $this->convertJwksToPem($keyData['n'], $keyData['e']);
            if ($pem !== null) {
                $parsedKeys[$keyData['kid']] = $pem;
            }
        }

        if (empty($parsedKeys)) {
            throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
        }

        return $parsedKeys;
    }

    private function convertJwksToPem(string $n, string $e): ?string
    {
        $modulus  = $this->base64UrlDecode($n);
        $exponent = $this->base64UrlDecode($e);

        $components = [
            'modulus'        => pack('Ca*a*', 0x02, $this->encodeLength(strlen($modulus)), $modulus),
            'publicExponent' => pack('Ca*a*', 0x02, $this->encodeLength(strlen($exponent)), $exponent),
        ];

        $rsaSequence = pack(
            'Ca*a*a*',
            0x30,
            $this->encodeLength(strlen($components['modulus'] . $components['publicExponent'])),
            $components['modulus'],
            $components['publicExponent']
        );

        $rsaAlgorithmIdentifier = pack('H*', '300d06092a864886f70d0101010500');
        $bitString              = pack('Ca*a*', 0x03, $this->encodeLength(strlen($rsaSequence) + 1), "\x00" . $rsaSequence);

        $publicKeySequence = pack(
            'Ca*a*a*',
            0x30,
            $this->encodeLength(strlen($rsaAlgorithmIdentifier . $bitString)),
            $rsaAlgorithmIdentifier,
            $bitString
        );

        return "-----BEGIN PUBLIC KEY-----\n" .
               chunk_split(base64_encode($publicKeySequence), 64, "\n") .
               "-----END PUBLIC KEY-----";
    }

    private function encodeLength(int $length): string
    {
        if ($length <= 0x7F) {
            return pack('C', $length);
        }

        $temp = ltrim(pack('N', $length), "\x00");
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }

    private function fetchJwks(bool $forceRefresh = false): string
    {
        $cache = Services::cache();
        if (! $forceRefresh) {
            $cachedJwks = $cache->get(self::JWKS_CACHE_KEY);
            if (is_string($cachedJwks) && $cachedJwks !== '') {
                return $cachedJwks;
            }
        }

        try {
            $http = Services::curlrequest([
                'timeout'         => 10,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ], null, null, false);

            $response = $http->get(self::JWKS_ENDPOINT);

            if ($response->getStatusCode() === 200) {
                $body = (string) $response->getBody();
                $cache->save(self::JWKS_CACHE_KEY, $body, self::JWKS_CACHE_TTL);
                return $body;
            }
        } catch (Throwable $e) {
            log_message('error', 'Google OAuth JWKS fetch error: ' . $e->getMessage());
        }

        throw new GoogleAuthException('Identitas Google tidak dapat diverifikasi. Silakan coba lagi.');
    }

    private function base64Url(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder !== 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}