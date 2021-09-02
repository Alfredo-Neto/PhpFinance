<?php

namespace PhpFinance\Controller;

use Exception;
use PhpFinance\Database\DbConnectionFactory;
use PhpFinance\Lib\JsonResponse;

class AuthController extends Controller
{
    public function login($request)
    {
        try {
            if (!property_exists($request, 'username') && !property_exists($request, 'password') 
            || $request->username == null || $request->username == '' 
            || $request->password == null || $request->password == '') {
                throw new Exception("Campos precisam ser preenchidos", 1);
            }

            $pdo = DbConnectionFactory::get();
            $sql = "select * from Usuarios where name like '$request->username'";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $usuariosEncontrados = $statement->fetch(\PDO::FETCH_ASSOC);

            if (empty($usuariosEncontrados)) {
                throw new \Exception("Usuário não encontrado");
            }

            if (!password_verify ($request->password , $usuariosEncontrados['password'])) {
                throw new \Exception("Senha incorreta");
            }

            $token_awt = $this->makeAWT(
                $usuariosEncontrados['name'],
                $usuariosEncontrados['id']
            );

            return new JsonResponse(['token_awt' => $token_awt], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['mensagem' => $e->getMessage()], 500);
        }
    }

    public function register($request)
    {
        try {
            if (!property_exists($request, 'username') || $request->username == null
            || $request->username == '') {
                throw new \Exception("Please inform username field", 1);
            }

            if (!property_exists($request, 'password') || $request->password == null
            || $request->password == '') {
                throw new \Exception("Please inform password field", 1);
            }

            if (!property_exists($request, 'repeat') || $request->repeat == null
            || $request->repeat == '') {
                throw new \Exception("Repeat password is     empty.", 1);
            }

            if (strcmp($request->password, $request->repeat) != 0) {
                throw new \Exception("Passwords must be equal.", 1);
            }

            $username = $request->username;
            $password = $request->password;

            $pdo = DbConnectionFactory::get();
            $sql = "select * from Usuarios where name like '$username'";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $numRowsUsername = $statement->fetchAll(\PDO::FETCH_ASSOC);

            if (count($numRowsUsername) > 0) {
                throw new \Exception("User already signed up.", 1);
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "insert into Usuarios (name, password)
            values (:username, :password)";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $passwordHash);
            $result = $statement->execute();

            $mensagem = '';

            if($result == true) {
                $mensagem = 'Cadastrado com sucesso!';
            } else {
                $mensagem = 'Erro no cadastro! Verifique seu email!';
            }

            return new JsonResponse(['mensagem' => $mensagem], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem' => $e->getMessage()], 500);
        }
    } 
}
