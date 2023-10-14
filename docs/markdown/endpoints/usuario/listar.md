# Listar Usuário

`GET /server/usuario/listar`

Este endpoint permite listar os usuário do sistema.

## Resposta

### Sucesso

Se a solicitação for bem-sucedida, você receberá uma resposta com o status `200 OK` e os seguintes dados:

```json
{
    "status": true,
    "http_status_code": 200,
    "message": "Dados listados",
    "description": "Lista de todos os usuarios da plataforma",
    "data": {
        "usuarios": [
            {
                "id": 1,
                "nome": "Alice Silva",
                "email": "alice@example.com",
                "status": "ativo",
                "tipo": "admin",
                "criado": "2023-10-08 09:45:21",
                "modificado": "2023-10-08 09:45:21"
            },
            {
                "id": 2,
                "nome": "Carlos Oliveira",
                "email": "carlos@example.com",
                "status": "ativo",
                "tipo": "usuario",
                "criado": "2023-10-08 10:15:32",
                "modificado": "2023-10-08 10:15:32"
            }
        ]
    }
}
```

### Erros

Se ocorrerem erros durante o processo de cadastro, você receberá uma resposta com o status de erro apropriado (por exemplo, `400 Bad Request`) e uma mensagem de erro explicativa.
Para mais detalhes sobre cada código de erro acese o [**Índice de Mensagens de Erro**](/docs/markdown/errors/index.md).

---
[**index**](/docs/markdown/endpoints/usuario.md)
