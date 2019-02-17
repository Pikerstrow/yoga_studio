<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 20:36
 */

namespace Core\Exceptions;


class FilesException extends \Exception
{
    public function getError()
    {
        $date = new \DateTime();
        $date = $date->format('d-m-Y H:i:s');

        return "Date: {$date}. Type: files. Error: " . $this->getMessage() . ". File: " . $this->getFile() . ". Line: " . $this->getLine() . "\n";
    }
}