<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;


use App\Models\News;

class HomeController
{
    public function index()
    {
        $lastPost = News::last();

        return _view('pages/index.twig', ['post' => $lastPost]);
    }



}