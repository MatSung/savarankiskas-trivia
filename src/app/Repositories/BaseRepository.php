<?php

namespace App\Repositories;

class BaseRepository
{
    protected $name = 'Question';
    protected $table = 'questions';

    protected \PDO $dbh;

    function __construct()
    {
        try {
            $dsn = 'mysql:host=mariadb;dbname=' . getenv('MYSQL_DATABASE');

            $this->dbh = new \PDO($dsn, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'), [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Server error', 500);
        }
    }

    public function find(string $id): array
    {
        return $this->findBy('id', $id);
    }

    public function findBy(string $column, string $value): array
    {

        $query = "
        SELECT
            *
        FROM
            `$this->table`
        WHERE
            `$column` = :value
        ";


        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':value', $value, \PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$result){
            throw new \RuntimeException('Entry not found', 400);
        }
        return $result;
    }
}