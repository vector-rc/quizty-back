<?php

namespace Quizty\Utils\Octopus;

use ReflectionFunction;

class Octopus
{
    public $req;
    public function __construct()
    {
        $body_request = file_get_contents('php://input');
        $body_request = (array)json_decode($body_request);
        array_push($body_request, ...$_POST);
        $headers_request = getallheaders();
         $this->req = new Request($_GET['request'], $body_request, $headers_request, $_COOKIE, $_GET);
        //$this->req = ['uri' => $_GET['request'], 'body' => $body_request, 'headers' => $headers_request, 'cookies' => $_COOKIE, 'query' => $_GET];
        //$this->req=(object)$this->req;
    }

    public function middleware($callback)
    {
        $this->req=call_user_func($callback,$this->req, new Response());
    }


    public function route($route)
    {
        return new Route($route);
    }


    public function get($route, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->callback_route($callback, $route);
        }
    }
    public function post($route, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->callback_route($callback, $route);
        }
    }

    public function put($route, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {
            $this->callback_route($callback, $route);
        }
    }
    public function delete($route, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
            $this->callback_route($callback, $route);
         
        }
    }

    public function callback_route($callback, $route)
    {
        if (!$this->match_uri($this->req->uri, $route)) return;

        $params = $this->get_params($this->req->uri, $route);

        $rp = new ReflectionFunction($callback);

        if (sizeof($rp->getParameters()) === 0) {
            call_user_func($callback, ...$params);
            return;
        }

        $req_res = [];
        foreach ($rp->getParameters() as $param) {
            if ($param->name == 'res' || $param->name == 'response') {
                array_push($req_res, new Response());
            }
            if ($param->name == 'req' || $param->name == 'request') {
                array_push($req_res, $this->req);
            }
        }
        call_user_func($callback, ...$req_res, ...$params);
     

    }


    private function get_params($uri, $route)
    {
        $route = $route[0] == '/' ? substr($route, 1) : $route;

        if (!(str_contains($route, ':') || str_contains($route, '{'))) {
            return array();
        }
        $temp_route = explode('/', $route);
        $temp_params = array();
        $temp_uri_explode = explode('/', $uri);
        foreach ($temp_route as $index => $value) {
            if (str_starts_with($value, ':') || (str_starts_with($value, '{') && str_ends_with($value, '}'))) {
                array_push($temp_params,  $temp_uri_explode[$index]);
            }
        };
        return $temp_params;
    }

    private function match_uri($uri, $route)
    {
        $route = $route[0] == '/' ? substr($route, 1) : $route;

        $temp_route = explode('/', $route);
        foreach ($temp_route as $key => $value) {
            if (str_contains($value, ':') || str_contains($value, '{')) {
                $temp_route[$key] = '[ñ \w]+';
            }
        };

        $regex = '/' . implode('\/', $temp_route) . '\/' . '|' . implode('\/', $temp_route) . '/';
        preg_match($regex, $uri, $output_array);
        return ($output_array && str_replace($output_array[0], '', $uri) == '');
    }
}
