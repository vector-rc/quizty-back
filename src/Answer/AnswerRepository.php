<?php
namespace Quizty\Answer;
use Quizty\Utils\MysqlRepository;

final class AnswerRepository
{
    private $repository;
    public function __construct()
    {
        $this->repository = new MysqlRepository();
    }

    public function findAll()
    {
        return $this->repository->select('answer',null,'enable=1');
    }
    public function findById($id)
    {
        $data = $this->repository->select('answer', null, 'id = :id and enable=1', ['id' => $id]);
        return $data ? $data[0] : $data;
    }

    public function save(Answer $new_answer)
    {
        return $this->repository->insert('answer', (array)$new_answer);
    }
    public function edit(Answer $answer)
    {
        return $this->repository->update('answer', (array)$answer,'id=:id');
    }
    public function delete($id)
    {
        return $this->repository->delete('answer', $id);
    }
}
