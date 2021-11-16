<?php

namespace PhpFinance\Controller;

use DateTime;
use Exception;
use PDOException;
use PhpFinance\Database\DbConnectionFactory;
use PhpFinance\Lib\AuthorizationException;
use PhpFinance\Lib\JsonResponse;

class AuthController extends Controller
{
    public function loadIndex($request)
    {
        header('Location: index.html');
        exit();
    }

    public function login($request)
    {
        try {
            var_dump(property_exists($request,'username'));
            if (!property_exists($request, 'username') || !property_exists($request, 'password') 
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

            $dataHora = new DateTime();
            $dataHora = $dataHora->format("Y-m-d H:i:s");

            //FAZER INSERÇÃO DO TOKEN NA TABELA TOKENS
            $sql = "insert into Tokens(token, datahora, status, usuarioid)
                    values(:token, :datahora, :status, :usuarioid)";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":token", $token_awt);
            $statement->bindValue(":datahora", $dataHora);
            $statement->bindValue(":status", 1);
            $statement->bindValue(":usuarioid", $usuariosEncontrados['id']);
            $statement->execute();

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

    public function logout($request)
    {
        try {

            if (!property_exists($request, 'token_awt') || $request->token_awt == null
            || $request->token_awt == '') {
                throw new AuthorizationException("Please inform token_awt field", 1);
            }

            $this->validateAWT($request->token_awt);

            $pdo = DbConnectionFactory::get();
            $sql = "UPDATE Tokens SET status = '0' WHERE token = '$request->token_awt'";
            $statement = $pdo->prepare($sql);
            $statement->execute();
    
            return new JsonResponse(['mensagem' => 'Token invalidado com sucesso'], 200);

        } catch (PDOException $e) {
            file_put_contents('log.txt', $e->getMessage() . '\n', FILE_APPEND);
            return new JsonResponse(['mensagem' => $e->getMessage()], 500);
        } catch (AuthorizationException $e) {
            return new JsonResponse(['mensagem' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return new JsonResponse(['mensagem' => $e->getMessage()], 500);
        }
    }
}
