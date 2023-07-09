<?php

/** ARQUIVO PARA FUNÇÕES DA APLICAÇÃO */

function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}