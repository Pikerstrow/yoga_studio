<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 14:57
 */

namespace Core\Files;

use Core\Exceptions\FilesException;


class File
{
    public $temporaryPath;
    public $type;
    public $size;
    public $filename;
    public $path;


    public function __construct($file)
    {
        $this->setFile($file);
    }

    private function setFile($file) :void
    {
        $ext = mb_strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $this->filename = mb_substr(md5($file['name'] . microtime()), 0, 10) . '.' . $ext;
        $this->temporaryPath = $file['tmp_name'];
        $this->type = $file['type'];
        $this->size = $file['size'];
    }

    public static function move($oldDestination, $newDestination)
    {
        if(!rename($oldDestination, $newDestination)){
            throw new FilesException("Помилка переміщення файлу із однієї директорії в іншу!");
        }
        return true;
    }

    public static function getJsonData($file)
    {
        if(!file_exists($file)){
            throw new FilesException("Файлу із маршрутами не існує!");
        } else {
            if(!$data = file_get_contents($file)){
                throw new FilesException("Помилка зчитування файлу із маршрутами!");
            }
            return json_decode($data, true);
        }

        return null;
    }

    public function getPath() :string
    {
        return $this->path;
    }

    public static function delete(string $fileName) :bool
    {
        if(file_exists($fileName)){
            return unlink($fileName) ? true : false;
        }
    }

}