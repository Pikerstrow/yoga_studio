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

class Admin extends AbstractModel
{
    protected static $dbTableName = 'admins';
    protected $dbColumns = ['login', 'email', 'photo', 'password', 'token'];

    const ADMIN_AVATAR_MAIN_STORAGE = APP_ROOT . DS . "public" . DS . "images" . DS . "profile" . DS . "main" . DS;
    const ADMIN_AVATAR_TEMPORARY_STORAGE = APP_ROOT . DS . "public" . DS . "images" . DS . "profile" . DS . "temporary" . DS;

    public function deletePhoto()
    {
        $fileName = mb_substr($this->photo, strrpos($this->photo, "/")+1);
        $fileName = self::ADMIN_AVATAR_MAIN_STORAGE . $fileName;

        if(file_exists($fileName)){
            return File::delete($fileName);
        }

        return false;
    }

}