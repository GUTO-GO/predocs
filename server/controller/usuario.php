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

    public function novo()
    {
        $dataRequired = [
            "name",
            "email",
            "password",
        ];
        $methods = ["POST"];

        if ($this->method !== "POST") {
            return (new Erro())->invalidMethod($methods);
        }

        if (empty($this->post["email"]) || empty($this->post["password"]) || empty($this->post["name"])) {
            return (new Erro())->invalidRequest("Campos obrigatórios requeridos", $dataRequired, ["cost" => 100]);
        }

        $userModel = new UserModel();
        $email = $this->post["email"];
        $password = $this->post["password"];
        $name = $this->post["name"];

        if ($userModel->getByEmail($email)) {
            return (new Erro())->invalidRequest("Email já cadastrado", $dataRequired);
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

    public function listar()
    {
        $methods = ["GET"];

        if ($this->method !== "GET") {
            return (new Erro())->invalidMethod($methods);
        }

        $userModel = new UserModel();

        return [
            "status" => true,
            "data" => $userModel->getAll()
        ];
    }
}
