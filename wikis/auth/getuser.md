# Método

- `/api/auth/getuser`

Obter informações do usuário
-

Esta função obtém os dados gerais do usuário atualmente logado ou informações gerais de outro usuário passando o **id** do mesmo via parametro POST ou GET

> Este método não requer parâmetros de entrada

# Response

```json
  {
    "result":"integer",
    "data":{
      "uid":"string",
      "ativo":"string",
      "login":"string",
      "senha":"string",
      "nome":"string",
      "tel":"string",
      "email":"string",
      "configs":"array",
      "devices":"array",
      "lastseen":"integer",
      "created":"string"
    },
    "policy":"NULL",
    "header":"NULL",
    "page":"integer",
    "state":"integer"
  }
```

