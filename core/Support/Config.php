<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2019
 * Time: 19:35
 */

namespace Core\Support;

use Core\Exceptions\FilesException;
use Core\Files\File;

class Config
{
    protected $configData = array();


    public function __construct()
    {
        $file = APP_ROOT . "/storage/config.json";

        try {
            $this->configData = File::getJsonData($file);
        } catch(FilesException $e){
            _log()->add($e->getError());
        }
    }

    public static function get(string $needle) :array
    {
        $self = new static;

        if (array_key_exists($needle, $self->configData)) {
            return $self->configData[$needle];
        }
        return null;
    }

    /*Теж саме що і get. Добавлено виключно для можливості використання в контексті сутності*/
    public function take(string $needle) :array
    {
        if (array_key_exists($needle, $this->configData)) {
            return $this->configData[$needle];
        }
        return null;
    }
}

