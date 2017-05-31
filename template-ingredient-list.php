<div class="container-fluid">
  <h2>Lista de Ingredientes</h2>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Item</th>
      </tr>
    </thead>
    <tfoot></tfoot>
    <tbody>
    <?php if(count($results) === 0) : ?>
      <tr>
        <td>Nenhum ingrediente cadastrado.</td>
      </tr>
    <?php else : ?>
      <?php foreach($results as $ingredient) : ?>
        <tr>
          <td><?php echo $ingredient->name; ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
