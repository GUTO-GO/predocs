# Cadastrar Usuário

`POST /server/usuario/cadastrar`

Este endpoint permite registrar um novo usuário no sistema.

## Requisição

A solicitação deve conter os seguintes dados no corpo da requisição, no formato JSON:

```json
{
  "nome": "Nome do Usuário",
  "sobrenome": "Sobrenome do Usuário",
  "email": "email@exemplo.com",
  "senha": "SenhaSecreta123",
  "tipo": "usuario",
  "status": "ativo",
}
```

## Campos obrigatórios

- `nome`: O nome do usuário.
- `sobrenome`: O sobrenome completo do usuário.
- `email`: O endereço de e-mail do usuário.
- `senha`: A senha do usuário (deve conter pelo menos 8 caracteres, incluindo números, letras maiúsculas e minúsculas).

## Resposta

### Sucesso

Se a solicitação for bem-sucedida, você receberá uma resposta com o status `200 OK` e os seguintes dados:

```json
{
    "status": true,
    "http_status_code": 200,
    "message": "Usuário Cadastrado",
    "description": "O cadastro do usuário foi concluido com exito",
    "data": {
        "id": 1,
        "nome": "Nome completo do Usuário ",
        "email": "email@exemplo.com",
        "status": "ativo",
        "tipo": "usuario",
        "criado": "2023-10-08 19:10:44",
        "modificado": "2023-10-08 19:10:44"
    }
}
```

### Erros

Se ocorrerem erros durante o processo de cadastro, você receberá uma resposta com o status de erro apropriado (por exemplo, `400 Bad Request`) e uma mensagem de erro explicativa.
Para mais detalhes sobre cada código de erro acese o [**Índice de Mensagens de Erro**](/docs/markdown/errors/index.md).

---
[**index**](/docs/markdown/endpoints/usuario.md)
