<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 12:08
 */

namespace Core\Database;


use Core\Exceptions\DatabaseException;
use Core\Support\Pagination;

abstract class AbstractModel
{
    protected $connection = null;
    protected static $dbTableName = null;
    protected $dbColumns = [];
    protected $pagination;

    public function __construct()
    {
        $this->connection = Db::getConnection();
        $this->pagination = new Pagination();
    }

    public static function all()
    {
        $connection = Db::getConnection();

        try {
            $query = "SELECT * FROM " . static::$dbTableName;
            $stmt = $connection->query($query);

            $objects = [];

            while ($obj = $stmt->fetchObject(static::class)) {
                $objects[] = $obj;
            }

            return $objects;

        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function last()
    {
        $connection = Db::getConnection();

        try {
            $query = "SELECT * FROM " . static::$dbTableName . " ORDER BY id DESC LIMIT 1";
            $stmt = $connection->query($query);

            return $stmt->fetchObject(static::class);

        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function getByParam($dbColumn, $param)
    {
        $connection = Db::getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM " . static::$dbTableName . " WHERE {$dbColumn} = :param");
            $stmt->execute([':param' => $param]);
            $data = $stmt->fetchObject(static::class);
            return $data;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }


    public static function create(array $data)
    {
        $connection = Db::getConnection();

        $query = "INSERT INTO " . static::$dbTableName . " (" . implode(",", array_keys($data)) . ") ";
        $query .= "VALUES (" . implode(",", array_fill(0, count($data), '?')) . ")";


        try {
            $stmt = $connection->prepare($query);

            $counter = 0;
            foreach ($data as $key => &$value) {
                $counter++;
                $stmt->bindParam($counter, $value);
            }

            $stmt->execute();
            return $connection->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

    }


    /**
     * Needs to be refactored!!!!!!!!!!!
     */
    public function update(array $data, $dbColumn = '', $param = '')
    {
        $dataForDb = [];

        foreach($data as $key => $value){
            if(in_array($key, $this->dbColumns)){
                $dataForDb[] = "{$key}=:$key";
            }
        }

        if(empty($dbColumn) and empty($param)){
            $query = "UPDATE " . static::$dbTableName . " SET " . implode(",", $dataForDb) . " WHERE id = " . $this->id;

            try {
                $stmt = $this->connection->prepare($query);

                foreach ($data as $key => &$value) {
                    $stmt->bindParam(":" . $key, $value);
                }

                $stmt->execute();
                return $stmt->rowCount() ? true : false;
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage());
            }
        } else {
            $query = "UPDATE " . static::$dbTableName . " SET " . implode(",", $dataForDb) . " WHERE {$dbColumn} = :param";

            try {
                $stmt = $this->connection->prepare($query);

                foreach ($data as $key => &$value) {
                    $stmt->bindParam(":" . $key, $value);
                }
                $stmt->bindParam(":param", $param);

                $stmt->execute();
                return $stmt->rowCount() ? true : false;
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage());
            }
        }
    }

    public static function count()
    {
        $connection = Db::getConnection();;

        try {
            $query = "SELECT count(*) FROM " . static::$dbTableName;
            $result = $connection->query($query);
            $row = $result->fetch();

            return array_shift($row);

        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function paginate($uri, $quantity, $page)
    {
        $connection = Db::getConnection();

        $model = new static;
        $model->pagination->perPage = $quantity;
        $model->pagination->currentPage = $page;
        $total = $model->pagination->totalItems = static::count();
        $model->pagination->totalPages = ceil($total / $quantity);

        $query = "SELECT * FROM " . static::$dbTableName . " ORDER BY id DESC LIMIT " . $model->pagination->perPage;
        $query .= " OFFSET " . $model->pagination->offset();

        try {
            $stmt = $connection->query($query);

            $objects = [];

            while ($obj = $stmt->fetchObject(static::class)) {
                $objects[] = $obj;
            }

            $data['objects'] = $objects;
            $data['links'] = $model->pagination->getLinks($uri);

            return $data;

        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        return null;
    }

    public static function simplePaginate($uri, $quantity, $page)
    {
        $connection = Db::getConnection();

        $model = new static;
        $model->pagination->perPage = $quantity;
        $model->pagination->currentPage = $page;
        $total = $model->pagination->totalItems = static::count();
        $model->pagination->totalPages = ceil($total / $quantity);

        $query = "SELECT * FROM " . static::$dbTableName . " ORDER BY id DESC LIMIT " . $model->pagination->perPage;
        $query .= " OFFSET " . $model->pagination->offset();

        try {
            $stmt = $connection->query($query);

            $objects = [];

            while ($obj = $stmt->fetchObject(static::class)) {
                $objects[] = $obj;
            }

            $data['objects'] = $objects;
            $data['links'] = $model->pagination->getSimpleLinks($uri);

            return $data;

        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        return null;
    }

    public function delete()
    {
        $query = "DELETE FROM " . static::$dbTableName . " WHERE id = :param";

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":param", $this->id);

            if(!$stmt->execute()){
                throw new DatabaseException('Помилка видалення даних із БД');
            }

            return $stmt->rowCount() ? true : false;

        } catch (\PDOException $e) {
            _log()->add($e->getMessage());
        }
    }

}