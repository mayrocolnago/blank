# Método

- `/api/auth/setuser`

Salvar uma informação no usuário
-

Salvar qualquer informação/configuração no usuário para obter via **getuser** posteriormente. Em caso de sucesso retorna **true** em **result** e o valor em **data**

# Request

| Parâmetro | Tipo |
| ------ | ------ |
| chave | string |

# Response

```json
  {
    "result":"integer",
    "data":[],
    "policy":"NULL",
    "header":"NULL",
    "page":"integer",
    "state":"integer"
  }
```

