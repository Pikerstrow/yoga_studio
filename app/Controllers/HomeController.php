<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;


class HomeController
{
    public function index()
    {
        return _view('pages/index.twig');
    }

}