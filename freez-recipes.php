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
    add_action('init', array($this, 'freez_create_taxonomy'));
    add_action('init', array($this, 'freez_create_post_type'));
    add_action('add_meta_boxes', array($this, 'freez_add_ingredients_metaboxes'));
    add_action('save_post', array($this, 'freez_save_ingredients_metaboxes'));
    register_activation_hook(__FILE__, array($this, 'install'));
  }
  public function install(){
    update_option(
      'freez_recipes_ingredients_measures',
      array(
        'miligrama(s)',
        'grama(s)',
        'kilograma(s)',
        'mililitro(s)',
        'litro(s)',
        'xícara(s)'
      )
    );
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

    foreach($_POST['ingredient'] as $key => $value){
      if($_POST['ingredient']){
        // Sanitize the user input.
        $ingredient = sanitize_text_field($value);
        $amount = sanitize_text_field($_POST['amount'][$key]);
        // Update the meta field.
        update_post_meta($post_id, $ingredient, $amount);
      }
    }
  }
  public function freez_add_ingredients_metaboxes(){
    global $wp_meta_boxes;
    add_meta_box('freez_recipes_ingredients', __('Ingredientes'), array($this, 'freez_ingredients_metaboxes_html'), 'freez_recipes');
  }
  public function freez_ingredients_metaboxes_html(){
    // Add an nonce field so we can check for it later.
    wp_nonce_field('freez_recipes_ingredients', 'freez_recipes_ingredients_nonce');
    foreach(get_post_meta(get_the_ID()) as $key => $value){
      $strpos = strpos($key, '_edit_');
      if($strpos !== 0){
        $ingredients[$key] = $value[0];
      }
    }
    include_once 'template-ingredient-add.php';
  }
  public function freez_create_taxonomy(){
    register_taxonomy(
      'freez_recipe_category',
      'freez_recipes',
      array('labels' => array(
        'name' => __('Categories'),
        'singular_name' => __('Category'),
        'all_items' => __('All Categories'),
        'edit_item' => __('Edit Category'),
        'view_item' => __('View Category'),
        'add_new_item' => __('Add New Category'),
        'new_item_name' => __('New Category Name'),
      ),
      'description' => __('Freez Categorias de Receitas')
    ));
  }
  public function freez_create_post_type(){
    register_post_type('freez_recipes',
      array(
        'labels' => array(
          'name' => __('Receitas'),
          'singular_name' => __('Receita'),
          'add_new_item' => __('Adicionar nova receita'),
          'edit_item' => __('Editar receita'),
          'new_item' => __('Nova receita'),
          'view_item' => __('Ver receita'),
          'view_items' => __('Ver receitas'),
          'search_items' => __('Buscar receitas'),
          'not_found' => __('Nenhuma receita encontrada')
        ),
        'description' => __('Receitas personalizadas por Freez'),
        'public' => true,
        'exclude_from_search' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-clipboard',
        'has_archive' => true,
        'rewrite' => array('slug' => 'receitas'),
        'support' => array('title', 'editor', 'author', 'thumbnail', 'custom-fields', 'revisions'),
        'taxonomies' => array('categoria_receita')
      )
    );
  }
}

new Freez_Recipes();
