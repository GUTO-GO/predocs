<?php

class deslogado
{
    public function __autorizado($metodo)
    {
        $funcoes = [
            "login"
        ];
        if (in_array($metodo, $funcoes)) {
            return true;
        }

        return false;
    }

    public function login()
    {
        $banco = new Banco();

        $usuario = $banco->select([
            "tabela" => "usuario",
            "igual" => [
                "email" => $_POST["email"],
                "senha" => hash('sha512', $_POST["senha"]),
                "status" => "ativo"
            ]
        ]);

        return count($usuario) == 1;
    }
}
