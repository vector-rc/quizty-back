<?php

namespace OpenForms\User;

define(PASSWORD_DEFAULT, '1fc281');


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
