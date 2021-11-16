<?php

namespace PhpFinance\Lib;

class Route
{
    private $uri;
    private $httpMethod;
    private $function;
    private $controller;


    protected function __construct($httpMethod, $uri, $controller, $function)
    {
        $this->httpMethod = $httpMethod;
        $this->uri = $uri;
        $this->controller = $controller;
        $this->function = $function;
    }

    public static function get ($uri, $controller, $function) {
        return new self("GET", $uri, $controller, $function);
    }

    public static function post ($uri, $controller, $function) {
        return new self("POST", $uri, $controller, $function);
    }

    public static function put ($uri, $controller, $function) {
        return new self("PUT", $uri, $controller, $function);
    }

    public static function delete ($uri, $controller, $function) {
        return new self("DELETE", $uri, $controller, $function);
    }

    public function __get($key)
    {
        return $this->$key;
    }






}
