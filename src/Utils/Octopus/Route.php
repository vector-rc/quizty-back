<?php

namespace OpenForms\Utils\Octopus;

class Route
{
    private Octopus $_octopus;
    public function __construct(private $route)
    {
        $this->_octopus = new Octopus();
    }

    public function get($callback)
    {
        $this->_octopus->get($this->route, $callback);
        return $this;
    }
    public function post($callback)
    {
        $this->_octopus->post($this->route, $callback);
        return $this;
    }
    public function put($callback)
    {
        $this->_octopus->put($this->route, $callback);
        return $this;
    }
    public function delete($callback)
    {
        $this->_octopus->delete($this->route, $callback);
        return $this;
    }
}