<?php

class Usuario
{
    public function __autorizado($metodo)
    {
        return true;
    }

    function getLinks()
    {
        return [
            [
                "tipo" => "link",
                "nome" => "Usuarios",
                "icone" => "/midia/icones/usuario.png",
                "url" => "/pages/usuarios/listar.html"
            ],
            [
                "tipo" => "link",
                "nome" => "Arquivos",
                "icone" => "/midia/icones/pasta.png",
            ]
        ];
    }

    function listar()
    {
        $banco = new Banco();

        return $banco->select([
            "tabela" => "usuario"
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
            return [
                "status" => false,
                "code" => "campos",
                "msg" => $empty["msg"]
            ];
        }

        $usuario = $banco->select([
            "tabela" => "usuario",
            "igual" => [
                "email" => $_POST["email"]
            ]
        ]);

        if (!empty($usuario)) {
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
