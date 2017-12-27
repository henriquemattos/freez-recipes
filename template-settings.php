<h1>Freez Recipes - Configurações de Receitas</h1>
<blockquote cite="https://github.com/visualworks/freez-recipes/">
  <h2>Como utilizar os shortcodes</h2>
  <table class="wp-list-table widefat fixed striped">
  <thead>
    <tr>
      <th>Descrição</th>
      <th>Exemplo</th>
      <th>Observação</th>
    </tr>
  </thead>
  <tfoot></tfoot>
  <tbody>
    <tr>
      <td>Para listar todas as receitas</td>
      <td>[freezrecipes]</td>
      <td></td>
    </tr>
    <tr>
      <td>Para listar uma receita específica</td>
      <td>[freezrecipes id=123]</td>
      <td></td>
    </tr>
    <tr>
      <td>Para listar várias receitas</td>
      <td>[freezrecipes id=123,124,125,126,265,275]</td>
      <td>IDs não possuem aspas (").</td>
    </tr>
    <tr>
      <td>Para listar receitas com links</td>
      <td>[freezrecipes id=123 link="true"]</td>
      <td>Link funciona com qualquer combinação de shortcode.</td>
    </tr>
    <tr>
      <td>Para listar receitas com checkbox e botão de impressão</td>
      <td>[freezrecipes id=123,124,125 checkbox="true"]</td>
      <td>Checkbox funciona com qualquer combinação de shortcode.</td>
    </tr>
    <tr>
      <td>Para listar receitas com checkbox, botão de impressão e links</td>
      <td>[freezrecipes id=123,124,125 checkbox="true" link="true"]</td>
      <td></td>
    </tr>
    <tr>
      <td>Para listar receitas com paginação</td>
      <td>[freezrecipes perpage="10"]</td>
      <td>Paginação e Ordenação não funcionam combinados com IDs.</td>
    </tr>
    <tr>
      <td>Para listar receitas com ordenação</td>
      <td>[freezrecipes order="ASC" orderby="title"]</td>
      <td>Valores para order <strong>ASC</strong> e <strong>DESC</strong> e para orderby <strong>title</strong>, <strong>date</strong>, <strong>modified</strong> e <strong>rand</strong>.</td>
    </tr>
    <tr>
      <td>Para listar receitas por categoria usando o slug</td>
      <td>[freezrecipes category="receitas-praticas"]</td>
      <td></td>
    </tr>
    <tr>
      <td>Para listar receitas de várias categorias usando o slug</td>
      <td>[freezrecipes category="receitas-praticas,receitas-complicadas"]</td>
      <td>Não utilizar espaços entre as vírgulas.</td>
    </tr>
  </tbody>
</table>
</blockquote>