<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.02.2019
 * Time: 19:19
 */

namespace Core\Support;


class ForgotPassword
{
    private $length = 50;
    private $token;


    public function __construct()
    {
        $this->generateToken();
    }

    /**
     * Токен буде зберігатися в БД, та в подальшму використовуватися для відновлення паролю.
     */
    private function generateToken() :void
    {
        $this->token = bin2hex(openssl_random_pseudo_bytes($this->length));
    }

    public function getToken() :string
    {
        return $this->token;
    }
}