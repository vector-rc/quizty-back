<?php

namespace Quizty\Session;

use Quizty\Utils\MysqlRepository;

final class SessionValidator
{
    public function __construct(public $id)
    {
    }

    public function __invoke()
    {
        $mysql_repository = new MysqlRepository();
        $session = $mysql_repository->select('Session',null,'id=:id', ['id' => $this->id]);
        return $session?$session[0]:$session;
      
    }
}
