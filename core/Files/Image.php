<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.02.2019
 * Time: 19:45
 */

namespace Core\Files;


class Image extends File
{
    const IMAGES_STORAGE = APP_ROOT . DS . "public" . DS . "images" . DS;
    const NEWS_MAIN_DIRECTORY = self::IMAGES_STORAGE . "news" . DS . "main" . DS;
    const NEWS_TEMPORARY_DIRECTORY = self::IMAGES_STORAGE . "news" . DS . "temporary" . DS;

    public $src;

    public function save(string $destination = 'main') :bool
    {
        if (empty($this->filename) or empty($this->temporaryPath)) {
            return false;
        }

        switch(strtolower($destination)){
            case "temporary":
                $this->path = self::NEWS_TEMPORARY_DIRECTORY . $this->filename;
                $this->setSrc('news/temporary');
                if (move_uploaded_file($this->temporaryPath, $this->path)) {
                    unset($this->temporaryPath);
                    return true;
                } else {
                    return false;
                }
                break;
            case "main":
                $this->path = self::NEWS_MAIN_DIRECTORY . $this->filename;
                $this->setSrc('news/main');
                if (move_uploaded_file($this->temporaryPath, $this->path)) {
                    unset($this->temporaryPath);
                    return true;
                } else {
                    return false;
                }
                break;
        }
        return false;
    }


    public function getSrc() :string
    {
        return $this->src;
    }

    public function setSrc($directory) :void
    {
        $this->src = APP_URL . "/images/" . $directory . "/" . $this->filename;
    }
}