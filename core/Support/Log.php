<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2019
 * Time: 20:36
 */

namespace Core\Support;


class Log
{
    protected $logFile = null;
    protected $logs = array();


    public function __construct()
    {
        $this->logFile = APP_ROOT . "/storage/logs.txt";
    }


    public function add(string $message) :void
    {
         file_put_contents($this->logFile, $message, FILE_APPEND);
    }


    /**
     * @return array
     *
     * Note:
     * array_slice method used because of all Exception classes use "\n" symbol at the end of each error message.
     * Hence without using array_slice method last element of logs array will be empty.
     */
    public function getAll() :array
    {
        if(!$handle = fopen($this->logFile, 'r')){
            return null;
        } else {
            while (!feof($handle)) {
               $this->logs[] = fgets($handle);
            }
            fclose($handle);
            return array_slice($this->logs, 0, count($this->logs)-1);
        }
    }

}