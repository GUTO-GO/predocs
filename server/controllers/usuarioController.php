<?php

class Usuario
{
    public function __autorizado($metodo)
    {
        return true;
    }

    function getLinks()
    {
        return [
            [
                "tipo" => "link",
                "nome" => "Usuarios",
                "icone" => "/midia/icones/usuario.png",
                // "url" => "/pages/usuarios/listar.html"
            ],
            [
                "tipo" => "link",
                "nome" => "Arquivos",
                "icone" => "/midia/icones/pasta.png",
            ]
        ];
    }
}
