<?php

namespace Predocs\Controller;

use Predocs\Interface\ControllerInterface;
use Predocs\Include\Controller;
use Predocs\Class\Erro;
use Predocs\Model\User as UserModel;
use Predocs\Class\User as UserClass;

class Usuario implements ControllerInterface
{
    use Controller;

    public function index()
    {
        return "Usuario";
    }

    public function login()
    {
        $dataRequired = [
            "email",
            "password"
        ];
        $methods = ["POST"];

        if ($this->method !== "POST") {
            return (new Erro())->invalidMethod($methods);
        }

        if (empty($this->post["email"]) || empty($this->post["password"])) {
            return (new Erro())->invalidRequest("Email ou senha estão vazios", $dataRequired);
        }

        $userModel = new UserModel();
        $userClass = new UserClass();
        $email = $this->post["email"];
        $password = $this->post["password"];

        $dataUsuario = $userModel->getByEmail($email);

        if (empty($dataUsuario) || !password_verify($password, $dataUsuario["password"])) {
            return (new Erro())->invalidRequest("Email ou senha estão incorretos", $dataRequired);
        }

        $userClass->setDataUser($dataUsuario);
        $_SESSION["userId"] = $userClass->id;

        return [
            "status" => true,
            "mensagem" => "Usuário logado com sucesso",
        ];
    }
}
