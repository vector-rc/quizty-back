<?php

namespace Quizty\Session;

use Quizty\User\User;
use Quizty\Utils\MysqlRepository;

final class SessionCreator
{
    public function __construct(public User $user)
    {
    }

    public function __invoke()
    {
        $mysql_repository = new MysqlRepository();
        $id = hash('sha256', $this->user->email . time());
        $mysql_repository->data_write('UPDATE Session SET enable=0 WHERE user_id=:user_id', ['user_id' => $this->user->id]);
        $new_session = $mysql_repository->insert('Session', ['id' => $id, 'user_id' => $this->user->id, 'user_email' => $this->user->email, 'date_time' => date('Y-m-d H:i:s'), 'enable' => 1]);
        if ($new_session) {
            return $new_session;
        }
    }
}
