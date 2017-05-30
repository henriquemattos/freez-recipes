<?php
/*
Plugin Name: Freez Recipes
Plugin URI:  https://www.freez.com.br
Description: Create recipes, print PDF versions for customers and filter totals.
Version:     0.0.1
Author:      Freez
Author URI:  https://www.freez.com.br
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: freezrecipes
Domain Path: /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Freez_Recipes {
  private $freez_recipes_db_version  = '1.0.0';
  private $table_recipes;
  private $table_ingredients;
  private $table_recipes_ingredients;
  private $charset_collate;

  public function __construct(){
    global $wpdb;
    register_activation_hook( __FILE__, array($this,'install'));
    if(is_admin()){
      add_action('admin_menu', array($this, 'set_admin_menu'));
      // add_action('admin_init',array($this, 'vworders_init'));
      add_action('wp_ajax_save', array($this, 'save'));
      add_action('wp_ajax_nopriv_save', array($this, 'save'));
      add_action('admin_enqueue_scripts', array($this, 'enqueue_style_script'));
    }
    $this->table_recipes = $wpdb->prefix . 'freez_recipes';
    $this->table_ingredients = $wpdb->prefix . 'freez_ingredients';
    $this->table_recipes_ingredients = $wpdb->prefix . 'freez_recipes_ingredients';
    $this->charset_collate = $wpdb->get_charset_collate();
  }
  public function install(){
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    include_once 'db-create.php';
    add_option('freez_recipes_db_version', $this->freez_recipes_db_version);
  }
  public function uninstall(){

  }
  public function set_admin_menu(){
    add_menu_page('Receitas', 'Receitas','publish_posts','freez-recipes.php', array($this, 'get_all_recipes'), 'dashicons-clipboard');
    add_submenu_page('freez-recipes.php', 'Todas as Receitas', 'Todas as Receitas', 'publish_posts', 'freez-recipes.php', array($this, 'get_all_recipes'));
    add_submenu_page('freez-recipes.php', 'Adicionar Receita', 'Adicionar Receita', 'publish_posts', 'add-recipe', array($this, 'set_recipe'));
    add_submenu_page('freez-recipes.php', 'Ingredientes', 'Ingredientes', 'publish_posts', 'list-ingredients', array($this, 'ingredients'));
  }
  public function enqueue_style_script($hook){
    if($hook === 'toplevel_page_freez-recipes' || $hook === 'receitas_page_add-recipe' || $hook === 'receitas_page_list-ingredients' || $hook === 'receitas_page_add-ingredient') {
      wp_enqueue_style('bootstrap',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css');
      wp_enqueue_style('bootstrap-theme',  plugin_dir_url( __FILE__ ) . 'css/bootstrap-theme.min.css', array('bootstrap'));
      wp_enqueue_style('vworders-default',  plugin_dir_url( __FILE__ ) . 'css/default.css', array('bootstrap', 'bootstrap-theme'));
      wp_enqueue_script('bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'), '1.0.0', true);
      wp_enqueue_script('jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', array('jquery', 'bootstrap-js'), '1.0.0', true);
      wp_enqueue_script('jquery-serialize', plugin_dir_url(__FILE__) . 'js/jquery.serializejson.min.js', array('jquery'), '1.0.0', true);
      wp_enqueue_script('vw_orders', plugin_dir_url( __FILE__ ) . 'js/default.js', array('jquery', 'bootstrap-js', 'jquery-mask', 'jquery-serialize'), '1.0.0', true);
      wp_localize_script('vw_orders', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('vworders_save_nonce')));
    }
  }
  public function save(){
    ob_clean();
    if(!isset($_POST['data'])){
      print_r(json_encode(array('response' => 'You must POST some data to this action')));
      wp_die();
    }
    $data = $_POST['data'];
  }
  public function get_all_recipes(){
    include_once 'template-recipe-list.php';
  }
  public function get_single_recipe(){
    include_once 'template-recipe-view.php';
  }
  public function set_recipe(){
    include_once 'template-recipe-add.php';
  }
  public function ingredients(){
    include_once 'template-ingredient-add.php';
    include_once 'template-ingredient-list.php';
  }
}

new Freez_Recipes();
