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
     * Função responsável por listar os usuários.
     *
     * @param int|null $id O ID do usuário a ser pesquisado (opcional).
     * @param bool $interno Define se a requisição é interna ou não (padrão: false).
     * @return array O resultado da listagem dos usuários.
     */
    public function listar($id = null, $interno = false): array
    {
        // Validação da requisição
        if (!$interno) {
            $validationResult = FuncoesApp::validateRequest(
                allowedMethods: ["GET"],
                auth: true
            );
            if ($validationResult["error"]) {
                return $validationResult["data"];
            }
        }

        // Inicia variáveis
        $banco = new Banco();
        $dados = [
            "tabela" => "usuario",
            "campos" => [
                "id", "nome", "email", "status",
                "tipo", "criado", "modificado"
            ]
        ];

        if (empty($id)) {
            // Lista todos os usuários
            $usuarios = $banco->select($dados);
            return FuncoesApp::returnData("success", [
                "message" => "Dados listados",
                "description" => "Lista de todos os usuários da plataforma",
                "data" => [
                    "usuarios" => $usuarios,
                ],
            ]);
        } else {
            // Pesquisa usuário por ID
            if (is_numeric($id)) {
                $dados["where"] = ["id" => $id];
                $usuario = $banco->select($dados)[0];
                if ($usuario) {
                    return FuncoesApp::returnData("success", [
                        "message" => "Dados listados",
                        "description" => "Dados do usuário com o ID igual a $id",
                        "data" => [
                            "usuario" => $usuario,
                        ],
                    ]);
                } else {
                    return FuncoesApp::returnData("not_found", [
                        "message" => "Usuário não encontrado",
                        "description" => "Não foi encontrado nenhum usuário com o ID igual a $id",
                    ]);
                }
            } else {
                return FuncoesApp::returnData("invalid_fields", [
                    "message" => "O ID enviado não é válido",
                    "description" => "O campo 'id' enviado não está no formato numérico",
                ]);
            }
        }
    }

    /**
     * Método responsável por cadastrar um novo usuário.
     *
     * @return array Retorna um array contendo os dados do usuário cadastrado ou uma mensagem de erro.
     */
    public function cadastrar(): array
    {
        // Validação da requisição
        $validationResult = FuncoesApp::validateRequest(
            allowedMethods: ["POST"],
            requiredFields: ["nome", "sobrenome", "email", "senha"],
            auth: true
        );
        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        // Sanitize input
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $sobrenome = filter_input(INPUT_POST, 'sobrenome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Validação do email
        if (!FuncoesApp::isEmailValid($email)) {
            return FuncoesApp::returnData("invalid_fields", [
                "message" => "O endereço de email é inválido",
                "description" => "O endereço de email não é válido para ser cadastrado no sistema",
            ]);
        }

        // Verifica se o usuário já existe
        $banco = new Banco;
        $usuario = $banco->select([
            "tabela" => "usuario",
            "igual" => [
                "email" => $email
            ]
        ]);

        if (!empty($usuario)) {
            return FuncoesApp::returnData("conflict", [
                "message" => "Usuário já cadastrado",
                "description" => "O endereço de email fornecido para cadastro já existe na base de dados",
            ]);
        }

        // Inserção do usuário
        $id = $banco->insert([
            "nome" => $nome . " " . $sobrenome,
            "email" => $email,
            "senha" => hash('sha512', $senha),
            "status" => "ativo",
            "tipo" => "usuario"
        ], "usuario");

        $dataUsuario = $this->listar($id, true);
        if ($dataUsuario["status"]) {
            return FuncoesApp::returnData("success", [
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
     * Retorna os dados do usuário atual.
     *
     * Verifica se o usuário está logado na sessão e, caso esteja, retorna o ID do usuário.
     * Caso contrário, autentica o usuário e retorna o ID se a autenticação for bem-sucedida.
     *
     * @return array|null Os dados do usuário atual ou null se não estiver logado.
     */
    public function eu(?int $id = null): array|null
    {
        if ($id !== null) {
            return $this->listar($id);
        }

        $usuario = FuncoesApp::autenticaUsuario();
        if ($usuario) {
            $id = $usuario["id"];
            return $this->listar($id);
        }

        return null;
    }

    /**
     * Método responsável por atualizar um usuário.
     *
     * @param int $id O ID do usuário a ser atualizado.
     * @return array O resultado da atualização do usuário.
     */
    public function atualizar($id = 0): array
    {
        // Validação da requisição
        $validationResult = FuncoesApp::validateRequest(
            allowedMethods: ["PUT", "PATCH"],
            auth: true
        );
        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        $requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if ($requestMethod == "PUT") {
            return $this->atualizarPUT($id);
        } elseif ($requestMethod == "PATCH") {
            return $this->atualizarPATCH($id);
        }
    }

    /**
     * Método privado para atualizar um usuário através do método PUT.
     *
     * @param int $id O ID do usuário a ser atualizado.
     * @return array|null Os dados atualizados do usuário ou null em caso de erro.
     */
    private function atualizarPUT(int $id): array|null
    {
        $allowedMethods = ["PUT"];
        $requiredFields = ["nome", "email", "senha", "status", "tipo"];
        $authRequired = true;

        $validationResult = FuncoesApp::validateRequest(
            allowedMethods: $allowedMethods,
            requiredFields: $requiredFields,
            auth: $authRequired
        );

        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        return $this->atualizarPATCH($id, true);
    }

    /**
     * Atualiza um usuário através do método PATCH.
     *
     * @param int $id O ID do usuário a ser atualizado.
     * @param bool $interno Define se a requisição é interna ou não. O valor padrão é false.
     * @return array O resultado da atualização do usuário.
     */
    private function atualizarPATCH($id, $interno = false): array
    {
        if (!$interno) {
            // Validação da requisição
            $validationResult = FuncoesApp::validateRequest(
                allowedMethods: ["PATCH"],
                auth: true
            );
            if ($validationResult["error"]) {
                return $validationResult["data"];
            }
        }

        // Inicializa variáveis
        $dados = filter_input_array(INPUT_POST);
        $salvar = [];
        $banco = new Banco;

        // Valida se o campo nome está correto
        if (!empty($dados["nome"])) {
            $salvar["nome"] = $dados["nome"];
        }

        // Valida se o campo senha está correto
        if (!empty($dados["senha"])) {
            $salvar["senha"] = hash('sha512', $dados["senha"]);
        }

        // Valida se o status está correto
        if (!empty($dados["status"])) {
            if (in_array($dados["status"], $this->options["status"])) {
                $salvar["status"] = $dados["status"];
            } else {
                return FuncoesApp::returnData("invalid_fields", [
                    "message" => "Status inválido",
                    "description" => "O status atual não é válido",
                ]);
            }
        }

        // Valida se o tipo está correto
        if (!empty($dados["tipo"])) {
            if (in_array($dados["tipo"], $this->options["tipo"])) {
                $salvar["tipo"] = $dados["tipo"];
            } else {
                return FuncoesApp::returnData("invalid_fields", [
                    "message" => "Tipo inválido",
                    "description" => "O tipo atual não é válido",
                ]);
            }
        }

        // Valida se tem email
        if (!empty($dados["email"])) {
            // Valida se o email é válido
            if (!FuncoesApp::isEmailValid($dados["email"])) {
                return FuncoesApp::returnData("invalid_fields", [
                    "message" => "Email inválido",
                    "description" => "O email atual não é válido",
                ]);
            }

            // Pesquisa novo email na base
            $usuario = $banco->select([
                "tabela" => "usuario",
                "where" => "email = '" . $dados["email"] . "' AND id NOT IN($id)"
            ]);
            if (!empty($usuario)) {
                return FuncoesApp::returnData("conflict", [
                    "message" => "Email já cadastrado",
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
            return FuncoesApp::returnData("success", [
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
