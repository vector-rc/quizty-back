<?php

namespace Quizty\Utils\Octopus;

class Request
{
    public function __construct(public $uri,public $body, public $headers,public $cookies,public $query)
    {
    }
}