<h1>Freez Recipes - Configurações de Receitas</h1>
<blockquote cite="https://github.com/visualworks/freez-recipes/">
  <h2>Como utilizar os shortcodes</h2>
  <ul>
    <li>Para listar todas as receitas: <strong>[freezrecipes]</strong>.</li>
    <li>Para listar uma receita específica: <strong>[freezrecipes id=123]</strong>.</li>
    <li>Para listar várias receitas: <strong>[freezrecipes id=123,124,125,126,265,275]</strong>.</li>
    <li>Para listar receitas com links: <strong>[freezrecipes id=123 link="true"]</strong>.</li>
    <li>Para listar receitas com checkbox e botão de impressão: <strong>[freezrecipes id=123,124,125 checkbox="true"]</strong>.</li>
    <li>Para listar receitas com checkbox, botão de impressão e links: <strong>[freezrecipes id=123,124,125 checkbox="true" link="true"]</strong>.</li>
    <li>Para listar receitas com paginação <strong>[freezrecipes pagination="true" perpage="10"]</strong>. Paginação em desenvolvimento.</li>
  </ul>
</blockquote>
<form action="<?php echo admin_url('options-general.php?page=freez_recipes_settings'); ?>" method="POST">
  <input type="hidden" id="freez-recipes-setting" name="action" value="freez_recipes_settings" />
  <div>
    <label class="" for="freez_recipes_description">Texto descritivo do PDF e visualização da lista de compras:</label>
  </div>
  <div style="width: 95%; margin-bottom: 2em;";>
    <?php
    wp_editor($this->get_freez_recipes_settings_description(),
      'freez_recipes_settings_description',
      array(
        'media_buttons' => false,
        'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close')
      ));
    ?>
  </div>
  <div>
    <button class="button button-primary button-large">Atualizar</button>
  </div>
</form>
