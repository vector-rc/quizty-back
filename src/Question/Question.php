<?php
namespace Quizty\Question;

class Question{
    public function __construct(
        public $id,
        public $name,
        public $type_answer,
        public $required,
    )
    {
        # code...
    }
}