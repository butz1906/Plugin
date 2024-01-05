<?php
/*
Plugin Name: Search index
Description: Plugin indexant les recherches des utilisateurs
Version: 1.0
Author: Xavier
*/
add_action('admin_menu', 'search_index_menu');

function search_index_enqueue_admin_styles()
{
  wp_enqueue_script('search_index-admin-script', plugins_url('/js/admin-scripts.js', __FILE__), array('jquery'), '1.0.0', true);
  wp_localize_script('search_index-admin-script', 'search_index_object', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('search_index')));
}

add_action('admin_enqueue_scripts', 'search_index_enqueue_admin_styles');

function search_index_menu()
{
  // Ajouter le menu principal
  add_menu_page(
    'Search index',
    'Search index',
    'manage_options',
    'search_index',
    'search_index_dashboard',
    'dashicons-search',
    30
  );

  // Ajouter les sous-menus
  add_submenu_page(
    'search_index',
    'Liste des mots recherchés',
    'Liste des mots recherchés',
    'manage_options',
    'search_list',
    'search_list_page'
  );

  add_submenu_page(
    'search_index',
    'Redirection des recherches',
    'Redirection des recherches',
    'manage_options',
    'search_redirect',
    'search_redirect_page'
  );

  add_submenu_page(
    'search_index',
    'Injecter des résultats dans une recherche',
    'Injecter des résultats dans une recherche',
    'manage_options',
    'inject_terms',
    'inject_terms_page'
  );
}

function search_list_page()
{
  include(plugin_dir_path(__FILE__) . 'search_list.php');
}

function search_redirect_page()
{
  include(plugin_dir_path(__FILE__) . 'search_redirect.php');
}

function inject_terms_page()
{
  include(plugin_dir_path(__FILE__) . 'inject_terms.php');
}

function search_index_dashboard()
{
  echo '<h1>Gestion des recherches</h1>';
  echo '<ul>';
  echo '<li><a href="' . admin_url('admin.php?page=search_list') . '">Liste des mots recherché</a></li>';
  echo '<li><a href="' . admin_url('admin.php?page=search_redirect') . '">Redirection des recherches</a></li>';
  echo '<li><a href="' . admin_url('admin.php?page=inject_terms') . '">Injecter des résultats dans une recherche</a></li>';

  echo '</ul>';
}
// Activation du plugin
function search_index_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_index';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        search_term varchar(255) NOT NULL,
        search_date datetime NOT NULL,
        PRIMARY KEY  (id)
    )
    $charset_collate;";
  $wpdb->query($sql);
}

add_action('init', 'search_index_table');

function redirect_link_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_redirection';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id INT(11) NOT NULL AUTO_INCREMENT,
    keyword VARCHAR(255) NOT NULL,
    redirect_option VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    PRIMARY KEY  (id)
  )
  $charset_collate;";
  $wpdb->query($sql);
}

add_action('init', 'redirect_link_table');

function inject_link_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_inject';
  $table_post = $wpdb->prefix . 'posts';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id INT(11) NOT NULL AUTO_INCREMENT,
    keyword VARCHAR(255) NOT NULL,
    post_id BIGINT(20) UNSIGNED NOT NULL,
    resultat_position INT(10) NOT NULL,
    PRIMARY KEY  (id),
    FOREIGN KEY (post_id) REFERENCES $table_post(ID)
  )
  $charset_collate;";
  $wpdb->query($sql);
}

add_action('init', 'inject_link_table');

// Enregistrement du terme de recherche dans la base de données
function save_search_term($search_term)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_index';
  $search_term = htmlentities($search_term);
  $wpdb->insert(
    $table_name,
    array(
      'search_term' => $search_term,
      'search_date' => current_time('mysql')
    ),
    array(
      '%s',
      '%s'
    )
  );
}

// Ajout d'un filtre sur la recherche WordPress pour enregistrer les termes de recherche
function register_search()
{
  if (is_search() && get_search_query() !== '') {
    save_search_term(get_search_query());
  }
}

add_action('wp', 'register_search');

function search_data()
{
  if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'search_index';
    $results = $wpdb->get_results(
      "SELECT search_term, COUNT(*) AS count
        FROM $table_name
        WHERE search_date BETWEEN '$start_date' AND '$end_date'
        GROUP BY search_term
        ORDER BY count DESC"
    );

    // Afficher les résultats
    if ($results) {
      echo '<h3>Résultats pour la période du ' . $start_date . ' au ' . $end_date . ' :</h3>';
      echo '<ul>';
      foreach ($results as $result) {
        echo '<li>' . esc_html($result->search_term) . ' (' . $result->count . ' fois)</li>';
      }
      echo '</ul>';
    } else {
      echo 'Aucun résultat trouvé pour la période du ' . $start_date . ' au ' . $end_date;
    }
  }
  wp_die();
}

add_action('wp_ajax_search_data', 'search_data');
add_action('wp_ajax_nopriv_search_data', 'search_data');

add_action('wp_ajax_my_get_pages', 'my_get_pages');
add_action('wp_ajax_nopriv_my_get_pages', 'my_get_pages');

function my_get_pages()
{
  $args = array(
    'post_type' => 'page',
    'orderby' => 'title',
    'order' => 'ASC',
  );
  $pages = get_posts($args);

  $options = array();
  foreach ($pages as $page) {
    $options[] = '<option value="' . get_permalink($page->ID) . '">' . $page->post_title . '</option>';
  }

  echo implode('', $options);
  wp_die();
}

add_action('wp_ajax_my_get_pages', 'my_get_posts');
add_action('wp_ajax_nopriv_my_get_pages', 'my_get_posts');

function my_get_posts()
{
  global $wpdb;

  $results = $wpdb->get_results(
    "
      SELECT *
      FROM {$wpdb->prefix}posts
      WHERE post_type = 'post' AND post_status = 'publish'
      ORDER BY post_date DESC
      "
  );

  $options = '';
  foreach ($results as $result) {
    $options .= '<option value="' . get_permalink($result->ID) . '">' . $result->post_title . '</option>';
  }

  echo $options;
}

add_action('wp_ajax_my_get_categories', 'my_get_categories');
add_action('wp_ajax_nopriv_my_get_categories', 'my_get_categories');

function my_get_categories()
{
  $args = array(
    'taxonomy' => 'category',
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => false
  );
  $categories = get_categories($args);

  $options = array();
  foreach ($categories as $category) {
    $options[] = '<option value="' . get_category_link($category->cat_ID) . '">' . $category->name . '</option>';
  }

  echo implode('', $options);
  wp_die();
}

add_action('wp_ajax_my_get_tags', 'my_get_tags');
add_action('wp_ajax_nopriv_my_get_tags', 'my_get_tags');

function my_get_tags()
{
  $args = array(
    'taxonomy' => 'post_tag',
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => false
  );
  $tags = get_terms($args);

  $options = array();
  foreach ($tags as $tag) {
    $options[] = '<option value="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</option>';
  }

  echo implode('', $options);
  wp_die();
}

add_action('wp_ajax_my_get_posts', 'my_get_posts');
add_action('wp_ajax_nopriv_my_get_posts', 'my_get_posts');

function save_redirection()
{
  $keyword = $_POST['keyword'];
  $redirect_option = $_POST['redirect_option'];
  $title = $_POST['title'];

  global $wpdb;
  $table_name = $wpdb->prefix . 'search_redirection';

  // Vérifier si le keyword existe déjà dans la base de données
  $existing_keyword = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT keyword FROM $table_name WHERE keyword = %s",
      $keyword
    )
  );

  if ($existing_keyword) {
    wp_send_json_error("Le keyword '$keyword' existe déjà dans la base de données.");
  } else {
    $data = array(
      'keyword' => $keyword,
      'redirect_option' => $redirect_option,
      'title' => $title
    );
    $wpdb->insert($table_name, $data);

    wp_send_json_success('Redirection enregistrée avec succès !');
  }
}

add_action('wp_ajax_save_redirection', 'save_redirection');
add_action('wp_ajax_nopriv_save_redirection', 'save_redirection');

function redirect_delete()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_redirection';
  $id = $_POST['id'];
  $wpdb->delete($table_name, array('id' => $id));
}

add_action('wp_ajax_redirect_delete', 'redirect_delete');

add_action('rest_api_init', 'my_plugin_register_rest_routes');

function my_plugin_register_rest_routes()
{
  register_rest_route('my-plugin/v1', '/search', array(
    'methods' => 'POST',
    'callback' => 'my_plugin_search',
  ));
}

function get_search_results($search_term)
{
  $args = array(
    's' => $search_term,
    'post_type' => 'any',
    'post_status' => 'publish',
    'orderby' => 'relevance',
    'nopaging' => true,
  );
  $search_results = get_posts($args);
  return $search_results;
}

function my_plugin_search($request)
{
  $searchTerm = $request['search_term']; // Récupérer le terme de recherche envoyé en AJAX
  $searchResults = get_search_results($searchTerm); // Récupérer les résultats de recherche avec WordPress
  if (count($searchResults) > 0) {
    // Afficher les résultats de recherche
    $results = '';
    foreach ($searchResults as $result) {
      $results .= '<p><a href="' . get_permalink($result->ID) . '">' . $result->post_title . '</a></p>';
    }
    return $results;
  } else {
    return '<p>Aucun résultat trouvé.</p>';
  }
}

add_action('rest_api_init', 'my_plugin_get_post_by_id_register_rest_routes');

function my_plugin_get_post_by_id_register_rest_routes()
{
  register_rest_route('my-plugin/v1', '/get-post-by-id', array(
    'methods' => 'POST',
    'callback' => 'my_plugin_get_post_by_id',
  ));
}

function my_plugin_get_post_by_id($request)
{
  $postId = $request['post_id']; // Récupérer l'ID du post envoyé en AJAX
  $post = get_post($postId); // Récupérer le post avec WordPress
  if ($post) {
    wp_send_json(array(
      'id' => $post->ID,
      'title' => $post->post_title,
      'link' => get_permalink($post->ID),
      'content' => apply_filters('the_content', $post->post_content),
    ));
  } else {
    // Le post n'a pas été trouvé, renvoyer une erreur
    return new WP_Error('post_not_found', 'Le post n\'a pas été trouvé', array('status' => 404));
  }
}

add_action('wp_ajax_save_search_inject', 'save_search_inject');

function save_search_inject()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_inject';
  $keyword = $_POST['keyword'];
  $post_id = $_POST['post_id'];
  $resultat_position = $_POST['resultat_position'];
  $data = array(
    'keyword' => $keyword,
    'post_id' => $post_id,
    'resultat_position' => $resultat_position,
  );
  $wpdb->insert($table_name, $data);
  wp_die();
}

function inject_delete()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_inject';
  $id = $_POST['id'];
  $wpdb->delete($table_name, array('id' => $id));
}

add_action('wp_ajax_inject_delete', 'inject_delete');

add_action('wp_ajax_update_position', 'update_position');
function update_position()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'search_inject';
  $id = $_POST['id'];
  $position = $_POST['position'];
  $wpdb->update($table_name, array('resultat_position' => $position), array('id' => $id));
  wp_die();
}