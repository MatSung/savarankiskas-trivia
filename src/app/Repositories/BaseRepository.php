<?php

namespace App\Repositories;

class BaseRepository
{
    // forgot to make the type
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

    public function find(string $id): ?array
    {
        return $this->findBy('id', $id);
    }

    public function findBy(string $column, string $value): ?array
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
             return null;
         }
        return $result;
    }

    public function findSingle(string $whereColumn, string $value, string $column): ?string
    {
        $query = "
        SELECT
            *
        FROM
            `$this->table`
        WHERE
            `$whereColumn` = :value
        ";


        $stmt = $this->dbh->prepare($query);
        
        // $stmt->bindValue(':column', $column, \PDO::PARAM_STR);
        $stmt->bindValue(':value', $value, \PDO::PARAM_STR);

        

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$result){
             return null;
         }
        // dd($result, $query, $column, $id);
        // kept returning 'correct_answer' => 'correct_answer'
        return $result[$column];
    }

    public function getCustomEntry(string $id, array $columns): ?array
    {
        $query = 'SELECT ';
        $query .= implode(', ', $columns);
        $query .= " FROM $this->table WHERE `id` = :id";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$result){
             return null;
         }
        return $result;
    }

    public function countEntries(): ?int
    {
        $query = "
            SELECT
                count(*) as amount
            FROM
                `$this->table`
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$result){
            return null;
        }

        return $result['amount'];
    }
}