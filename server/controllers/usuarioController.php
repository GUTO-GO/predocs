<?php

class Usuario
{
    public $options = [
        "status" => [
            "ativo", "inativo", "banido"
        ],
        "tipo" => [
            "usuario", "admin"
        ]
    ];

    /**
     * Função para Listar os usuaris da base
     * @version 2.0.0
     * @access public
     * @method GET
     * @param int $id - id do usuario que deseja retornar os dados
     * @return array - array com dados de usuarios
     */
    public function listar($id, $interno = false)
    {
        // Validação da requisição
        if (!$interno) {
            $validationResult = validateRequest(
                allowedMethods: ["GET"],
                auth: true
            );
            if ($validationResult["error"]) {
                return $validationResult["data"];
            }
        }

        //Inicia variaveis
        $banco = new Banco();
        $dados = [
            "tabela" => "usuario",
            "campos" => [
                "id", "nome", "email", "status",
                "tipo", "criado", "modificado"
            ]
        ];

        //valida se veio id
        if (empty($id)) {
            //Lista os usuarios
            return returnData("success", [
                "message" => "Dados listados",
                "description" => "Lista de todos os usuarios da plataforma",
                "data" => [
                    "usuarios" => $banco->select($dados),
                ],
            ]);
        } else {
            //Pesquisa usuario por id
            if (is_numeric($id)) {
                $dados["where"] = ["id" => $id];
                return returnData("success", [
                    "message" => "Dados listados",
                    "description" => "Dados do usuario com o id igual a $id",
                    "data" => [
                        "usuario" => $banco->select($dados)[0],
                    ],
                ]);
            } else {
                return returnData("invalid_fields", [
                    "message" => "O id enviado não é valido",
                    "description" => "O campo 'id' enviado não é formato numérico",
                ]);
            }
        }
    }

    /**
     * Função para cadastrar um novo usuario
     * @version 2.0.0
     * @access public
     * @method POST
     */
    public function cadastrar()
    {
        // Validação da requisição
        $validationResult = validateRequest(
            allowedMethods: ["POST"],
            requiredFields: ["nome", "sobrenome", "email", "senha"],
            auth: true
        );
        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        // Validação do email
        if (!validaEmail($_POST["email"])) {
            return returnData("invalid_fields", [
                "message" => "O endereço de email é inválido",
                "description" => "O endereço de email não é válido para ser cadastrado no sistema",
            ]);
        }

        // Verifica se o usuário já existe
        $banco = new Banco;
        $usuario = $banco->select([
            "tabela" => "usuario",
            "igual" => [
                "email" => $_POST["email"]
            ]
        ]);

        if (!empty($usuario)) {
            return returnData("conflict", [
                "message" => "Usuário já cadastrado",
                "description" => "O endereço de email fornecido para cadastro já existe na base de dados",
            ]);
        }

        // Inserção do usuário
        $id = $banco->insert([
            "nome" => $_POST["nome"] . " " . $_POST["sobrenome"],
            "email" => $_POST["email"],
            "senha" => hash('sha512', $_POST["senha"]),
            "status" => "ativo",
            "tipo" => "usuario"
        ], "usuario");

        $dataUsuario = $this->listar($id, true);
        if ($dataUsuario["status"]) {
            return returnData("success", [
                "message" => "Usuário Cadastrado",
                "description" => "O cadastro do usuário foi concluído com sucesso",
                "data" => [
                    "usuario" => $dataUsuario["data"]["usuario"],
                ],
            ]);
        } else {
            return $dataUsuario;
        }
    }

    /**
     * Lista dados do usuario logado
     * @version 1.0.0
     * @access public
     * @method GET
     */
    public function eu()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["logado"]) {
            $id = $_SESSION["usuario"]["data"]["id"];
        } else {
            $usuario = autenticaUsuario();
            if ($usuario) {
                $id = $usuario["id"];
            }
        }
        return $this->listar($id ?? null);
    }

    /**
     * Função para atualizar dados de um usuario
     * @version 2.0.0
     * @access public
     * @method PUT|PATCH
     * @return array
     */
    public function atualizar($id = 0)
    {
        // Validação da requisição
        $validationResult = validateRequest(
            allowedMethods: ["PUT", "PATCH"],
            auth: true
        );
        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        if ($_SERVER["REQUEST_METHOD"] == "PUT") {
            return $this->atualizarPUT($id);
        } elseif ($_SERVER["REQUEST_METHOD"] == "PATCH") {
            return $this->atualizarPATCH($id);
        }
    }

    /**
     * Atualiza todos os dados de um usuario
     * @version 2.0.0
     * @access private
     * @method PUT
     * @return array
     */
    private function atualizarPUT($id)
    {
        // Validação da requisição
        $validationResult = validateRequest(
            allowedMethods: ["PUT"],
            requiredFields: ["nome", "email", "senha", "status", "tipo"],
            auth: true
        );
        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        return $this->atualizarPATCH($id, true);
    }

    /**
     * Atualiza alguns dados de um usuario
     * @version 2.0.0
     * @access private
     * @method PUT
     * @return array
     */
    private function atualizarPATCH($id, $interno = false)
    {
        if (!$interno) {
            // Validação da requisição
            $validationResult = validateRequest(
                allowedMethods: ["PATCH"],
                auth: true
            );
            if ($validationResult["error"]) {
                return $validationResult["data"];
            }
        }

        //inicia variaveis
        $dados = $_POST;
        $salvar = [];
        $banco = new Banco;

        //Valida se o campo nome está correto
        if (!empty($dados["nome"])) {
            $salvar["nome"] = $dados["nome"];
        }

        //valida se o campo senha está correto
        if (!empty($dados["senha"])) {
            $salvar["senha"] = hash('sha512', $dados["senha"]);
        }

        //Valida se o status está correto
        if (!empty($dados["status"])) {
            if (in_array($dados["status"], $this->options["status"])) {
                $salvar["status"] = $dados["status"];
            } else {
                return returnData("invalid_fields", [
                    "message" => "Status invalido",
                    "description" => "O status atual não é valido",
                ]);
            }
        }

        //valida se o tipo está correto
        if (!empty($dados["tipo"])) {
            if (in_array($dados["tipo"], $this->options["tipo"])) {
                $salvar["tipo"] = $dados["tipo"];
            } else {
                return returnData("invalid_fields", [
                    "message" => "Tipo invalido",
                    "description" => "O Tipo atual não é valido",
                ]);
            }
        }

        //Valida se tem email
        if (!empty($dados["email"])) {
            //Valida se o email é valido
            if (!validaEmail($dados["email"])) {
                return returnData("invalid_fields", [
                    "message" => "Email invalido",
                    "description" => "O email atual não é valido",
                ]);
            }

            //Pesquisa novo email na base
            $usuario = $banco->select([
                "tabela" => "usuario",
                "where" => "email = '" . $dados["email"] . "' AND id NOT IN($id)"
            ]);
            if (!empty($usuario)) {
                return returnData("conflict", [
                    "message" => "email já cadastrado",
                    "description" => "O endereço de email fornecido existe na base de dados",
                ]);
            }

            $salvar["email"] = $dados["email"];
        }

        $banco->update(
            "usuario",
            $salvar,
            ["id" => $id]
        );

        $dataUsuario = $this->listar($id, true);
        if ($dataUsuario["status"]) {
            return returnData("success", [
                "message" => "Usuário Editado",
                "description" => "Os dados do usuário foram atualizados com sucesso",
                "data" => [
                    "usuario" => $dataUsuario["data"]["usuario"],
                ],
            ]);
        } else {
            return $dataUsuario;
        }
    }
}
