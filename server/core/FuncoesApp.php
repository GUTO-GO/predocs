<?php

/** ARQUIVO PARA FUNÇÕES DA APLICAÇÃO */

function validaEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function autenticaUsuario($dadosPost = false)
{
    $username = $password = $hashedPassword = null;

    //valida de onde vem o usuario e a senha
    if ($dadosPost) {
        $username = $_POST["email"];
        $password = $_POST["senha"];
    } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/^basic/i', $_SERVER['HTTP_AUTHORIZATION'])) {
        list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    } elseif (isset($_SESSION["usuario"]) && isset($_SESSION["usuario"]["logado"])) {
        return $_SESSION["usuario"]["data"];
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

function returnData($tipo, $dados = null)
{
    $funcoes = new Funcoes();
    $response = [
        "docs" => $funcoes->getLinksDocs(),
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
            $response["data"] = $dados['data']; // Supondo que $dados contenha os dados do usuário recém-cadastrado
            break;

        default:
            $response["http_status_code"] = 200;
            $response["status"] = true;
    }

    $funcoes->setStatusCode($response["http_status_code"]);
    return $response;
}

function validateRequest($allowedMethods = [], $requiredFields = [], $auth = true)
{
    $funcoes = new Funcoes();

    $currentMethod = $_SERVER["REQUEST_METHOD"];

    if ($allowedMethods && !in_array($currentMethod, $allowedMethods)) {
        return [
            "error" => true,
            "data" => returnData("method_not_allowed", $allowedMethods)
        ];
    }

    if ($requiredFields) {
        $empty = $funcoes->empty($requiredFields);
        if ($empty["status"]) {
            return [
                "error" => true,
                "data" => returnData("bad_request", $empty["msg"])
            ];
        }
    }

    if ($auth && !autenticaUsuario()) {
        return [
            "error" => true,
            "data" => returnData("nAutorizado")
        ];
    }

    return [
        "error" => false
    ];
}
