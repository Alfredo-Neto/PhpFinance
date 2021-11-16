<?php

namespace PhpFinance\Lib;

class RouteDefiner {

    //onde o usuario define as rotas
    public function get () {
        $routes[] = Route::get("/", 'AuthController', "loadIndex");
        $routes[] = Route::post("/login" ,'AuthController', "login");
        $routes[] = Route::post("/register" ,'AuthController', "register");
        $routes[] = Route::post("/logout" ,'AuthController', "logout");
        $routes[] = Route::get("/logados" ,'IndexLoggedInController', "index");

        return $routes;
    }
}