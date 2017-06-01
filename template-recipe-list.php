<div class="container-fluid">
  <h2>Lista de Receitas</h2>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-sm-1">ID</th>
        <th>Título</th>
      </tr>
    </thead>
    <tfoot></tfoot>
    <tbody>
    <?php if(count($results) === 0) : ?>
      <tr>
        <td>0</td>
        <td>Nenhuma receita cadastrada</td>
      </tr>
    <?php else : ?>
      <?php foreach($results as $recipe) : ?>
        <tr>
          <td><?php echo $recipe->id_recipes; ?></td>
          <td><?php echo $recipe->title; ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
