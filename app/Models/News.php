<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 13:32
 */

namespace App\Models;


use Core\Database\AbstractModel;
use Core\Files\File;

class News extends AbstractModel
{
    const NEWS_IMAGES_MAIN_STORAGE = APP_ROOT . DS . "public" . DS . "images" . DS . "news" . DS . "main" . DS;
    const NEWS_IMAGES_TEMPORARY_STORAGE = APP_ROOT . DS . "public" . DS . "images" . DS . "news" . DS . "temporary" . DS;

    protected static $dbTableName = 'news';
    protected $dbColumns = ['title', 'photo', 'body', 'slug'];


    public function deletePhoto()
    {
        $fileName = mb_substr($this->photo, strrpos($this->photo, "/")+1);
        $fileName = self::NEWS_IMAGES_MAIN_STORAGE . $fileName;

        if(file_exists($fileName)){
            return File::delete($fileName);
        }

        return false;
    }

    public function deleteBodyImages()
    {
        $bodyImages = getImagesFileNamesFromSrc($this->body);

        if(!empty($bodyImages)){
            foreach($bodyImages as $value){
                if(file_exists($image = News::NEWS_IMAGES_MAIN_STORAGE . $value)){
                    File::delete($image);
                }
            }
        }
    }

}