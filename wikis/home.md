# Observações Gerais

Este é um documentador gerado de forma automática com base nas chamadas de API no contexto de desenvolvimento. Portanto, algumas APIs podem diferenciar seus parâmetros de entrada ou respostas de saída

Para acessar a documentação das APIs aceder ao [índice](_sidebar) no menu lateral

# Padrões de retorno

Toda API do **Backend** retorna como padrão os seguintes parâmetros:

```json
{
  "result": integer,  // resultado do retorno
  "state":  1,        // resultado padrão de conexão
  "header": null,     // retorno do header Content-Type
  "policy": null,     // retorno do header CORS
  "page":   integer,  // página atual da listagem/paginação
  "data":   ...       // os dados retornados (array boolean ou text)
}
```

Parâmetro **result**:

| **result** | Descrição |
| ------ | ------ |
| -400 | Falta parâmetros |
| -401 | Autenticação falhou/indisponível |
| -403 | Não tem acesso à esta área |
| -404 | Método não encontrado |
| -405 | Método não permitido |
| -406 | Método não aceito neste contexto |
| -407 | Falta token de autenticação |
| -408 | Timeout/Tempo expirado de execução |
| -409 | Conflito/Método não completado |
| -411 | Parâmetros inválidos |
| -500 | Erro interno de comunicação |
| -501 | Método não implementado |
| -502 | Função estática não pode ser alcançada |
| -503 | Banco de dados do cliente não pode ser alcançado |
| -504 | Recurso externo demorou demais para responder |
| -505 | Problemas com o armazenamento de dados |
| [-2 ... < -9] | Erros específicos do método/api, útil para identificar momento de corte |
| -1 | Falha ao executar à ação, motivos diversos |
| 0 | Retorno nulo |
| 1 | Retorno bem sucedido |
| [2 ... > 10] | Retorno bem sucedido com a quantidade de resultados retornados |


Demais parâmetros:

| Parâmetro | Descrição |
| ------ | ------ |
| state | Padrão: 1. Para identificar que a conexão foi bem sucedida com o servidor/API |
| header | Identificativo aplicador de `header("Content-Type: application/json")`. Normalmente null ou false caso não aplicado |
| policy | Identificativo aplicador de `header("Access-Control-Allow-Origin: *")`. Normalmente null ou false caso não aplicado |
| page | Página atual cujo o qual a API está filtrando os resultados `?page=1` |
| data | Parâmetro de retorno dos dados |
