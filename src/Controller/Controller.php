<?php

namespace PhpFinance\Controller;

use PDO;
use DateTime;
use DateInterval;
use PhpFinance\Lib\AuthorizationException;
use PhpFinance\Database\DbConnectionFactory;

class Controller
{
    private $delimitador = '9416485941';
    private $qtdTempoToken = 60 * 10 * 6;

    protected function makeAWT ($nome, $id) {
        $data = new DateTime();
        $data->add(new DateInterval('PT' . $this->qtdTempoToken . 'S'));
        $dataFormatada = $data->format('Y-m-d H:i:s');

        $token_awt = $this->delimitador . $nome . $this->delimitador . $id . $this->delimitador . $dataFormatada . $this->delimitador;
        $token_awt = base64_encode($token_awt);
        return $token_awt;
    }

    private function decodeAWT($token_awt)
    {
      $decoded_token = base64_decode($token_awt);
      $arrayDados = explode($this->delimitador, $decoded_token);
      return $arrayDados;
    }

    protected function validateAWT($token_awt)
    {
        $arrayDados = $this->decodeAWT($token_awt);

        $dataAtual = new DateTime();
        $dataToken = new DateTime($arrayDados[3]);

        $resultado = $dataToken->getTimestamp() - $dataAtual->getTimestamp();
        if ($resultado <= 0) {
            throw new AuthorizationException("Seu token não é mais válido! Favor Relogar!", 1);
        }

        $pdo = DbConnectionFactory::get();
        $sql = "select * from Usuarios where id = $arrayDados[2] and name like '$arrayDados[1]'";
        $statement= $pdo->prepare($sql);
        $statement->execute();
        $usuarioEncontrado = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$usuarioEncontrado) {
            throw new AuthorizationException("Usuário inválido! Favor Relogar!", 1);
        }

        $sql = "SELECT * FROM Tokens WHERE token = :token and usuarioId = :usuarioId";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":token", $token_awt);
        $statement->bindValue(":usuarioId", $usuarioEncontrado['id']);
        $statement->execute();
        $token = $statement->fetch(PDO::FETCH_ASSOC);

        if (count($token) == 0 || $token['status'] == 0) {
            throw new AuthorizationException ("Unauthorized", 1);
        }

        return $arrayDados;
    }

}
