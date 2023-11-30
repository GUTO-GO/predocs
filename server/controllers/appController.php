<?php

class App
{

    /**
     * Realiza o login do usuário.
     *
     * @return array Retorna os dados do usuário logado ou uma mensagem de erro.
     */
    public function login(): array
    {
        // Validação da requisição
        $validationResult = FuncoesApp::validateRequest(
            allowedMethods: ["POST"],
            requiredFields: ["email", "senha"],
            auth: false
        );

        if ($validationResult["error"]) {
            return $validationResult["data"];
        }

        $usuario = FuncoesApp::autenticaUsuario(true);

        if ($usuario) {
            $_SESSION["usuario"]["data"] = $usuario;
            $_SESSION["usuario"]["logado"] = true;

            return FuncoesApp::returnData("success", [
                "message" => "Login feito",
                "description" => "Login efetuado com sucesso",
                "data" => [
                    "usuario" => $usuario
                ],
            ]);
        }

        return FuncoesApp::returnData("nAutorizado");
    }
}
