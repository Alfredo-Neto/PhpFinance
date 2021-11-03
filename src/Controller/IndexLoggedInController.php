<?php

namespace PhpFinance\Controller;

use Exception;
use PDOException;
use PhpFinance\Lib\JsonResponse;
use PhpFinance\Controller\Controller;
use PhpFinance\Lib\AuthorizationException;

class IndexLoggedInController extends Controller
{
    public $isLoggedIn = [
        'Alfredo' => 1, 
        'Rui' => 1, 
        'Victor' => 1
    ];

    public function index($request)
    {
        try {
            if(!property_exists($request, 'token_awt') || $request->token_awt == null 
            || $request->token_awt == ''){
                throw new AuthorizationException ("Please inform token_awt field.", 1);
            }
            
            $this->validateAWT($request->token_awt);

            return new JsonResponse(['usuarios' => $this->isLoggedIn], 200);
            
        } catch (PDOException $e) {
            file_put_contents('logBanco.txt', $e->getMessage() . '\n', FILE_APPEND);
            return new JsonResponse(['mensagem' => $e->getMessage()], 500);
        } catch (AuthorizationException $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);

        }
    }
}



