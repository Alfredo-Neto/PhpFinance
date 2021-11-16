<?php

namespace PhpFinance\Tests;

use PDO;
use stdClass;
use PHPUnit\Framework\TestCase;
use PhpFinance\Tests\DatabaseHelper;
use PhpFinance\Controller\AuthController;
use PhpFinance\Database\DbConnectionFactory;

class LoginTest extends DatabaseHelper
{
    public function testLoginSemsUsernameSemSenha()
    {
        // Arrange
        // front => (request) server =>(variaveis) php
        $request = new stdClass();
        
        //$request->token_awt = '';//botar token aqui caso exista;
        $authController = new AuthController();

        // Act
        $response = $authController->login($request);

        // Assert
        $this->assertEquals(500,$response->code);

        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Tokens WHERE usuarioId = :usuarioId and status = :status";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":usuarioId", 6);
        $statement->bindValue(":status", 1);
        $statement->execute();
        $token = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertTrue(is_bool($token));
    }

    public function testLoginSemSenha()
    {
        // Arrange
        // front => (request) server =>(variaveis) php
        $request = new stdClass();
        $request->username = 'alfredo';
        
        //$request->token_awt = '';//botar token aqui caso exista;
        $authController = new AuthController();

        // Act
        $response = $authController->login($request);

        // Assert
        $this->assertEquals(500,$response->code);

        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Tokens WHERE usuarioId = :usuarioId and status = :status";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":usuarioId", 6);
        $statement->bindValue(":status", 1);
        $statement->execute();
        $token = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertTrue(is_bool($token));
    }

    public function testLoginSemUsername()
    {
        // Arrange
        // front => (request) server =>(variaveis) php
        $request = new stdClass();
        $request->password = 'alfredo';
        
        //$request->token_awt = '';//botar token aqui caso exista;
        $authController = new AuthController();

        // Act
        $response = $authController->login($request);
        // Assert
        $this->assertEquals(500,$response->code);

        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Tokens WHERE usuarioId = :usuarioId and status = :status";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":usuarioId", 6);
        $statement->bindValue(":status", 1);
        $statement->execute();
        $token = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertTrue(is_bool($token));
    }

    public function testLoginComUsernameEComSenha()
    {
        // Arrange
        // front => (request) server =>(variaveis) php
        
        $request = new stdClass();
        $request->username = 'a@a';
        $request->password = '123456';
        
        //$request->token_awt = '';//botar token aqui caso exista;
        $authController = new AuthController();

        // Act
        $response = $authController->login($request);

        // Assert
        $this->assertEquals(200,$response->code);

        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Tokens WHERE token = :token and usuarioId = :usuarioId and status = :status";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":token", $response->data['token_awt']);
        $statement->bindValue(":usuarioId", 6);
        $statement->bindValue(":status", 1);
        $statement->execute();
        $token = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertTrue(is_array($token));

        //se criamos um registro na tabela de tokens, precisamos remover de la
        $this->delete('Tokens', [
            'token' => $response->data['token_awt'],
            'usuarioId' => 6,
            'status' => 1
        ]);
    }
}
