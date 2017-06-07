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
    <input type="text" class="recipe-ingredient-input" placeholder="Ingrediente" />
    <input type="text" name="amount[]" placeholder="Qntd." size="3" />
    <select name="measure[]">
    <?php foreach($measures as $index => $unit) : ?>
      <option value="<?php echo $unit; ?>"><?php echo $unit; ?></option>
    <?php endforeach; ?>
    </select>
  </p>
</div>
<p><button id="freez-recipes-add-ingredient" type="button" class="button">Novo Ingrediente</button></p>
