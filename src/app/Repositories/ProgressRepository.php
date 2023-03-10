<?php

namespace App\Repositories;

class ProgressRepository extends BaseRepository
{
    protected $name = 'progress';
    protected $table = 'saved';

    public function get(string $ip): array
    {
        return $this->findBy('ip', $ip);
    }

    public function create(string $ip): bool
    {
        $query = "
        INSERT INTO 
            `$this->table`
            (ip, progress)
        VALUES
            (:ip, null)
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);

        $stmt->execute();

        return true;
    }

    public function update(string $ip, string $progress): bool
    {
        $query = "
        UPDATE
            `saved`
        SET
            `progress`=:progress
        WHERE
            `ip` = :ip
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':progress', $progress, \PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);

        $stmt->execute();

        return true;
    }

    public function delete(string $ip): bool
    {
        $query = "
        DELETE FROM
            saved
        WHERE
            `ip` = :ip
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);

        $stmt->execute();

        return true;
    }
}
