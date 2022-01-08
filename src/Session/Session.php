<?php

namespace OpenForms\Session;

final class Session
{
    public function __construct(public $id, public $email)
    {
    }
    public function __invoke()
    {
    }
}
