<?php

class Usuario
{
    public function __autorizado($metodo)
    {
        return true;
    }

    /**
     * Função para Listar os usuaris da base
     * @version 1.0.0
     * @access public
     * @return array - array com dados de usuarios 
     */
    public function listar()
    {
        $banco = new Banco();
        $funcoes = new Funcoes;

        if ($_SERVER["REQUEST_METHOD"] != "GET") {
            $funcoes->setStatusCode(405);
            return [
                "methods" => [
                    "GET"
                ]
            ];
        }

        return $banco->select([
            "tabela" => "usuario",
            "campos" => [
                "id", "nome", "email", "status",
                "tipo", "criado", "modificado"
            ]
        ]);
    }

    /**
     * Função para cadastrar um novo usuario
     * @version 1.0.0
     * @access public
     * @return array ['
     *      "status" => boolean
     *      "msg" => string
     *      "code" => string //codigo da resposta
     * ']
     */
    public function cadastrar()
    {
        $banco = new Banco;
        $funcoes = new funcoes();

        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            $funcoes->setStatusCode(405);
            return [
                "methods" => [
                    "POST"
                ]
            ];
        }

        $empty = $funcoes->empty([
            "nome", "sobrenome", "email", "senha"
        ]);

        if ($empty["status"]) {
            $funcoes->setStatusCode(400);
            return [
                "status" => false,
                "code" => "campos",
                "msg" => $empty["msg"]
            ];
        }

        if (!validaEmail($_POST["email"])) {
            $funcoes->setStatusCode(400);
            return [
                "status" => false,
                "code" => "email",
                "msg" => "O endereço de email não é valido"
            ];
        }

        $usuario = $banco->select([
            "tabela" => "usuario",
            "igual" => [
                "email" => $_POST["email"]
            ]
        ]);

        if (!empty($usuario)) {
            $funcoes->setStatusCode(409);
            return [
                "status" => false,
                "code" => "email",
                "msg" => "O email já está cadastrado"
            ];
        }

        $banco->insert([
            "nome" => $_POST["nome"] . " " . $_POST["sobrenome"],
            "email" => $_POST["email"],
            "senha" => hash('sha512', $_POST["senha"]),
            "status" => "ativo",
            "tipo" => "usuario"
        ], "usuario");

        return [
            "status" => true,
            "code" => "sucesso",
            "msg" => "cadastro efetuado com sucesso"
        ];
    }
}
