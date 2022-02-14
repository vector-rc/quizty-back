<?php

namespace Quizty\SolvedQuiz;


class SolvedQuiz
{
    
    public function __construct(
        public $id,
        public $quiz_id,
        public $user_id,
        public $date_time,
        public $duration,
        public $responses,
        public $enable
    ) {
    }
}
