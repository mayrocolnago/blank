# Método

- `/api/auth/user_exists`

Verifica se usuário existe
-

Verificar se nome de usuário existe na base do sistema

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| username | string |

# Response

```json
  {
    "result":"boolean",
    "policy":"NULL",
    "header":"NULL",
    "state":"integer"
  }
```

