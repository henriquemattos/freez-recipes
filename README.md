# Freez Recipes
Este plugin de receitas foi desenvolvido a pedidos da [Freez Creative Studio](http://freez.com.br/) para o portal [HomeChefs](http://homechefs.com.br/).

## Como usar

### Shortcodes
+ Para listar todas as receitas: `[freezrecipes]`.
+ Para listar uma receita específica: `[freezrecipes id=123]`.
+ Para listar várias receitas: `[freezrecipes id=123,124,125,126,265,275]`.
+ Para listar receitas com links: `[freezrecipes id=123 link="true"]`.
+ Para listar receitas com checkbox e botão de impressão: `[freezrecipes id=123,124,125 checkbox="true"]`.
+ Para listar receitas com checkbox, botão de impressão e links: `[freezrecipes id=123,124,125 checkbox="true" link="true"]`.
+ Para listar receitas com paginação `[freezrecipes perpage="10"]`.
+ Para listar receitas com ordenação `[freezrecipes order="DESC" orderby="date"]`.
⋅⋅⋅ Valores para order: ASC | DESC
⋅⋅⋅ Valores para orderby: title | date | modified | rand
⋅⋅⋅ Paginação e Ordenação não funcionam combinados com IDs.