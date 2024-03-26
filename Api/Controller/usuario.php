<?php

namespace Predocs\Controller;

use Predocs\Interface\ControllerInterface;
use Predocs\Include\Controller;

use Predocs\Class\HttpError;

use Predocs\Attributes\Method;
use Predocs\Attributes\RequiredFields;
use Predocs\Attributes\RequiredParams;

use Predocs\Model\User as UserModel;
use Predocs\Class\User as UserClass;

class Usuario implements ControllerInterface
{
    use Controller;

    public function index()
    {
        return "Usuario";
    }

    #[Method("POST")]
    #[RequiredFields([
        "email" => FILTER_VALIDATE_EMAIL,
        "password"
    ])]
    public function login()
    {
        $userModel = new UserModel();
        $userClass = new UserClass();
        $email = $this->post["email"];
        $password = $this->post["password"];

        $dataUsuario = $userModel->getByEmail($email);

        if (empty($dataUsuario) || !password_verify($password, $dataUsuario["password"])) {
            throw new HttpError("Unauthorized", ["Email ou senha estão incorretos"]);
        }

        $userClass->setDataUser($dataUsuario);
        $_SESSION["userId"] = $userClass->id;

        return [
            "status" => true,
            "mensagem" => "Usuário logado com sucesso",
        ];
    }

    #[Method("POST")]
    #[RequiredFields([
        "name",
        "email" => FILTER_VALIDATE_EMAIL,
        "password",
    ])]
    public function novo()
    {
        $userModel = new UserModel();
        $email = $this->post["email"];
        $password = $this->post["password"];
        $name = $this->post["name"];

        if ($userModel->getByEmail($email)) {
            return new HttpError("conflict", ["Email já cadastrado"]);
        }

        $id = $userModel->insert([
            "email" => $email,
            "password" => password_hash($password, PASSWORD_BCRYPT),
            "name" => $name
        ]);

        return [
            "status" => true,
            "mensagem" => "Usuário cadastrado com sucesso",
            "id" => $id
        ];
    }

    #[Method("GET")]
    public function listar()
    {
        $userModel = new UserModel();

        if (!empty($_GET["id"]) && is_numeric($_GET["id"])) {
            $data = $userModel->getById($_GET["id"]);
        } else {
            $data = $userModel->getAll();
        }

        return [
            "status" => true,
            "data" => $data
        ];
    }

    public function editar()
    {
        $id = $_GET["id"] ?? null;
        $methods = ["PUT", "PATCH"];
        $dataRequired = [
            "name",
            "email",
            "password",
        ];

        if (!in_array($this->method, $methods)) {
            return (new Erro())->invalidMethod($methods);
        }

        if (empty($id) || !is_numeric($id)) {
            return (new Erro())->invalidRequest("Id inválido", ["id"]);
        }

        foreach ($dataRequired as $field) {
            if (empty($this->post[$field])) {
                return (new Erro())->invalidRequest("Campos obrigatórios requeridos", $dataRequired);
            }
        }

        $userModel = new UserModel();
        $user = new UserClass($id);
    }
}
