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

    public function __autorizado($metodo)
    {
        return true;
    }

    /**
     * Função para Listar os usuaris da base
     * @version 2.0.0
     * @access public
     * @method GET
     * @param int $id - id do usuario que deseja retornar os dados
     * @return array - array com dados de usuarios 
     */
    public function listar($id)
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

        if (empty($id)) {
            return $banco->select([
                "tabela" => "usuario",
                "campos" => [
                    "id", "nome", "email", "status",
                    "tipo", "criado", "modificado"
                ],
            ]);
        } else {
            if (is_numeric($id)) {
                return $banco->select([
                    "tabela" => "usuario",
                    "campos" => [
                        "id", "nome", "email", "status",
                        "tipo", "criado", "modificado"
                    ],
                    "where" => [
                        "id" => $id
                    ]
                ])[0];
            } else {
                $funcoes->setStatusCode(400);
                return [
                    "status" => false,
                    "code" => "id",
                    "msg" => "O id enviado não é valido!"
                ];
            }
        }
    }

    /**
     * Função para cadastrar um novo usuario
     * @version 1.0.0
     * @access public
     * @method POST
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

    /**
     * Função para atualizar dados de um usuario
     * @version 1.0.0
     * @access public
     * @method PUT|PATCH
     * @return array
     */
    public function atualizar($id = 0)
    {
        $funcoes = new Funcoes;
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "PUT":
                return $this->atualizar_PUT($id);
                break;
            case "PATCH":
                return $this->atualizar_PATCH($id);
                break;
            default:
                $funcoes->setStatusCode(405);
                return [
                    "methods" => [
                        "PUT",
                        "PATCH"
                    ]
                ];
                break;
        }
    }

    /**
     * Atualiza todos os dados de um usuario
     * @version 1.0.0
     * @access private
     * @method PUT
     * @return array
     */
    private function atualizar_PUT($id)
    {
        $funcoes = new funcoes();
        $banco = new banco();

        //Valida se os campos estão preenchidos
        $empty = $funcoes->empty([
            "nome", "email", "senha", "status", "tipo"
        ]);
        if ($empty["status"]) {
            $funcoes->setStatusCode(400);
            return [
                "status" => false,
                "code" => "campos",
                "msg" => $empty["msg"]
            ];
        }

        return $this->atualizar_PATCH($id);
    }

    /**
     * Atualiza alguns dados de um usuario
     * @version 1.0.0
     * @access private
     * @method PUT
     * @return array
     */
    private function atualizar_PATCH($id)
    {
        $dados = $_POST;
        $salvar = [];
        $funcoes = new Funcoes;
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
                $funcoes->setStatusCode(400);
                return [
                    "status" => false,
                    "code" => "status",
                    "msg" => "O status atual não é valido",
                    "opcoes" => $this->options["status"]
                ];
            }
        }

        //valida se o tipo está correto
        if (!empty($dados["tipo"])) {
            if (in_array($dados["tipo"], $this->options["tipo"])) {
                $salvar["tipo"] = $dados["tipo"];
            } else {
                $funcoes->setStatusCode(400);
                return [
                    "status" => false,
                    "code" => "tipo",
                    "msg" => "O tipo de usuario não é valido",
                    "opcoes" => $this->options["tipo"]
                ];
            }
        }

        //Valida se tem email
        if (!empty($dados["email"])) {
            //Valida se o email é valido
            if (!validaEmail($dados["email"])) {
                $funcoes->setStatusCode(400);
                return [
                    "status" => false,
                    "code" => "email",
                    "msg" => "O endereço de email não é valido"
                ];
            }

            //Pesquisa novo email na base
            $usuario = $banco->select([
                "tabela" => "usuario",
                "where" => "email = '" . $dados["email"] . "' AND id NOT IN($id)"
            ]);
            if (!empty($usuario)) {
                $funcoes->setStatusCode(409);
                return [
                    "status" => false,
                    "code" => "email",
                    "msg" => "O email já está cadastrado em outro usuario"
                ];
            }

            $salvar["email"] = $dados["email"];
        }

        $banco->update(
            "usuario",
            $salvar,
            [
                "id" => $id
            ]
        );

        return [
            "status" => true,
            "code" => "sucesso",
            "msg" => "Dados atualizado com sucesso"
        ];
    }
}
