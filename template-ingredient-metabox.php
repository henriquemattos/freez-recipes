<div id="freez-recipes-metaboxes">
  <?php if(count($ingredients) > 0) : ?>
    <?php foreach($ingredients as $key => $value) : ?>
      <p class="freez-recipes-ingredients">
        <label class="screen-reader-text" for="recipe-ingredient-name">Adicionar novo ingrediente</label>
        <input type="text" name="ingredient[]" class="newtag form-input-tip ui-autocomplete-input" value="<?php echo $key; ?>" />
        <input type="text" name="amount[]" class="newtag form-input-tip ui-autocomplete-input" value="<?php echo $value; ?>" />
      </p>
    <?php endforeach; ?>
  <?php endif; ?>
  <p class="freez-recipes-ingredients">
    <label class="screen-reader-text" for="recipe-ingredient-name">Adicionar novo ingrediente</label>
    <input type="text" name="ingredient[]" class="newtag form-input-tip ui-autocomplete-input" placeholder="Ingrediente" />
    <input type="text" name="amount[]" class="newtag form-input-tip ui-autocomplete-input" placeholder="Quantidade" />
  </p>
</div>
<p><button id="freez-recipes-add-ingredient" type="button" class="button">Novo Ingrediente</button></p>
<script type="text/javascript">
jQuery(document).ready(function(){
  jQuery('#freez-recipes-add-ingredient').on('click', function(){
    var newRow = jQuery('#freez-recipes-metaboxes .freez-recipes-ingredients').first().clone();
    newRow.find('input[type=text]').val('');
    newRow.appendTo(jQuery('#freez-recipes-metaboxes'));
  });
});
</script>
