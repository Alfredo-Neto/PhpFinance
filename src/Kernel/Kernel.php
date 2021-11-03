<?php

namespace phpFinance\Kernel;
use stdClass;
use PhpFinance\Lib\JsonResponse;

class Kernel
{
    private $method;
    private $uri;
    private $rotas;

    public function bootstrap()
    {
        try {
           $this->setupDefaultErrorHandling();
           $this->setCorsHeaders();
           $this->handleRequest();
           $this->loadRoute();
           $this->callController();
        } catch (\Throwable $e) {
            $response = new JsonResponse([
                'mensagem' =>
                $e->getMessage() . " " . $e->getFile() . "line " . $e->getLine()
            ], 500);
            echo $response->process();
        }
    }

    private function setupDefaultErrorHandling() {
        set_error_handler(function (int $errNo, string $errMsg, string $file, int $line) {
            file_put_contents(
                'errorLog.html',
                "1; [$errNo] [$errMsg] [$file] [$line]",
                FILE_APPEND
            );
        });
        set_exception_handler(function (\Throwable $exception) {
            file_put_contents(
                'errorLog.html',
                "2; [$exception->getMessage()] [$exception->getFile()] [$exception->getLine()]",
                FILE_APPEND
            );
//            $message =  "Error: ".$exception->getMessage();
//            $response = new JsonResponse(['message' => $message], 500);
//            echo $response->process();
        });
    }
    private function setCorsHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
    }

    private function handleRequest()
    {
        $request = json_decode(file_get_contents('php://input'));
        if ($request == null)
            $request = new stdClass();
        
        foreach ($_GET as $key => $value) {
            $request->$key = $value;
        }

        // $request->token_awt = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        $uriTratamento = $_SERVER['REQUEST_URI'];
        $uriTratamento = explode('?', $uriTratamento);

        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $uriTratamento[0];
        $this->request = $request;
    }

    private function loadRoute()
    {
        $rotas = [];
        $rotas["GET"]["/"] = ['AuthController', "loadIndex"];
        $rotas["POST"]["/login"] = ['AuthController', "login"];
        $rotas["POST"]["/register"] = ['AuthController', "register"];
        $rotas["GET"]["/logout"] = ['AuthController', "logout"];
        $rotas["GET"]["/logados"] = ['IndexLoggedInController', "index"];
        $this->rotas = $rotas;
    }

    private function callController()
    {
        $response = null;
        if (isset($this->rotas[$this->method][$this->uri])){
            $meuController = $this->instanciaClasse($this->rotas[$this->method][$this->uri][0]);
            $response = $this->executaMetodo($meuController, $this->rotas[$this->method][$this->uri][1], [$this->request]);
        } else {
            file_put_contents('logRotas.html', "Rota não encontrada!" . '<br>', FILE_APPEND);
            $response = new JsonResponse(['mensagem' => 'Rota não encontrada'], 405);
        }
        echo $response->process();
    }

    private function instanciaClasse($nomeDaClasse)
    {
        $nomeDaClasse = "PhpFinance\\Controller\\$nomeDaClasse";
        return new $nomeDaClasse();
    }

    private function executaMetodo($objeto, $nomeDoMetodo, $parametros = [])
    {
        return $objeto->{$nomeDoMetodo}(...$parametros);
    }
}