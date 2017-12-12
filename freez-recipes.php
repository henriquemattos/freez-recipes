<?php
/*
Plugin Name: Freez Recipes
Plugin URI:  http://www.freez.com.br
Description: Create recipes, print PDF versions for customers and filter totals.
Version:     1.1.0
Author:      Freez
Author URI:  http://www.freez.com.br
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: freezrecipes
Domain Path: /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// include autoloader
require_once 'dompdf/autoload.inc.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

class Freez_Recipes {
  public function __construct(){
    add_action('init', array($this, 'freez_create_post_type'));
    add_action('init', array($this, 'freez_create_taxonomy'));
    add_action('init', array($this, 'freez_create_taxonomy_measures'));
    add_action('init', array($this, 'freez_create_taxonomy_categories'));
    add_action('init', array($this, 'freez_enqueue_scripts'));
    add_action('add_meta_boxes', array($this, 'freez_add_ingredients_metaboxes'));
    add_action('add_meta_boxes', array($this, 'freez_add_shortcode_metaboxes'));
    add_action('save_post', array($this, 'freez_save_ingredients_metaboxes'));

    add_action('wp_ajax_get_ingredients', array($this, 'get_ingredients'));
    add_action('wp_ajax_nopriv_get_ingredients', array($this, 'get_ingredients'));

    add_action('wp_ajax_freez_recipes_view', array($this, 'freez_recipes_view'));
    add_action('wp_ajax_nopriv_freez_recipes_view', array($this, 'freez_recipes_view'));

    add_action('admin_menu', array($this, 'freez_recipes_settings_menu'));
    add_action('admin_action_freez_recipes_print', array($this, 'freez_recipes_print'));
    add_action('admin_action_freez_recipes_settings', array($this, 'freez_recipes_settings'));

    add_filter('template_include', array($this, 'freez_recipes_template_include'), 1);
    add_filter('manage_posts_columns', array($this, 'freez_recipes_columns_shortcode'));
    add_action('manage_posts_custom_column', array($this, 'freez_recipes_columns_shortcode_content'), 10, 2);

    add_shortcode('freezrecipes', array($this, 'freez_recipes_shortcode'));

    register_activation_hook(__FILE__, array($this, 'install'));
    register_deactivation_hook(__FILE__, array($this, 'uninstall'));
  }
  public function freez_recipes_settings_menu(){
    add_submenu_page('options-general.php', 'Freez Recipes', 'Freez Recipes', 'manage_options', 'freez_recipes_settings', array($this, 'freez_recipes_settings'));
  }
  public function freez_recipes_settings(){
    if(isset($_POST) && isset($_POST['freez_recipes_settings_description'])){
      update_option('freez_recipes_settings_description', $_POST['freez_recipes_settings_description']);
    }
    include_once plugin_dir_path(__FILE__) . 'template-settings.php';
  }
  public function freez_enqueue_scripts(){
    wp_enqueue_script(
      'freez-recipes-js',
      plugin_dir_url(__FILE__) . 'js/default.js',
      array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-position', 'jquery-ui-widget'),
      '1.0.0',
      true
    );
    wp_localize_script('freez-recipes-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('freez_recipes_get_ingredients_nonce')));
  }
  public function install(){
    if(get_option('freez_recipes_ingredients_measures')){
      delete_option('freez_recipes_ingredients_measures');
    }
    $this->freez_recipes_flush_rules();
  }
  public function uninstall(){
    $this->freez_recipes_flush_rules();
  }
  public function freez_recipes_flush_rules(){
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
  }
  public function freez_save_ingredients_metaboxes($post_id){
    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */
    if(!isset($_POST['freez_recipes_ingredients_nonce'])){
      return $post_id;
    }

    $nonce = $_POST['freez_recipes_ingredients_nonce'];

    if (!wp_verify_nonce($nonce, 'freez_recipes_ingredients')){
      return $post_id;
    }

    /*
     * If this is an autosave, our form has not been submitted,
     * so we don't want to do anything.
     */
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
        return $post_id;
    }
    // @TODO Check the user's permissions.
    $meta_value = array();
    foreach($_POST['ingredient'] as $key => $value){
      if(isset($_POST['ingredient']) && !empty($value)){
        // Sanitize the user input.
        $ingredient = ucwords(strtolower(sanitize_text_field($value)));
        $this->add_ingredient($ingredient);
        $amount = sanitize_text_field($_POST['amount'][$key]);
        $measure = strtolower(sanitize_text_field($_POST['measure'][$key]));
        array_push($meta_value, array('ingredient' => $ingredient, 'amount' => $amount, 'measure' => $measure));
      }
    }
    // Update the meta field.
    update_post_meta($post_id, 'freez_recipes_ingredients', json_encode($meta_value, JSON_UNESCAPED_UNICODE));
  }
  public function add_ingredient($ingredient){
    if(get_term_by('name', $ingredient)){
      return true;
    } else {
      wp_insert_term($ingredient, 'freez_ingredients');
    }
  }
  public function freez_add_ingredients_metaboxes(){
    global $wp_meta_boxes;
    add_meta_box(
      'freez_recipes_ingredients',
      __('Ingredientes'),
      array($this, 'freez_ingredients_metaboxes_html'),
      'freez_recipes'
    );
  }
  public function freez_ingredients_metaboxes_html($post){
    // Add an nonce field so we can check for it later.
    wp_nonce_field('freez_recipes_ingredients', 'freez_recipes_ingredients_nonce');
    $postmeta = json_decode(get_post_meta($post->ID, 'freez_recipes_ingredients', true));
    // $measures = get_option('freez_recipes_ingredients_measures');
    $measures = $this->get_measures();
    include_once 'template-ingredient-metabox.php';
  }
  public function get_ingredients(){
    $terms = get_terms(array(
      'taxonomy'   => 'freez_ingredients',
      'hide_empty' => false
    ));
    print json_encode($terms);
    wp_die();
  }
  public function get_measures(){
    return get_terms(array(
      'taxonomy'   => 'freez_measures',
      'hide_empty' => false
    ));
  }
  public function freez_add_shortcode_metaboxes(){
    global $wp_meta_boxes;
    add_meta_box(
      'freez_recipes_shortcode',
      __('Shortcode'),
      array($this, 'freez_shortcode_metaboxes_html'),
      'freez_recipes',
      'side',
      'high'
    );
  }
  public function freez_shortcode_metaboxes_html($post){
    include_once 'template-shortcode-metabox.php';
  }
  public function freez_recipes_columns_shortcode($defaults){
    $defaults['shortcode'] = 'Shortcode';
    return $defaults;
  }
  public function freez_recipes_columns_shortcode_content($column_name, $post_ID){
    if($column_name == 'shortcode'){
      echo "[freezrecipes id=\"{$post_ID}\"]";
    }
  }
  public function freez_create_taxonomy_categories() {
    $labels = array(
  		'name'                       => _x('Categorias', 'taxonomy general name', 'freez-recipes'),
  		'singular_name'              => _x('Categoria', 'taxonomy singular name', 'freez-recipes'),
  		'search_items'               => __('Buscar categorias', 'freez-recipes'),
  		'popular_items'              => __('Categorias populares', 'freez-recipes'),
  		'all_items'                  => __('Todas as categorias', 'freez-recipes'),
  		'parent_item'                => null,
  		'parent_item_colon'          => null,
  		'edit_item'                  => __('Editar categoria', 'freez-recipes'),
  		'update_item'                => __('Atualizar categoria', 'freez-recipes'),
  		'add_new_item'               => __('Adicionar novo categoria', 'freez-recipes'),
  		'new_item_name'              => __('Nome da nova categoria', 'freez-recipes'),
  		'separate_items_with_commas' => __('Separe categorias com vírgula', 'freez-recipes'),
  		'add_or_remove_items'        => __('Adicione ou remova categorias', 'freez-recipes'),
  		'choose_from_most_used'      => __('Escolha nas categorias  mais utilizadas', 'freez-recipes'),
  		'not_found'                  => __('Nenhuma categoria encontrada.', 'freez-recipes'),
  		'menu_name'                  => __('Categorias de Receitas', 'freez-recipes'),
  	);

  	$args = array(
  		'hierarchical'          => false,
  		'labels'                => $labels,
  		'show_ui'               => true,
  		'show_admin_column'     => true,
  		'update_count_callback' => '_update_post_term_count',
  		'query_var'             => true,
      'meta_box_cb'           => false,
  		'rewrite'               => array('slug' => 'freez_categories')
  	);

  	register_taxonomy('freez_categories', 'freez_recipes', $args);
  }
  public function freez_create_taxonomy(){
  	$labels = array(
  		'name'                       => _x('Ingredientes', 'taxonomy general name', 'freez-recipes'),
  		'singular_name'              => _x('Ingrediente', 'taxonomy singular name', 'freez-recipes'),
  		'search_items'               => __('Buscar ingredientes', 'freez-recipes'),
  		'popular_items'              => __('Ingredientes populares', 'freez-recipes'),
  		'all_items'                  => __('Todos os ingredientes', 'freez-recipes'),
  		'parent_item'                => null,
  		'parent_item_colon'          => null,
  		'edit_item'                  => __('Editar ingrediente', 'freez-recipes'),
  		'update_item'                => __('Atualizar ingrediente', 'freez-recipes'),
  		'add_new_item'               => __('Adicionar novo ingrediente', 'freez-recipes'),
  		'new_item_name'              => __('Nome do novo ingrediente', 'freez-recipes'),
  		'separate_items_with_commas' => __('Separe ingredientes com vírgula', 'freez-recipes'),
  		'add_or_remove_items'        => __('Adicione ou remova ingredientes', 'freez-recipes'),
  		'choose_from_most_used'      => __('Escolha nos ingredientes mais utilizados', 'freez-recipes'),
  		'not_found'                  => __('Nenhum ingrediente encontrado.', 'freez-recipes'),
  		'menu_name'                  => __('Ingredientes', 'freez-recipes'),
  	);

  	$args = array(
  		'hierarchical'          => false,
  		'labels'                => $labels,
  		'show_ui'               => true,
  		'show_admin_column'     => true,
  		'update_count_callback' => '_update_post_term_count',
  		'query_var'             => true,
      'meta_box_cb'           => false,
  		'rewrite'               => array('slug' => 'freez_ingredients')
  	);

  	register_taxonomy('freez_ingredients', 'freez_recipes', $args);
  }
  public function freez_create_taxonomy_measures(){
  	$labels = array(
  		'name'                       => _x('Medidas', 'taxonomy general name', 'freez-recipes'),
  		'singular_name'              => _x('Medida', 'taxonomy singular name', 'freez-recipes'),
  		'search_items'               => __('Buscar medidas', 'freez-recipes'),
  		'popular_items'              => __('Medidas populares', 'freez-recipes'),
  		'all_items'                  => __('Todas as medidas', 'freez-recipes'),
  		'parent_item'                => null,
  		'parent_item_colon'          => null,
  		'edit_item'                  => __('Editar medida', 'freez-recipes'),
  		'update_item'                => __('Atualizar medida', 'freez-recipes'),
  		'add_new_item'               => __('Adicionar nova medida', 'freez-recipes'),
  		'new_item_name'              => __('Nome da nova medida', 'freez-recipes'),
  		'separate_items_with_commas' => __('Separe medidas com vírgula', 'freez-recipes'),
  		'add_or_remove_items'        => __('Adicione ou remova medidas', 'freez-recipes'),
  		'choose_from_most_used'      => __('Escolha nas medidas mais utilizadas', 'freez-recipes'),
  		'not_found'                  => __('Nenhuma medida encontrada.', 'freez-recipes'),
  		'menu_name'                  => __('Medidas', 'freez-recipes'),
  	);

  	$args = array(
  		'hierarchical'          => false,
  		'labels'                => $labels,
  		'show_ui'               => true,
  		'show_admin_column'     => true,
  		'update_count_callback' => '_update_post_term_count',
  		'query_var'             => true,
      'meta_box_cb'           => false,
  		'rewrite'               => array('slug' => 'freez_measurese')
  	);

  	register_taxonomy('freez_measures', 'freez_recipes', $args);
  }
  public function freez_create_post_type(){
    register_post_type('freez_recipes',
      array(
        'labels' => array(
          'name'          => __('Receitas'),
          'singular_name' => __('Receita'),
          'add_new'       => __('Adicionar nova'),
          'add_new_item'  => __('Adicionar nova receita'),
          'edit_item'     => __('Editar receita'),
          'new_item'      => __('Nova receita'),
          'view_item'     => __('Ver receita'),
          'view_items'    => __('Ver receitas'),
          'all_items'     => __('Todas as receitas'),
          'search_items'  => __('Buscar receitas'),
          'not_found'     => __('Nenhuma receita encontrada'),
          'archives'      => __('Arquivo de receitas')
        ),
        'description'         => __('Receitas personalizadas por Freez'),
        'public'              => true,
        'exclude_from_search' => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-clipboard',
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'receitas'),
        'support'             => array('title', 'editor', 'author', 'thumbnail', 'custom-fields', 'revisions')
      )
    );
  }
  public function freez_recipes_template_include($template_path){
    if(get_post_type() == 'freez_recipes'){
      if(is_single()){
        // checks if the file exists in the theme first,
        // otherwise serve the file from the plugin
        if($theme_file = locate_template(array('single-freez_recipes.php'))){
          $template_path = $theme_file;
        } else {
          $template_path = plugin_dir_path(__FILE__) . 'single-freez_recipes.php';
        }
      }
    }
    return $template_path;
  }
  public function freez_recipes_shortcode($atts){
    $str = '<div class="recipes-list"><form id="freez-recipes-form-view-print" action="' . admin_url('admin.php') .'" method="POST">';
    $str .= '<input type="hidden" id="freez-recipes-form-action" name="action" value="freez_recipes_print" />';
    $checkbox = '';
    $link_begin = '';
    $link_end = '';
    if(isset($atts['id'])){
      $list = explode(',', $atts['id']);
      foreach($list as $id){
        $id = strip_tags($id);
        $str .= '<article id="recipes-' . $id . '" class="recipes-' . $id . ' post type-recipe">';
        $recipe = get_post($id, 'OBJECT');
        $link = get_post_permalink($id);
        if(isset($atts['link']) && $atts['link'] !== "false"){
          $link = get_post_permalink($id);
          $link_begin = '<a href="' . $link . '">';
          $link_end = '</a>';
        }
        if(isset($atts['checkbox']) && $atts['checkbox'] !== "false"){
          $checkbox = '<input type="checkbox" value="' . $id . '" name="checkbox-recipes[]" class="freez-recipes-checkboxes" />';
        }
        $str .= "<p>{$checkbox}{$link_begin}{$recipe->post_title}{$link_end}</p>";
        $str .= '</article>';
      }
    }
    if(isset($atts['checkbox']) && $atts['checkbox'] !== "false"){
      $str .= '<div>';
      $str .= '<button id="freez-recipes-pdf-view" name="freez-recipes-pdf-view" type="button">Ver Lista</button> ';
      $str .= '<button id="freez-recipes-pdf-print" name="freez-recipes-pdf-print" type="submit">Salvar PDF</button>';
      $str .= '<p><small>* Para ver a lista de compras você deve permitir que janelas pop-ups sejam abertas pelo navegador</small></p>';
      $str .= '</div>';
    }
    $str .= '</form></div>';
    return $str;
  }
  public function freez_recipes_view(){
    $pdf_html = $this->generate_pdf_html($_POST);
    wp_send_json(array(
      'html' => $pdf_html['page1'] . $pdf_html['page2']
    ));
    wp_die();
  }
  public function freez_recipes_print(){
    $pdf_html = $this->generate_pdf_html($_POST);

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    // instantiate and use the dompdf class
    $dompdf = new Dompdf($options);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->loadHtml($pdf_html['page1'] . $pdf_html['page2']);
    //$dompdf->loadHtml($pdf_html['page2']);

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();
    // return true;
  }
  public function generate_pdf_html($post = array()){
    if(isset($post['checkbox-recipes'])){
      $ingredients = array();
      foreach($post['checkbox-recipes'] as $id){
        // $recipe = get_post($id, 'OBJECT');
        $postmeta = json_decode(get_post_meta($id, 'freez_recipes_ingredients', true));
        if(count($postmeta) > 0){
          foreach($postmeta as $item){
            if(array_key_exists($item->ingredient, $ingredients)){
              $ingredients[$item->ingredient]['amount'] = $ingredients[$item->ingredient]['amount'] + $item->amount;
            } else {
              $ingredients += array($item->ingredient => array(
                'name' => $item->ingredient,
                'amount' => $item->amount,
                'measure' => $item->measure
              ));
            }
          }
        }
      }
      $page1 = '<!DOCTYPE html>
      <html lang="en">
        <head>
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Lista de Compras da Semana</title>
          <link href="' . plugin_dir_url(__FILE__) . 'css/bootstrap.min.css" rel="stylesheet">
          <link href="' . plugin_dir_url(__FILE__) . 'css/freez-recipes.css" rel="stylesheet">
        </head>
        <body>
        <div class="container">
          <div class="row">
            <div class="header col-sm-12">
              <h1>Lista de Compras</h1>
              <img src="' . plugin_dir_url(__FILE__) . 'img/logo-home-chefs.jpg" alt="Home Chefs" title="Home Chefs" />
            </div>
          </div>
          <div class="row">
            <div class="tips col-sm-12">' . $this->get_freez_recipes_settings_description() . '</div>
          </div>';
      $page2 = '<div class="ingredients row"><div class="col-sm-12">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <td class="text-center">Ingredientes</td>
                        <td class="text-center">Quantidade</td>
                        <td class="text-left">Medida</td>
                      </tr>
                    </thead>
                    <tfoot></tfoot>
                    <tbody>';
                    foreach($ingredients as $list_item) {
                      $page2 .= '<tr>
                        <td class="text-center">' . $list_item['name'] . '</td>
                        <td class="text-center">' . $list_item['amount'] . '</td>
                        <td class="text-left">' . $list_item['measure'] . '</td>
                      </tr>';
                    }
                    $page2 .= '</tbody>
                  </table>
              </div></div>
              </div>
            </body>
          </html>';
      $html = array('page1' => $page1, 'page2' => $page2);
      return $html;
    }
  }
  public function get_freez_recipes_settings_description(){
    if($str_description = get_option('freez_recipes_settings_description')){
      return $str_description;
    } else {
      return '<h2>5 Dicas Mágicas Para Usar Melhor a Sua Lista de Compras:</h2>
      <ol>
        <li><span>Antes de usar a lista, conheça as receitas da semana e seu passo a passo. Se não for preparar o Cardápio completo, avalie e decida quais refeições irá cozinhar e lembre-se de riscar da lista os ingredientes ou quantidades que não for utilizar.</span></li>
        <li><span>Outro ponto a avaliar são os rendimentos de cada refeição. Em alguns casos, por exemplo,  a receita está planejada para render 2 porções. Se você quiser 4 porções, deve ter o dobro dos ingredientes.</span></li>
        <li><span>Verifique quais dos ingredientes já possui em casa e risque da lista antes de ir ao supermercado.Em alguns casos, você usará apenas uma fração de um ingrediente. Porém, se não tiver a fração suficiente em casa, precisará comprar o ingrediente em sua porção integral. São exemplos de ingredientes fracionados que muitas vezes você já vai ter: Azeite, Óleo, Manteiga, Sal, Pimenta do Reino...</span></li>
        <li><span>Abrindo este arquivo PDF no Adobe Acrobat Reader, você pode realçar ou riscar ingredientes selecionando o texto e clicando com o botão direito do mouse. Depois é só salvar a sua cópia editada. Dá pra inserir notas também.</span></li>
        <li><span>Defina qual é a melhor forma de levar a lista para o mercado. Você pode baixar este arquivo no seu celular ou abrir no computador e tirar uma foto da tela (eu faço isso!) ou imprimir.</span></li>
      </ol>';
    }
  }
}

new Freez_Recipes();
