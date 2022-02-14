<?php
namespace Quizty\Quiz;
use Quizty\Utils\MysqlRepository;

final class QuizRepository
{
    private $repository;
    public function __construct()
    {
        $this->repository = new MysqlRepository();
    }

    public function findAll()
    {
        return $this->repository->select('Quiz',null,'enable=1');
    }

    public function findById($id)
    {
        $data = $this->repository->select('Quiz', null, 'id = :id and enable=1', ['id' => $id]);
        return $data ? $data[0] : $data;
    }
    public function findById_not_solved($id)
    {
        $data = $this->repository->select('Quiz', null, 'id = :id and enable=1', ['id' => $id]);
        
        if($data){
            $data=$data[0];
            $answers=json_decode($data['answers']);
            $answers=array_map(function($el){
                $el=(array)$el;
                return ['id'=>$el['id'],'questionId'=>$el['questionId'],'answer'=>$el['answer']];
            },$answers);
    
            $data['answers']=json_encode($answers);
            return $data;
        }
        return $data;
       
    }


    public function findByUser($user)
    {
        $data = $this->repository->select('Quiz', null, 'user_id = :user_id and enable=1', ['user_id' => $user]);
        return $data;
    }
    public function findMinimizedByUser($user)
    {
        $data = $this->repository->select('Quiz', null, 'user_id = :user_id and enable=1', ['user_id' => $user],'id,date_time,name');
        return $data;
    }

    public function save(Quiz $new_quiz)
    {
        return $this->repository->insert('Quiz', (array)$new_quiz);
    }

    public function edit(Quiz $quiz)
    {
        return $this->repository->update('Quiz', (array)$quiz,'id=:id');
    }

    public function delete($id)
    {
        return $this->repository->delete('Quiz', $id);
    }
}
