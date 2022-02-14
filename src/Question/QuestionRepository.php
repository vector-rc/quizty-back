<?php
namespace Quizty\Question;
use Quizty\Utils\MysqlRepository;

final class QuestionRepository
{
    private $repository;
    public function __construct()
    {
        $this->repository = new MysqlRepository();
    }

    public function findAll()
    {
        return $this->repository->select('question',null,'enable=1');
    }
    public function findById($id)
    {
        $data = $this->repository->select('question', null, 'id = :id and enable=1', ['id' => $id]);
        return $data ? $data[0] : $data;
    }

    public function save(Question $new_question)
    {
        return $this->repository->insert('question', (array)$new_question);
    }
    public function edit(Question $question)
    {
        return $this->repository->update('question', (array)$question,'id=:id');
    }
    public function delete($id)
    {
        return $this->repository->delete('question', $id);
    }
}
