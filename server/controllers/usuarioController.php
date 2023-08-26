<?php

class Usuario
{
    public function __autorizado($metodo)
    {
        return true;
    }

    function listar()
    {
        $banco = new Banco();

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
     * @return array ['
     *      "status" => boolean
     *      "msg" => string
     *      "code" => string //codigo da resposta
     * ']
     */
    function cadastrar()
    {
        $banco = new Banco;
        $funcoes = new funcoes();

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
