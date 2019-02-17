<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 13:32
 */

namespace App\Models;


use Core\Database\AbstractModel;

class Admin extends AbstractModel
{
    protected static $dbTableName = 'admins';
    protected $dbColumns = ['login', 'email', 'photo', 'password', 'token'];

}