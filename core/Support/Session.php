<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.01.2019
 * Time: 21:29
 */

namespace Core\Support;


class Session
{
    public function __construct()
    {
        if(session_status() == PHP_SESSION_NONE ) {
            session_start();
            $this->set('flash_array', []);
        }
    }


    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }


    public function has(string $needle) :bool
    {
        return array_key_exists($needle, $_SESSION);
    }


    public function checkMany(array $needle) :bool
    {
        foreach($needle as $key){
            if(!in_array($key, array_keys($_SESSION))){
                return false;
            }
        }
        return true;
    }


    public function get(string $needle)
    {
        return $this->has($needle) ? $_SESSION[$needle] : null;
    }


    public function remove($needle) :void
    {
        if(is_array($needle)){
           foreach($needle as $key){
               if($this->has($key)){
                   $this->remove($key);
               }
           }
        } else {
            if($this->has($needle)){
                unset($_SESSION[$needle]);
            }
        }
    }


    public function flash($name, $value)
    {
        if (!$this->has($name)) {
            $this->set($name, $value);
        }
    }


    public function getFlash($name)
    {
        if (!$this->has($name)) {
            return null;
        }
        $value = $this->get($name);
        $this->remove($name);
        return $value;
    }

}