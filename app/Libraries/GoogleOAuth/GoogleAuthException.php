<?php

namespace App\Libraries\GoogleOAuth;

use RuntimeException;

/**
 * Exception khusus alur Google Login.
 *
 * PENTING: pesan exception ini SELALU aman ditampilkan ke pengguna.
 * Detail teknis hanya ditulis ke log, tidak pernah ke pesan exception.
 */
class GoogleAuthException extends RuntimeException
{
}