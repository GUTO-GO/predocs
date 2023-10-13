<?php

class App
{
    public function login()
    {
        // Validação da requisição
        $validationResult = validateRequest(
            allowedMethods: ["POST"],
            requiredFields: ["email", "senha"],
            auth: false
        );
        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        if (autenticaUsuario(true)) {
            $usuario = $_SESSION["dataUsuario"];
            unset($usuario["senha"]);
            return returnData("success", [
                "message" => "Login feito",
                "description" => "Login efetuado com sucesso",
                "data" => [
                    "usuario" => $usuario
                ],
            ]);
        } else {
            return returnData("nAutorizado");
        }
    }
}
