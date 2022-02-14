<?php

namespace Quizty\User;

class PasswordValidate
{
    public function __construct(private $password, private $email)
    {
    }

    public function __invoke()
    {
        $user_repository = new UserRepository();
        $user = $user_repository->findByEmail($this->email);
        return password_verify($this->password, $user['password']) ? $user : false;
    }
}
