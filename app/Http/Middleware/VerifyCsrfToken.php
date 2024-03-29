<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'http://127.0.0.1:8000/api/register?email=waseemghazal@gmail.com&firstName=waseem&lastName=ghazal&phone=0597567681&picture=alknfkslf&password=Qqqq1234',
        // '/api/register',
        '/requestResetPassword',
        '/verifyResetPassword'
    ];
}
