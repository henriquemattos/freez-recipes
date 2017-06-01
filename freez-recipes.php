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
  protected $freez_recipes_db_version  = '1.0.0';
  protected $table_recipes;
  protected $table_ingredients;
  protected $table_recipes_ingredients;
  protected $charset_collate;

  public function __construct(){
    global $wpdb;
    if(is_admin()){
      add_action('admin_menu', array($this, 'set_admin_menu'));
      // add_action('admin_init',array($this, 'vworders_init'));
      add_action('wp_ajax_set_ingredients', array($this, 'set_ingredients'));
      add_action('wp_ajax_nopriv_set_ingredients', array($this, 'set_ingredients'));
      add_action('wp_ajax_set_recipes', array($this, 'set_recipes'));
      add_action('wp_ajax_nopriv_set_recipes', array($this, 'set_recipes'));
      add_action('admin_enqueue_scripts', array($this, 'enqueue_style_script'));
      $this->table_recipes = $wpdb->prefix . 'freez_recipes';
      $this->table_ingredients = $wpdb->prefix . 'freez_ingredients';
      $this->table_recipes_ingredients = $wpdb->prefix . 'freez_recipes_ingredients';
      $this->charset_collate = $wpdb->get_charset_collate();
      register_activation_hook(__FILE__, array($this, 'install'));
    }
  }
  public function install(){
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $sql_create_recipes = "CREATE TABLE IF NOT EXISTS $this->table_recipes (
      id_recipes INT UNSIGNED NOT NULL AUTO_INCREMENT,
      title VARCHAR(255) NOT NULL,
      description TEXT NULL,
      instructions TEXT NULL,
      PRIMARY KEY (id_recipes))
      $this->charset_collate;";

    dbDelta($sql_create_recipes);

    $sql_create_ingredients = "CREATE TABLE IF NOT EXISTS $this->table_ingredients (
      id_ingredients INT UNSIGNED NOT NULL AUTO_INCREMENT,
      name VARCHAR(255) NOT NULL,
      PRIMARY KEY (id_ingredients))
      $this->charset_collate;";

    dbDelta($sql_create_ingredients);

    $sql_create_recipes_ingredients = "CREATE TABLE IF NOT EXISTS $this->table_recipes_ingredients (
      id_recipes INT UNSIGNED NOT NULL,
      id_ingredients INT UNSIGNED NOT NULL,
      amount VARCHAR(255) NOT NULL,
      PRIMARY KEY (id_recipes, id_ingredients))
      $this->charset_collate;";

    dbDelta($sql_create_recipes_ingredients);
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
      wp_enqueue_style('select2-css',  plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array('bootstrap', 'bootstrap-theme'));
      wp_enqueue_style('freez-default',  plugin_dir_url( __FILE__ ) . 'css/default.css', array('bootstrap', 'bootstrap-theme'));
      wp_enqueue_script('bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'), '1.0.0', true);
      wp_enqueue_script('jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', array('jquery', 'bootstrap-js'), '1.0.0', true);
      wp_enqueue_script('jquery-serialize', plugin_dir_url(__FILE__) . 'js/jquery.serializejson.min.js', array('jquery'), '1.0.0', true);
      wp_enqueue_script('select2-js', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery', 'bootstrap-js'), '1.0.0', true);
      wp_enqueue_script('freez-recipes', plugin_dir_url( __FILE__ ) . 'js/default.js', array('jquery', 'bootstrap-js', 'jquery-mask', 'jquery-serialize'), '1.0.0', true);
      wp_localize_script('freez-recipes', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('freez_save_nonce')));
    }
  }
  public function set_recipes(){
    global $wpdb;
    ob_clean();
    if(!isset($_POST['data'])){
      print_r(json_encode(array('response' => 'You must POST some data to this action.')));
      wp_die();
    }
    $data = array(
      'title'        => $_POST['data']['recipe-title'],
      'description'  => $_POST['data']['recipe-description'],
      'instructions' => $_POST['data']['recipe-instructions']
    );

    $wpdb->insert($this->table_recipes, $data);

    if($wpdb->last_error !== ''){
      status_header(400);
      $str = htmlspecialchars($wpdb->last_result, ENT_QUOTES) . ' // // // ' . htmlspecialchars($wpdb->last_query, ENT_QUOTES);
      print_r(json_encode(array('response' => $str)));
      wp_die();
    }
    $id = $wpdb->insert_id;
    foreach($_POST['data']['recipe-ingredient'] as $ingredients){
      $data = array(
        'id_recipes'     => $id,
        'id_ingredients' => $ingredients['ingredient'],
        'amount'         => $ingredients['amount']
      );
      $wpdb->insert($this->table_recipes_ingredients, $data);
      if($wpdb->last_error !== ''){
        status_header(400);
        $str = htmlspecialchars($wpdb->last_result, ENT_QUOTES) . ' // // // ' . htmlspecialchars($wpdb->last_query, ENT_QUOTES);
        print_r(json_encode(array('response' => $str)));
        wp_die();
      }
      print_r(json_encode(array('response' => "Receita ({$id}) {$_POST['data']['recipe-title']} inserida com sucesso")));
      wp_die();
    }
  }
  public function set_ingredients(){
    global $wpdb;

    ob_clean();

    if(!isset($_POST['data'])){
      print_r(json_encode(array('response' => 'You must POST some data to this action.')));
      wp_die();
    }
    $data = array('name' => $_POST['data']['input-ingredient']);

    $wpdb->insert($this->table_ingredients, $data);
    if($wpdb->last_error !== ''){
        // $wpdb->print_error();
        status_header(400);
        $str = htmlspecialchars($wpdb->last_result, ENT_QUOTES) . ' // // // ' . htmlspecialchars($wpdb->last_query, ENT_QUOTES);
        print_r(json_encode(array('response' => $str)));
        wp_die();
    }
    $id = $wpdb->insert_id;
    $str = "Ingrediente ({$id}) {$_POST['data']['input-ingredient']} adicionado com sucesso.";
    print_r(json_encode(array('response' => $str)));
    wp_die();
  }
  public function get_all_recipes(){
    global $wpdb;
    $query = "SELECT id_recipes, title FROM {$this->table_recipes} ORDER BY id_recipes ASC";
    $results = $wpdb->get_results($query, OBJECT);
    include_once 'template-recipe-list.php';
  }
  public function get_single_recipe(){
    include_once 'template-recipe-view.php';
  }
  public function set_recipe(){
    $results = $this->list_ingredients();
    include_once 'template-recipe-add.php';
  }
  public function ingredients(){
    $results = $this->list_ingredients();
    include_once 'template-ingredient-add.php';
    include_once 'template-ingredient-list.php';
  }
  private function list_ingredients(){
    global $wpdb;
    $query = "SELECT * FROM {$this->table_ingredients} ORDER BY name ASC";
    return $wpdb->get_results($query, OBJECT);
  }
}

new Freez_Recipes();
