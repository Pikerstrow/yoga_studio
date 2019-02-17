<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.01.2019
 * Time: 21:33
 */

namespace Core\Support;


trait Checkout
{
    protected $storage = null;

    public function has(string $needle) :bool
    {
        return array_key_exists($needle, $this->storage);
    }

    public function get(string $needle)
    {
        return $this->has($needle) ? $this->storage[$needle] : null;
    }

    public function getAll() :array
    {
        return $this->storage;
    }
}