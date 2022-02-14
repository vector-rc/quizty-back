<?php

namespace Quizty\User;

class EncryptPassword
{
    public function __construct(private $password)
    {
    }

    public function __invoke()
    {
        return password_hash($this->password, PASSWORD_DEFAULT);
    }
}
