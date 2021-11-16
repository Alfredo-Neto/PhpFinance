<?php

namespace PhpFinance\Tests;

use PhpFinance\Database\DbConnectionFactory;
use PHPUnit\Framework\TestCase;

class DatabaseHelper extends TestCase
{
    protected function delete($table, $data){
        $pdo = DbConnectionFactory::get();

        $colunas = array_keys($data);
        $dados = array_values($data);

        $parte = '';
        for ($i=0; $i < count($colunas); $i++) { 
            if($i > 0){
                $parte .= " and ";
            }
            $parte .= " $colunas[$i] = :$colunas[$i]";
        }

        foreach ($colunas as $key => $coluna) { // 0x124F0
            
        }

        $sql = "delete from $table WHERE $parte";
        $statement = $pdo->prepare($sql);

        for ($i=0; $i < count($colunas); $i++) { 
            $statement->bindValue(":$colunas[$i]", $dados[$i]);
        }

        $statement->execute();
    }
}
