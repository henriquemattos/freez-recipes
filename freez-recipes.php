<?php
/*
Plugin Name: Freez Recipes
Plugin URI:  https://www.freez.com.br
Description: Create recipes, print PDF versions for customers and filter totals.
Version:     1.0.0
Author:      Freez
Author URI:  https://www.freez.com.br
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: freezrecipes
Domain Path: /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
class Freez_Recipes {
  public function __construct(){
    add_action('init', array($this, 'freez_create_post_type'));
    add_action('init', array($this, 'freez_create_taxonomy'));
    add_action('init', array($this, 'freez_enqueue_scripts'));
    add_action('add_meta_boxes', array($this, 'freez_add_ingredients_metaboxes'));
    add_action('save_post', array($this, 'freez_save_ingredients_metaboxes'));
    register_activation_hook(__FILE__, array($this, 'install'));
    register_deactivation_hook(__FILE__, array($this, 'uninstall'));

    add_action('wp_ajax_get_ingredients', array($this, 'get_ingredients'));
    add_action('wp_ajax_nopriv_get_ingredients', array($this, 'get_ingredients'));
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
    update_option(
      'freez_recipes_ingredients_measures',
      array(
        'mg',
        'g',
        'kg',
        'ml',
        'l',
      )
    );
  }
  public function uninstall(){
    delete_option('freez_recipes_ingredients_measures');
  }
  public function freez_save_ingredients_metaboxes($post_id){
    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */
    // Check if our nonce is set.
    if(!isset($_POST['freez_recipes_ingredients_nonce'])){
      return $post_id;
    }

    $nonce = $_POST['freez_recipes_ingredients_nonce'];

    // Verify that the nonce is valid.
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
    $measures = get_option('freez_recipes_ingredients_measures');
    include_once 'template-ingredient-metabox.php';
  }
  public function get_ingredients(){
    $terms = get_terms( array(
      'taxonomy' => 'freez_ingredients',
      'hide_empty' => false
    ));
    print json_encode($terms);
    wp_die();
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
}

new Freez_Recipes();
