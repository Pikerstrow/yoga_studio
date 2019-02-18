<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.02.2019
 * Time: 19:45
 */

namespace Core\Files;


class Avatar extends Image
{
    const AVATAR_MAIN_DIRECTORY = self::IMAGES_STORAGE . "profile" . DS . "main" . DS;
    const AVATAR_TEMPORARY_DIRECTORY = self::IMAGES_STORAGE . "profile" . DS . "temporary" . DS;

    public function save(string $destination = 'main') :bool
    {
        if (empty($this->filename) or empty($this->temporaryPath)) {
            return false;
        }

        switch(strtolower($destination)){
            case "temporary":
                $this->path = self::AVATAR_TEMPORARY_DIRECTORY . $this->filename;
                $this->setSrc('profile/temporary');
                if (move_uploaded_file($this->temporaryPath, $this->path)) {
                    unset($this->temporaryPath);
                    return true;
                } else {
                    return false;
                }
                break;
            case "main":
                $this->path = self::AVATAR_MAIN_DIRECTORY . $this->filename;
                $this->setSrc('profile/main');
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


}