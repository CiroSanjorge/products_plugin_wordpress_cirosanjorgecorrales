<?php

/**
 * Plugin Name: products_plugin
 * Plugin URI: https://google.com
 * Description: Guarda los productos de tu página de WooCommerce en una base de datos externa
 * Version: 1.0.0
 * Author: Ciro Sanjorge 
 * Author URI: https://github.com/CiroSanjorge
 * License: none
 */

/* Main Plugin File */

//========== ACTIVATION / DEACTIVATION / UNINSTALL HOOKS


include(__DIR__ . '\includes\admin\store_into_db.php');

register_activation_hook(__FILE__, 'activate_products_plugin');
register_deactivation_hook(__FILE__, 'deactivate_products_plugin');
register_uninstall_hook(__FILE__, 'uninstall_products_plugin');

function activate_products_plugin()
{
  products_plugin_create_post_form();
}
function deactivate_products_plugin()
{
  products_plugin_delete_post_form();
}
function uninstall_products_plugin()
{
  products_plugin_delete_post_form();
}


//========== CREATE / GET CATEGORY

function products_plugin_create_category($slug)
{
  if (!$alredy_created_category = get_category_by_slug($slug)) {
    $new_category = wp_insert_term($slug, 'category', array(
      'description' => 'Descripción de la nueva categoría', 'parent' => 0
    ));
    return $new_category;
  } else {
    return $alredy_created_category;
  };
}


//========== DELETE CATEGORY

function products_plugin_delete_category($category_id)
{
  return wp_delete_term($category_id, 'category');
}


//========== CREATE  CATEGORY AND IMAGE

function products_plugin_create_post_form()
{
  $user_id = wp_get_current_user();
  $post_data = array(
    'ID'            => wp_count_posts('post')->publish + 1,
    'post_title'    => 'Plugin data form',
    'post_name' => 'plugin-data-form',
    'post_content'  => '[products_plugin_form]',
    'post_status'   => 'publish',
    'post_author'   => $user_id->ID
  );

  $post = get_page_by_path('plugin-data-form', OBJECT, 'post');
  if (!$post instanceof WP_Post) {
    $post_id = wp_insert_post($post_data);
    wp_set_post_categories($post_id, products_plugin_create_category('products_plugin_new_category'));
  }

  $img_path = 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fwallpapercave.com%2Fwp%2Fwp3006091.jpg&f=1&nofb=1&ipt=361ed5b750caadb6dfd3963947c562240f9929a508d4d7c9d5a280460116a580&ipo=images';
  $img_id =  media_sideload_image($img_path, $post, 'img_desc', 'id');

  if (!is_wp_error($img_id)) {
    set_post_thumbnail($post_id, $img_id);
  }
}


//========== DELETE POST

function products_plugin_delete_post_form()
{
  $post = get_page_by_path('plugin-data-form', OBJECT, 'post');
  if ($post instanceof WP_Post) {
    $post_categories = wp_get_post_categories($post->ID);
    foreach ($post_categories as $cat) {
      wp_delete_category($cat);                                     // DELETE CATEGORIES
    }
    wp_delete_attachment(get_post_thumbnail_id($post->ID), true);   // DELETE THUMBNAIL
    wp_delete_post($post->ID);                                      // DELETE POST
  }
}


//========== FORM SHORTCODE

function products_plugin_form_shortcode()
{
  $form = include_once(__DIR__ . '\includes\public\products_plugin_form.php');
  return $form;
}
add_shortcode('products_plugin_form', 'products_plugin_form_shortcode');


//========== FORM DATA

function products_plugin_form_data()
{
  $req_data = ['url', 'ck', 'cs', 'user', 'pass', 'server', 'db', 'table'];
  $output = [];
  foreach ($req_data as $item) {
    if (!isset($POST[$item])) {
      $POST[$item] = '';
    }
    array_push($output, sanitize_text_field($_POST[$item]));
  }
  $output = array_combine($req_data, array_values($output));
  return $output;
}


//========== FORM SEND

add_action('admin_post', 'products_plugin_form_send');
add_action("admin_post_data_form", 'products_plugin_form_send');
add_action('admin_post_nopriv', 'products_plugin_form_send');
add_action("admin_post_nopriv_data_form", 'products_plugin_form_send');
function products_plugin_form_send()
{
  $data = products_plugin_form_data();
  $woo = get_woocommerce_client($data['url'], $data['ck'], $data['cs']);
  $db_connect = connect_to_database($data['user'], $data['pass'], $data['server'], $data['db']);
  try {
    store_from_woocommerce_into_db($db_connect, $data['table'], $woo);
  } catch (Exception $e) {
    wp_die("ERROR EN EL FORMULARIO: " . $e->getMessage(), 'Aviso', array('response' => 100));
  } finally {
    close_connection($db_connect);
  }
}
