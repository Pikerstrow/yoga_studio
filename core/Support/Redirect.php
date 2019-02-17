<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 15:15
 */

namespace Core\Support;


class Redirect
{
    public function to($route)
    {
        return header("Location:" . $route);
    }
}