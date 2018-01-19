Desafio Treasy

O c�digo presente dentro da pasta html foi desenvolvido e testado utilizando um Web Server (Apache).

Todos os dados apresentados como resposta da base de dados s�o disponibilizados pela Base de Dados p�blica da empresa Treasy (https://www.treasy.com.br/).

O c�digo foi desenvolvido em HTML, PHP e JavaScritp.


%%%%% Das condi��es levadas em considera��o

Como filtros s�o tomadas as seguintes decis�es (todas dentro do per�odo de semanas selecionado pelo usu�rio):

Para o problema 1:
- Contam-se todos os neg�cios presentes dentro da tabela (deals), mesmo que sejam Triagens j� deletadas.
- Contam-se todas as atualiza��es de neg�cios (tabela deals_updates), mesmo que se tratem do mesmo neg�cio, sendo atulizado diversas vezes dentro do per�odo especificado.

Para o problema 2:
- Contam-se todos os neg�cios presentes dentro da tabela (deals), por�m DESCARTAM-SE os neg�cios j� deletados.
- Contam-se todas as atualiza��es de neg�cios (tabela deals_updates), por�m s�o descartadas as atualiza��es de um mesmo neg�cios, mantendo-se somente a atualiza��o de maior prioridade (atribuindo menor prioridade para Triagem e maior prioridade para Decis�o de Compra).


%%%%% O mecanismo de execu��o>

A seguinte sequ�ncia pode ser utilizada como entendimento do mecanismo de execu��o das fun��es presente neste c�digo, ao se pressionar o bot�o de busca (da p�gina /pages/search.php):

1) Uma fun��o em JS detecta o bot�o pressionado e chama uma fun��o de valida��o (update_results)

2) Esta fun��o valida os dados impostos e realiza a chamada via Axaj da pagina db_select.php

3) Esta p�gina realiza as chamadas de base de dados (conectando-se a mesma via db_connect.php), de acordo com os filtros requeridos, assim como processa os dados recebidos, separando-os em tabelas de acordo com o problem_number (definidos como quest�es 1 e 2 do desafio)

4) Por fim, todas as vari�veis criadas s�o transmitidas via json de volta para a p�gina atual (search.php)

5) Essas vari�veis impostas dentro resposta json s�o atribuidas �s suas respectivas vari�veis dentro do JS, (linhas 219 � 222 de search.php)

6) � realizada a chamada da fun��o print_results, que imprime as tabelas em linguagem HTML, em conjunto com os dados recebidos, atribuindo estes � um DIV pr� definido

7) Ao final desta fun��o, chama-se a fun��o drawChart, que realiza o plot dos dados recebidos com a ajuda do pacote Chart.js