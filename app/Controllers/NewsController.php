<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;


class NewsController
{
    public function index()
    {
        return _view('pages/index.twig');
    }

    public function show($identifier)
    {
        $data = [
            'identifier' => $identifier
        ];

        return _view('pages/news.show.twig', ['data' => $data]);
    }
}