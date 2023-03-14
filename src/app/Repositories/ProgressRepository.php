<?php

namespace App\Repositories;

class ProgressRepository extends BaseRepository
{
    protected $name = 'progress';
    protected $table = 'saved';

    public function get(string $ip): ?array
    {
        return $this->findBy('ip', $ip);
    }

    public function getResults(string $ip): ?string
    {
        return $this->findSingle('ip', $ip, 'correct_answers');
    }

    public function create(string $ip): array
    {
        $query = "
        INSERT INTO 
            `$this->table`
            (ip, progress, correct_answers)
        VALUES
            (:ip, null, '[null]')
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);

        $stmt->execute();

        return $this->get($ip);
    }

    public function update(string $ip, string $progress, string $correctAnswers): array
    {
        // move them to base repository
        $query = "
        UPDATE
            `$this->table`
        SET
            `progress`=:progress,
            `correct_answers`=:correctAnswers
        WHERE
            `ip` = :ip
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':progress', $progress, \PDO::PARAM_STR);
        $stmt->bindValue(':correctAnswers', $correctAnswers, \PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);
        

        $stmt->execute();

        return $this->get($ip);
    }

    public function delete(string $ip): bool
    {
        $query = "
        DELETE FROM
            `$this->table`
        WHERE
            `ip` = :ip
        ";

        $stmt = $this->dbh->prepare($query);

        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);

        $stmt->execute();

        return true;
    }
}
