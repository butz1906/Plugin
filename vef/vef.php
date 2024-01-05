<?php
/*
Plugin Name: VEF
Description: Plugin pour la gestion des liens sousmis par les lecteurs et de travaux en cours.
Version: 1.0
Author: Xavier
*/
add_action('admin_menu', 'vef_create_menu');

function vef_create_menu()
{
  // Ajouter le menu principal VEF
  add_menu_page(
    'VEF',
    'VEF',
    'manage_options',
    'vef',
    'vef_dashboard',
    'dashicons-admin-site-alt',
    30
  );

  // Ajouter les sous-menus
  add_submenu_page(
    'vef',
    'Notice',
    'Notice',
    'manage_options',
    'vef-notice',
    'vef_notice_page'
  );

  add_submenu_page(
    'vef',
    'Shortcodes',
    'Shortcodes',
    'manage_options',
    'vef-shortcodes',
    'vef_shortcode_page'
  );

  add_submenu_page(
    'vef',
    'Liens',
    'Liens',
    'manage_options',
    'vef-liens',
    'vef_liens_page'
  );

  add_submenu_page(
    'vef',
    'WIP',
    'WIP',
    'manage_options',
    'vef-wip',
    'vef_wip_page'
  );

  add_submenu_page(
    'vef',
    '404',
    '404',
    'manage_options',
    'vef-404',
    'vef_404_page'
  );
}

function vef_dashboard()
{
  echo '<h1>VEF Plugin</h1>';
  echo '<ul>';
  echo '<li><a href="' . admin_url('admin.php?page=vef-notice') . '">Notice</a></li>';

  echo '<li><a href="' . admin_url('admin.php?page=vef-shortcodes') . '">Créer des shortcodes</a></li>';
  echo '<li><a href="' . admin_url('admin.php?page=vef-liens') . '">Gérer les liens</a></li>';
  echo '<li><a href="' . admin_url('admin.php?page=vef-wip') . '">Voir les travaux en cours</a></li>';
  echo '<li><a href="' . admin_url('admin.php?page=vef-404') . '">Réglages page 404</a></li>';
  echo '</ul>';
}

function vef_notice_page()
{
  include(plugin_dir_path(__FILE__) . 'notice.php');
}

function vef_shortcode_page()
{
  include(plugin_dir_path(__FILE__) . 'shortcode.php');
}

function vef_liens_page()
{
  include(plugin_dir_path(__FILE__) . 'liens.php');
}

function vef_wip_page()
{
  include(plugin_dir_path(__FILE__) . 'wip.php');
}

function vef_404_page()
{
  include(plugin_dir_path(__FILE__) . '404.php');
}


function vef_enqueue_admin_styles()
{
  wp_enqueue_script('vef-admin-script', plugins_url('/js/admin-scripts.js', __FILE__), array('jquery'), '1.0.0', true);
  wp_enqueue_style('vef-admin', plugins_url('css/admin.css', __FILE__));
  wp_localize_script('vef-admin-script', 'vef_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('vef_remove_shortcode')));
}

add_action('admin_enqueue_scripts', 'vef_enqueue_admin_styles');

function vef_enqueue_scripts()
{
  wp_enqueue_script('vef-shortcode-links', plugins_url('/js/shortcode.js', __FILE__), array('jquery'), '1.0.0', true);
  wp_enqueue_style('vef-admin', plugins_url('css/shortcode-links.css', __FILE__));
  wp_localize_script('vef-shortcode-links', 'vef_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('vef_ajax_nonce')));
}

add_action('wp_enqueue_scripts', 'vef_enqueue_scripts');


function vef_create_table_link_buttons()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'link_buttons';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      color VARCHAR(7) NOT NULL,
      text VARCHAR(255) NOT NULL,
      PRIMARY KEY  (id)
  ) $charset_collate;";

  $wpdb->query($sql);
}

add_action('init', 'vef_create_table_link_buttons');

function vef_404_tag()
{
  global $wpdb;
  $table_name = $wpdb->prefix . '404_tag';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      tag VARCHAR(255),
      afficher VARCHAR(255),
      PRIMARY KEY (id)
  ) $charset_collate;";

  $wpdb->query($sql);

  // Vérifier si la ligne existe déjà
  $existing_row = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

  // Insérer une entrée avec les champs "tag" et "afficher" vides si la ligne n'existe pas déjà
  if (!$existing_row) {
    $wpdb->insert(
      $table_name,
      array(
        'tag' => '',
        'afficher' => 'non',
      )
    );
  }
}

add_action('init', 'vef_404_tag');

function vef_404_category()
{
  global $wpdb;
  $table_name = $wpdb->prefix . '404_category';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      category VARCHAR(255),
      afficher VARCHAR(255),
      PRIMARY KEY (id)
  ) $charset_collate;";

  $wpdb->query($sql);

  // Vérifier si la ligne existe déjà
  $existing_row = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

  // Insérer une entrée avec les champs "tag" et "afficher" vides si la ligne n'existe pas déjà
  if (!$existing_row) {
    $wpdb->insert(
      $table_name,
      array(
        'category' => '',
        'afficher' => 'non',
        )
      );
    }
  }
  
  add_action('init', 'vef_404_category');

function vef_404_reco()
{
  global $wpdb;
  $table_name = $wpdb->prefix . '404_reco';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      recommandation VARCHAR(255) NOT NULL,
      PRIMARY KEY  (id)
  ) $charset_collate;";

  $wpdb->query($sql);
}

add_action('init', 'vef_404_reco');


function get_link_buttons()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'link_buttons';
  $results = $wpdb->get_results("SELECT * FROM $table_name");
  return $results;
}

function vef_delete_button()
{
  if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vef_shortcode_action')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'link_buttons';
    $button_id = intval($_POST['button_id']);
    $wpdb->delete($table_name, array('id' => $button_id));
    echo 'success';
  } else {
    echo 'nonce error';
  }
}

add_action('wp_ajax_vef_delete_button', 'vef_delete_button');

function vef_button_shortcode($atts)
{
  $atts = shortcode_atts(array(
    'text' => '',
    'color' => ''
  ), $atts, 'vef_button');
  return "<button class='button-shortcode' style='background-color:{$atts['color']};'>{$atts['text']}</button>";
}

add_shortcode('vef_button', 'vef_button_shortcode', 1);

add_filter('the_content', 'do_shortcode');

function vef_create_table_liens_contributeurs()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'liens_contributeurs';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      url VARCHAR(255) NOT NULL,
      favicon VARCHAR(255),
      statut VARCHAR(20) NOT NULL,
      article_title VARCHAR(255) NOT NULL,
      titre_section VARCHAR(255) NOT NULL,
      PRIMARY KEY  (id)
  ) $charset_collate;";

  $wpdb->query($sql);
}

add_action('init', 'vef_create_table_liens_contributeurs');


function vef_liens_contributeurs()
{
  if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'vef_ajax_nonce')) {
    wp_die('Nonce invalide');
  }

  global $wpdb;
  $table_name = $wpdb->prefix . 'liens_contributeurs';

  $favicon = isset($_POST['favicon']) ? $_POST['favicon'] : '';

  $wpdb->insert(
    $table_name,
    array(
      'favicon' => $favicon,
      'url' => $_POST['url'],
      'statut' => $_POST['status'],
      'article_title' => $_POST['article_title'],
      'titre_section' => $_POST['titre_section'],
    )
  );
}

add_action('wp_ajax_vef_liens_contributeurs', 'vef_liens_contributeurs');


function get_liens_contributeurs()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'liens_contributeurs';
  $results = $wpdb->get_results("SELECT * FROM $table_name");
  return $results;
}

function vef_delete_liens()
{
  if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vef_shortcode_action')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'liens_contributeurs';
    $id = intval($_POST['id']);
    $wpdb->delete($table_name, array('id' => $id));
    $results = get_liens_contributeurs();
    wp_send_json($results);
  } else {
    echo 'nonce error';
  }
}

add_action('wp_ajax_vef_delete_liens', 'vef_delete_liens');

function vef_validation_liens()
{
  if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vef_shortcode_action')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'liens_contributeurs';
    $id = intval($_POST['id']);
    $wpdb->update($table_name, array('statut' => '&#x2714;'), array('id' => $id));
    $results = get_liens_contributeurs();
    wp_send_json($results);
  } else {
    echo 'nonce error';
  }
}

add_action('wp_ajax_vef_validation_liens', 'vef_validation_liens');

function update_post_state()
{
  if (!current_user_can('edit_posts')) {
    return;
  }

  check_admin_referer('vef_shortcode_action', 'nonce');

  $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
  $new_state = isset($_POST['etat']) ? sanitize_text_field($_POST['etat']) : '';

  $new_state = $_POST['etat'];

  update_post_meta($post_id, 'etat', $new_state);

  echo '<script>window.location.href = "' . esc_url(admin_url('admin.php?page=vef-wip')) . '";</script>';
}

add_action('admin_post_change_state_state', 'update_post_state');

function update_post_visibilite()
{
  if (!current_user_can('edit_posts')) {
    return;
  }

  check_admin_referer('vef_shortcode_action', 'nonce');

  $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
  $new_visibilite = isset($_POST['visibilite']) ? sanitize_text_field($_POST['visibilite']) : '';

  $new_visibilite = $_POST['visibilite'];

  update_post_meta($post_id, 'visibilite', $new_visibilite);

  echo '<script>window.location.href = "' . esc_url(admin_url('admin.php?page=vef-wip')) . '";</script>';
}

add_action('admin_post_change_state_visibilite', 'update_post_visibilite');

add_action('wp_ajax_update_404_tag', 'update_404_tag');
add_action('wp_ajax_nopriv_update_404_tag', 'update_404_tag');

function update_404_tag()
{
  global $wpdb;

  $tag = $_POST['tag'];
  $afficher = $_POST['afficher'];

  $result = $wpdb->update(
    $wpdb->prefix . '404_tag',
    array('afficher' => $afficher, 'tag' => $tag),
    array('id' => 1)
  );

  if ($result === false) {
    echo 'Error';
  } else {
    echo 'Success';
  }

  wp_die();
}

add_action('wp_ajax_update_404_category', 'update_404_category');
add_action('wp_ajax_nopriv_update_404_category', 'update_404_category');

function update_404_category()
{
  global $wpdb;

  $category = $_POST['category'];
  $afficher = $_POST['afficher'];

  $result = $wpdb->update(
    $wpdb->prefix . '404_category',
    array('afficher' => $afficher, 'category' => $category),
    array('id' => 1)
  );

  if ($result === false) {
    echo 'Error';
  } else {
    echo 'Success';
  }

  wp_die();
}

function reco_posts()
{
  global $wpdb;

  $results = $wpdb->get_results(
    "
      SELECT *
      FROM {$wpdb->prefix}posts
      WHERE (post_type = 'post' OR post_type = 'page')
      AND post_status = 'publish'
      ORDER BY post_date DESC
      "
  );

  $options = '';
  foreach ($results as $result) {
    $options .= '<option value="' . get_permalink($result->ID) . '">' . $result->post_title . '</option>';
  }

  echo $options;
}

function save_recommandation()
{
  $recommandation = $_POST['recommandation'];

  global $wpdb;
  $table_name = $wpdb->prefix . '404_reco';
    $data = array(
      'recommandation' => $recommandation
    );
    $wpdb->insert($table_name, $data);

    wp_send_json_success('Redirection enregistrée avec succès !');
  }


add_action('wp_ajax_save_recommandation', 'save_recommandation');
add_action('wp_ajax_nopriv_save_recommandation', 'save_recommandation');

function reco_delete()
{
  global $wpdb;
  $table_name = $wpdb->prefix . '404_reco';
  $id = $_POST['id'];
  $wpdb->delete($table_name, array('id' => $id));
}

add_action('wp_ajax_reco_delete', 'reco_delete');
