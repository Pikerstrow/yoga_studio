<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;


use Core\Http\Request;
use App\Models\News;

class NewsController
{
    public function index(Request $request)
    {
        if($request->has('page')){
            if(ctype_digit($request->get('page'))){
                $page = $request->get('page');
            } else {
                return _redirect()->to(APP_URL . "/404");
            }
        } else {
            $page = 1;
        }
        $data = News::simplePaginate("news", 5, $page);

        return _view('pages/news.twig', ['news' => $data['objects'], 'links' => $data['links']]);
    }





    public function show(Request $request, $slug)
    {
        $post = News::getByParam('slug', $slug);

        if(!$post){
            _session()->flash('error', 'Упс! Сталася помилка передачі даних. Спробуйте, будь ласка, ще раз');
            return _redirect()>to("news");
        }

        return _view('pages/news.show.twig', ['post' => $post]);
    }
}