<?php
namespace Quizty\Answer;

class Answer{
    public function __construct(
        public $id,
        public $name,
        public $is_correct,
    )
    {
        # code...
    }
}