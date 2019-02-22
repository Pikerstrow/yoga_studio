<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:45
 */

namespace App\Controllers;

use Core\Files\Avatar;
use Core\Files\Image;
use Core\Http\Request;

class ImagesController
{
    /**
     * Uploads images from CKEditor only!!!
     * @param Request $request
     */
    public function upload(Request $request)
    {
        $image = new Image($request->get('image'));
        $image->save('temporary');
        echo json_encode(['url' => $image->getSrc()]);
    }


    public function uploadNewsMainPhoto(Request $request)
    {
        $data = $request->validate([
            "photo" => "required|maxsize:5120|image|proportions:900*450"
        ]);

        if(isset($data['errors'])){
            echo json_encode(["error" => $data['errors']['photo']]);
            return;
        }

        $image = new Image($request->get('photo'));
        $image->save('temporary');
        echo json_encode(['url' => $image->getSrc()]);
    }

    public function uploadAdminAvatar(Request $request)
    {
        $data = $request->validate([
            "photo" => "required|maxsize:5120|image|proportions:500*500"
        ]);

        if(isset($data['errors'])){
            echo json_encode(["error" => $data['errors']['photo']]);
            return;
        }

        $image = new Avatar($request->get('photo'));
        $image->save('temporary');
        echo json_encode(['url' => $image->getSrc()]);
    }
}