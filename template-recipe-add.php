<div class="container-fluid">
<h2>Adicionar Receita</h2>
<div id="alert"></div>
<form id="set_recipes" class="form-horizontal">
  <div class="form-group">
    <label for="recipe-title" class="col-sm-2 control-label">Título da Receita:</label>
    <div class="col-sm-10">
      <input name="recipe-title" type="text" class="form-control" placeholder="Título da Receita">
    </div>
  </div>
  <div class="form-group">
    <label for="recipe-description" class="col-sm-2 control-label">Descrição:</label>
    <div class="col-sm-10">
      <textarea name="recipe-description" rows="5" class="form-control" placeholder="Descrição da Receita"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="recipe-ingredient" class="col-sm-2 control-label">Ingredientes:</label>
    <div class="col-sm-10">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Item</th>
            <th colspan="2">Quantidade</th>
        </thead>
        <tfoot></tfoot>
        <tbody>
          <tr>
            <td class="col-sm-7">
              <select name="recipe-ingredient[][ingredient]" class="form-control input-recipe-ingredient">
                <?php if(count($results) > 0) : ?>
                  <?php foreach($results as $ingredient) : ?>
                    <option value="<?php echo $ingredient->id_ingredients; ?>"><?php echo $ingredient->name; ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </td>
            <td class="col-sm-2"><input name="recipe-ingredient[][amount]" type="text" class="form-control input-recipe-amount" placeholder="0" /></td>
            <td class="col-sm-1 text-center"><button type="button" class="btn btn-success btn-add-recipe"><span class="glyphicon glyphicon-plus"></span></button>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="form-group">
    <label for="recipe-instructions" class="col-sm-2 control-label">Modo de Preparo:</label>
    <div class="col-sm-10">
      <textarea name="recipe-instructions" rows="5" class="form-control" placeholder="Modo de Preparo"></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-info">Cadastrar Receita</button>
    </div>
  </div>
</form>
</div>
