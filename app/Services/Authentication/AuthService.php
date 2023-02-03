<?php

/**
 * Laravel Service Class
 * PHP version 8.1
 *
 * @category App\Services
 * @package  App\Services\Authentication
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
namespace App\Services\Authentication;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class AuthService
 *
 * @category App\Services
 * @package  App\Services\Authentication
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
class AuthService
{
    /**
     * Authenticate user by their email and password
     * @param string $email
     * @param string $password
     * @return string|null
     */
    public function authUserByEmailAndPassword(string $email, string $password): ?string
    {

        $authCheck = Auth::attempt([
            'email'     => $email,
            'password'  => $password,
        ]);

        if (!$authCheck) {
            return null;
        }

        $user = Auth::user();
        return $user->createToken(Config::get('app.key'))->accessToken;
    }
}
