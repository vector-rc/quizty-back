<?php

namespace OpenForms\Utils\Octopus;


class Response
{

    public function __construct(public mixed $data = null,public $success = false,public $message = 'data not available')
    {
        $this->data = $data;
        $this->success = $success;
        $this->message = $message;

    }

    public function json($data = null, $success = false, $message = 'data not available')
    {
        print_r(JsonSerialize::encode([
            'data' => $data,
            'success' => $success,
            'message' => $message
        ]));
    }
}
