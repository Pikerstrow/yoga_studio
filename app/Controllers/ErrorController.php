<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:45
 */

namespace App\Controllers;


class ErrorController
{
    public static function notFound()
    {
        return _view('pages/404.twig');
    }

    public static function forbidden()
    {
        return _view('pages/503.twig');
    }
}