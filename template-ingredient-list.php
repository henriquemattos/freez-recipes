<div class="container-fluid">
  <h2>Lista de Ingredientes</h2>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-sm-1">ID</th>
        <th>Item</th>
      </tr>
    </thead>
    <tfoot></tfoot>
    <tbody>
    <?php if(count($results) === 0) : ?>
      <tr>
        <td>0</td>
        <td>Nenhum ingrediente cadastrado.</td>
      </tr>
    <?php else : ?>
      <?php $i = 1; ?>
      <?php foreach($results as $ingredient) : ?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $ingredient->name; ?></td>
        </tr>
        <?php $i++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
