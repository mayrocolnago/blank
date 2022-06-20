# Método

- `/api/auth/isauthed`

Verificar autenticação
-

Esta função serve para verificar se há um usuário logado no momento com o token no Cookie (ou recebido através de parâmetro **?actk=** POST ou GET

> Este método não requer parâmetros de entrada

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

