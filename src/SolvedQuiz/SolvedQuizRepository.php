<?php
namespace OpenForms\SolvedQuiz;
use OpenForms\Utils\MysqlRepository;

final class SolvedQuizRepository
{
    private $repository;
    public function __construct()
    {
        $this->repository = new MysqlRepository();
    }

    public function findAll()
    {
        return $this->repository->select('Solved_Quiz',null,'enable=1');
    }

    public function findById($id)
    {
        $data = $this->repository->select('Solved_Quiz', null, 'id = :id and enable=1', ['id' => $id]);
        return $data ? $data[0] : $data;
    }
    public function findByQuizId($quiz_id)
    {
        $data = $this->repository->select('Solved_Quiz', null, 'quiz_id = :quiz_id and enable=1', ['quiz_id' => $quiz_id]);
        
        
        return $data;
       
    }


    public function findByUser($user)
    {
        $data = $this->repository->select('Solved_Quiz', null, 'user_id = :user_id and enable=1', ['user_id' => $user]);
        return $data;
    }

    public function save(SolvedQuiz $new_quiz)
    {
        return $this->repository->insert('Solved_Quiz', (array)$new_quiz);
    }

    public function edit(SolvedQuiz $quiz)
    {
        return $this->repository->update('Solved_Quiz', (array)$quiz,'id=:id');
    }

    public function delete($id)
    {
        return $this->repository->delete('Solved_Quiz', $id);
    }
}
