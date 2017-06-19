<h1>Freez Recipes - Configurações de Receitas</h1>
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
