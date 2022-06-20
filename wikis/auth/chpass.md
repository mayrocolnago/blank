# Método

- `/api/auth/chpass`

Trocar senha
-

Esta função serve para alterar a senha do usuário atualmente autenticado

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| current | string |
| password | string |
| confirmation | string |

# Response

```json
  {
    "result":"string",
    "policy":"NULL",
    "header":"NULL",
    "state":"integer"
  }
```

