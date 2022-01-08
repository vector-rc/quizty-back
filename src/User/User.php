<?php

namespace OpenForms\User;

class User
{

    public function __construct(
        public $id,
        public $name,
        public $email,
        public $password,
        public $enable
    ) {
    }
}
