# Método

- `/api/auth/login`

Fazer login
-

Função nativa responsável pela autenticação do usuário e obtenção de token. Via POST com os parâmetros **login** e **passw** é capaz de performar um login

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| login | string |
| passw | string |

# Response

```json
  {
    "result":"boolean",
    "uid":"integer",
    "token":"string",
    "policy":"NULL",
    "header":"NULL",
    "state":"integer"
  }
```

