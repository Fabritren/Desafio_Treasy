Desafio Treasy

O código presente dentro da pasta html foi desenvolvido e testado utilizando um Web Server (Apache).

Todos os dados apresentados como resposta da base de dados são disponibilizados pela Base de Dados pública da empresa Treasy (https://www.treasy.com.br/).

O código foi desenvolvido em HTML, PHP e JavaScritp.


%%%%% Das condições levadas em consideração

Como filtros são tomadas as seguintes decisões (todas dentro do período de semanas selecionado pelo usuário):

Para o problema 1:
- Contam-se todos os negócios presentes dentro da tabela (deals), mesmo que sejam Triagens já deletadas.
- Contam-se todas as atualizações de negócios (tabela deals_updates), mesmo que se tratem do mesmo negócio, sendo atulizado diversas vezes dentro do período especificado.

Para o problema 2:
- Contam-se todos os negócios presentes dentro da tabela (deals), porém DESCARTAM-SE os negócios já deletados.
- Contam-se todas as atualizações de negócios (tabela deals_updates), porém são descartadas as atualizações de um mesmo negócios, mantendo-se somente a atualização de maior prioridade (atribuindo menor prioridade para Triagem e maior prioridade para Decisão de Compra).


%%%%% O mecanismo de execução>

A seguinte sequência pode ser utilizada como entendimento do mecanismo de execução das funções presente neste código, ao se pressionar o botão de busca (da página /pages/search.php):

1) Uma função em JS detecta o botão pressionado e chama uma função de validação (update_results)

2) Esta função valida os dados impostos e realiza a chamada via Axaj da pagina db_select.php

3) Esta página realiza as chamadas de base de dados (conectando-se a mesma via db_connect.php), de acordo com os filtros requeridos, assim como processa os dados recebidos, separando-os em tabelas de acordo com o problem_number (definidos como questões 1 e 2 do desafio)

4) Por fim, todas as variáveis criadas são transmitidas via json de volta para a página atual (search.php)

5) Essas variáveis impostas dentro resposta json são atribuidas às suas respectivas variáveis dentro do JS, (linhas 219 à 222 de search.php)

6) É realizada a chamada da função print_results, que imprime as tabelas em linguagem HTML, em conjunto com os dados recebidos, atribuindo estes à um DIV pré definido

7) Ao final desta função, chama-se a função drawChart, que realiza o plot dos dados recebidos com a ajuda do pacote Chart.js