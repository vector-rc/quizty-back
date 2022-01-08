<?php

namespace OpenForms\Quiz;

use DateTime;

class Quiz
{
    
    public function __construct(
        public $id,
        public $user_id,
        public $name,
        public $date_time,
        public $duration,
        public $questions,
        public $answers,
        public $enable
    ) {
    }
}
