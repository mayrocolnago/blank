# Método

- `/api/auth/createaccount`

Criar usuário
-

Criar um usuário no sistema. Este método aceita uma infinita possibilidade de parametrôs e salva todos recebidos via REQUEST nas informações do usuário. Retorna o novo **UID** no **result**

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| fullname | string |
| login | string |
| password | string |
| email | string |
| noautologin | string |
| moreinfo | string |
| anotherinfo | string |

# Response

```json
  {
    "result":"string",
    "policy":"NULL",
    "header":"NULL",
    "state":"integer"
  }
```

