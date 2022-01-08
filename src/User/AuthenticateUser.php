<?php

namespace OpenForms\User;

class AuthenticateUser
{
    public function __construct(private $email, private $password)
    {
    }
    public function __invoke()
    {
        $user_repository = new UserRepository();
        if (!$user_repository->exist($this->email)) {
            return false;
        }
        $password_validate = new PasswordValidate($this->password, $this->email);
        $user = $password_validate();


        return $user ? new User($user['id'], $user['name'],  $user['email'], $user['password'],1) : false;
    }
}
