<?php

namespace PhpFinance\Lib;

use PhpFinance\Lib\Route;

class Router
{
    public $rotas = [];

    // carregar as rotas que foram criadas anteriormente
    public function registerFromArray($routes){
        $this->rotas = $routes;
    }

    //localizar uma rota
    public function locate($httpMethod, $uri) {
        foreach ($this->rotas as $key => $rota) {
            if($httpMethod == $rota->httpMethod && $uri == $rota->uri) {
                return $rota;
            }
        }
        return false;
    }

}
