<?php

namespace App\Repositories;



class QuestionRepository extends BaseRepository
{
    protected $name = 'question';
    protected $table = 'questions';

    public function getSingleQuestion(string $id): ?array
    {
        return $this->getCustomEntry($id, ['id', 'question', 'choices']);
    }   

    public function getAnswers(): array
    {
        $query = "
        SELECT
            `id`, `correct_answer`
        FROM
            `$this->table`
        ";


        $stmt = $this->dbh->prepare($query);

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function getAnswer(string $id): ?string
    {
        return $this->findSingle('id', $id, 'correct_answer');
    }
}
