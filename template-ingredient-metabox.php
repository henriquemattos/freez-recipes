<div id="freez-recipes-metaboxes">
  <?php if(count($postmeta) > 0) : ?>
    <?php foreach($postmeta as $ingredient) : ?>
      <div class="freez-recipes-ingredients">
        <label class="screen-reader-text" for="recipe-ingredient-name">Adicionar novo ingrediente</label>
        <input type="text" name="ingredient[]" class="recipe-ingredient-input" placeholder="Ingrediente" value="<?php echo $ingredient->ingredient; ?>" />
        <input type="number" min="0" max="99999" step="0.1" name="amount[]" placeholder="Qntd." size="3" value="<?php echo $ingredient->amount; ?>" />
        <select name="measure[]">
        <?php foreach($measures as $index => $unit) : ?>
          <option value="<?php echo $unit; ?>" <?php if($unit === $ingredient->measure){ echo 'selected="selected"'; } ?>><?php echo $unit; ?></option>
        <?php endforeach; ?>
        </select>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <div class="freez-recipes-ingredients">
    <label class="screen-reader-text" for="recipe-ingredient-name">Adicionar novo ingrediente</label>
    <input type="text" name="ingredient[]" class="recipe-ingredient-input" placeholder="Ingrediente" />
    <input type="number" min="0" max="99999" step="0.1" name="amount[]" placeholder="Qntd." size="3" />
    <select name="measure[]">
    <?php foreach($measures as $index => $unit) : ?>
      <option value="<?php echo $unit; ?>"><?php echo $unit; ?></option>
    <?php endforeach; ?>
    </select>
  </div>
</div>
<div>
  <button id="freez-recipes-add-ingredient" type="button" class="button">Novo Ingrediente</button>
</div>
