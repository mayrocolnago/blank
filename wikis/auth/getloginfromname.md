# Método

- `/api/auth/getloginfromname`

Sugestão de nome válido de usuário
-

Obter uma sugestão válida de nome de usuário de autenticação com base em um nome completo (Ex. João Maria da Silva, irá retornar **joaosilva** se estiver disponível)

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| name | string |

# Response

```json
  {
    "result":"string",
    "policy":"NULL",
    "header":"NULL",
    "state":"integer"
  }
```

