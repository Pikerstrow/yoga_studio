<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2019
 * Time: 21:55
 */

namespace Core\Exceptions;


class DatabaseException extends \Exception
{
    public function getError()
    {
        $date = new \DateTime();
        $date = $date->format('d-m-Y H:i:s');

        return "Date: {$date}. Type: database. Error: " . $this->getMessage() . ". File: " . $this->getFile() . ". Line: " . $this->getLine() . "\n";
    }
}