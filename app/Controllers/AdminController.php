<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;


use App\Models\Admin;

class AdminController
{
    public function index()
    {
        return _view('pages/admin/index.twig');
    }

}