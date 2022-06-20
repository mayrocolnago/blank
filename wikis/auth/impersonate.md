# Método

- `/api/auth/impersonate`

Personificar um usuário
-

Esta função permite ao utilizador personificar a autenticação de outro usuário. Esta função requer permissão **_admin** para execução via POST ou GET

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| uid | string |

# Response

```json
  {
    "result":"integer",
    "policy":"NULL",
    "header":"NULL",
    "state":"integer"
  }
```

