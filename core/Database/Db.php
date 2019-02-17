<?php
namespace Core\Database;

use Core\Exceptions\DatabaseException;
use Core\Support\Config;


class Db
{
    private static $connection = null;

    private static function connect()
    {
        try {
            if(!$settings = Config::get('database')) {
                throw new DatabaseException('Помилка зчитування налаштувань для підключення!');
            } else {
                $dsn = $settings['driver'] . ":dbname=" . $settings['name'] . ";host=" . $settings['host'] . ";charset=utf8";
                $user = $settings['user'];
                $password = $settings['password'];

                if (!$connection = new \PDO($dsn, $user, $password,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false
                    ]
                )) {
                    throw new DatabaseException('Помилка підключення до бази даних.');
                }
                return $connection;
            }
        } catch(\PDOException $e){
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function getConnection()
    {
        if(self::$connection == null){
            self::$connection = self::connect();
        }
        return self::$connection;
    }
}