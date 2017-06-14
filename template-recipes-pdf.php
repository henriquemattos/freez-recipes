<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Compras da Semana</title>
    <link href="<?php echo plugin_dir_url(__FILE__); ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo plugin_dir_url(__FILE__); ?>css/freez-recipes.css" rel="stylesheet">
  </head>
  <body>
    <div class="row">
      <section class="header container">
        <h1 class="col-sm-10">Lista de Compras | Semana 05 a 09 de Junho</h1>
        <div class="col-sm-2">
          <img src="<?php echo plugin_dir_url(__FILE__); ?>img/logo-home-chefs.png" alt="Home Chefs" title="Home Chefs" />
        </div>
      </section>
      <section class="tips container">
        <h2>5 Dicas Mágicas Para Usar Melhor a Sua Lista de Compras:</h2>
        <ol>
          <li><span>Antes de usar a lista, conheça as receitas da semana e seu passo a passo. Se não for preparar o Cardápio completo, avalie e decida quais refeições irá cozinhar e lembre-se de riscar da lista os ingredientes ou quantidades que não for utilizar.</span></li>
          <li><span>Outro ponto a avaliar são os rendimentos de cada refeição. Em alguns casos, por exemplo,  a receita está planejada para render 2 porções. Se você quiser 4 porções, deve ter o dobro dos ingredientes.</span></li>
          <li><span>Verifique quais dos ingredientes já possui em casa e risque da lista antes de ir ao supermercado.Em alguns casos, você usará apenas uma fração de um ingrediente. Porém, se não tiver a fração suficiente em casa, precisará comprar o ingrediente em sua porção integral. São exemplos de ingredientes fracionados que muitas vezes você já vai ter: Azeite, Óleo, Manteiga, Sal, Pimenta do Reino...</span></li>
          <li><span>Abrindo este arquivo PDF no Adobe Acrobat Reader, você pode realçar ou riscar ingredientes selecionando o texto e clicando com o botão direito do mouse. Depois é só salvar a sua cópia editada. Dá pra inserir notas também.</span></li>
          <li><span>Defina qual é a melhor forma de levar a lista para o mercado. Você pode baixar este arquivo no seu celular ou abrir no computador e tirar uma foto da tela (eu faço isso!) ou imprimir.</span></li>
        </ol>
        <div class="col-sm-8 col-sm-offset-2">
          <h3><strong><i>VALE LEMBRAR:</i></strong> Nas primeiras semanas, as compras poderão ser maiores, em função dos ingredientes fracionados e temperos. Você vai perceber que, com o passar do tempo, começará a ter muitos deles em casa, pois sobrarão das receitas anteriores e não são perecíveis.</h3>
        </div>
      </section>
      <section class="ingredients container">
        <table class="table table-striped">
          <thead>
            <tr>
              <td class="text-center">Ingredientes</td>
              <td class="text-center">Quantidade</td>
              <td class="text-left">Medida</td>
            </tr>
          </thead>
          <tfoot></tfoot>
          <tbody>
          <?php foreach($ingredients as $list_item) : ?>
            <tr>
              <td class="text-center"><?php echo $list_item['name']; ?></td>
              <td class="text-center"><?php echo $list_item['amount']; ?></td>
              <td class="text-left"><?php echo $list_item['measure']; ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </div>
  </body>
</html>
