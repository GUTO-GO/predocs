<?php

/** ARQUIVO PARA FUNÇÕES DA APLICAÇÃO */

class FuncoesApp
{

    /**
     * Valida um endereço de email.
     *
     * @param string $email O endereço de email a ser validado.
     * @return bool Retorna true se o endereço de email for válido, caso contrário retorna false.
     */
    public static function isEmailValid($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Autentica o usuário com base nos dados fornecidos.
     *
     * @param bool $dadosPost Os dados do usuário enviados via POST.
     * @return bool|array Retorna os dados do usuário autenticado ou false caso a autenticação falhe.
     */
    public static function autenticaUsuario($dadosPost = false): bool|array
    {
        $username = $password = $hashedPassword = null;

        //valida de onde vem o usuario e a senha
        if ($dadosPost) {
            $username = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'senha', FILTER_DEFAULT, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        } elseif (filter_input(INPUT_SERVER, 'PHP_AUTH_USER') && filter_input(INPUT_SERVER, 'PHP_AUTH_PW')) {
            $username = filter_input(INPUT_SERVER, 'PHP_AUTH_USER');
            $password = filter_input(INPUT_SERVER, 'PHP_AUTH_PW');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION') && preg_match('/^basic/i', filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION'))) {
            list($username, $password) = explode(':', base64_decode(substr(filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION'), 6)));
        } elseif (isset($_SESSION["usuario"], $_SESSION["usuario"]["logado"])) {
            return $_SESSION["usuario"]["data"];
        } else {
            return false;
        }

        if (empty($username) || empty($password)) {
            return false;
        }

        //inicia variaveis
        $banco = new Banco();
        $hashedPassword = $hashedPassword ?? hash('sha512', $password);

        //pesquisa usuario
        $usuario = $banco->select([
            "tabela" => "usuario",
            "igual" => [
                "email" => $username,
                "senha" => $hashedPassword,
                "status" => "ativo"
            ]
        ]);

        //caso usuario exista
        if ($usuario) {
            return $usuario[0];
        } else {
            return false;
        }
    }

    /**
     * Retorna um array com os dados de resposta para diferentes tipos de requisição.
     *
     * @param string $tipo O tipo de requisição.
     * @param array|null $dados Os dados opcionais para a requisição.
     * @return array O array de resposta com os dados correspondentes ao tipo de requisição.
     */
    public static function returnData($tipo, $dados = null): array
    {
        $response = [
            "docs" => Funcoes::getLinksDocs()
        ];

        switch ($tipo) {
            case "bad_request":
                $response["http_status_code"] = 400;
                $response["message"] = "Dados obrigatórios não enviados";
                $response["description"] = $dados;
                break;

            case "invalid_fields":
                $response["http_status_code"] = 400;
                $response["message"] = $dados["message"] ?? "Dados obrigatórios não enviados";
                $response["description"] = $dados["description"];
                break;

            case "nAutorizado":
                $response["http_status_code"] = 401;
                $response["message"] = "Não autorizado";
                $response["description"] = "Os dados de login não condizem, verifique os dados e tente novamente";
                break;

            case "method_not_allowed":
                $response["http_status_code"] = 405;
                $response["message"] = "Método de requisição inválido";
                $response["description"] = "O método de requisição utilizado não está implantado para esta função";
                $response["allowed_methods"] = $dados;
                break;

            case "conflict":
                $response["http_status_code"] = 409;
                $response["status"] = false;
                $response["message"] = $dados['message'] ?? "Usuário já cadastrado";
                $response["description"] = $dados['description'] ?? "O endereço de email fornecido para cadastro já existe na base de dados";
                break;

            case "success":
                $response["http_status_code"] = 200;
                $response["status"] = true;
                $response["message"] = $dados['message'] ?? "Usuário Cadastrado";
                $response["description"] = $dados['description'] ?? "O cadastro do usuário foi concluído com êxito";
                $response["data"] = $dados['data'];
                break;

            default:
                $response["http_status_code"] = 200;
                $response["status"] = true;
        }

        Funcoes::setStatusCode($response["http_status_code"]);
        return $response;
    }

    /**
     * Valida a requisição com base nos métodos permitidos, campos obrigatórios e autenticação.
     *
     * @param array $allowedMethods Os métodos HTTP permitidos.
     * @param array $requiredFields Os campos obrigatórios na requisição.
     * @param bool $auth Determina se a autenticação é necessária.
     * @return array O resultado da validação.
     */
    public static function validateRequest($allowedMethods = [], $requiredFields = [], $auth = true): array
    {
        $currentMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

        if ($allowedMethods && !in_array($currentMethod, $allowedMethods)) {
            return [
                "error" => true,
                "data" => static::returnData("method_not_allowed", $allowedMethods)
            ];
        }

        if ($requiredFields) {
            foreach ($requiredFields as $key => $value) {
                if (empty($value)) {
                    return [
                        "error" => true,
                        "data" => static::returnData("bad_request", "Campo $key obrigatório")
                    ];
                }
            }
        }

        if ($auth && !static::autenticaUsuario()) {
            return [
                "error" => true,
                "data" => static::returnData("nAutorizado")
            ];
        }

        return [
            "error" => false
        ];
    }
}
