<?php

if (isset($_POST['vef_shortcode_submit']) && wp_verify_nonce($_POST['vef_shortcode_nonce'], 'vef_shortcode_action')) {
    $text = sanitize_text_field($_POST['vef_shortcode_text']);
    $color = sanitize_text_field($_POST['vef_shortcode_color']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'link_buttons';
    $wpdb->insert(
        $table_name,
        array(
            'color' => $color,
            'text' => $text
        )
    );

    $shortcode = $button_div . "[vef_button id=\"" . $wpdb->insert_id . "\" text=\"$text\" color=\"$color\"]" . $button_div_close;
    $shortcodes = get_option('vef_shortcodes');
    if (!$shortcodes) {
        $shortcodes = array();
    }
    array_push($shortcodes, $shortcode);
    update_option('vef_shortcodes', $shortcodes);
}

$shortcode_list = get_option('vef_shortcodes', array());
?>
<h1>Shortcodes VEF</h1>
<h2>Créer un nouveau shortcode :</h2>
<form method="post">
    <?php wp_nonce_field('vef_shortcode_action', 'vef_shortcode_nonce'); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Entrez le texte du bouton</th>
            <td><input type="text" name="vef_shortcode_text" /></td>
        </tr>
        <tr valign="top">
            <th scope="row">Selectionnez une couleur pour le bouton</th>
            <td>
                <select name="vef_shortcode_color">
                    <option value="#e84244">Rouge</option>
                    <option value="#99c00f">Vert</option>
                    <option value="#18a096">Bleu</option>
                </select>
            </td>
        </tr>
    </table>
    <?php submit_button('Générer un shortcode', 'primary', 'vef_shortcode_submit'); ?>
</form>

<h2>Shortcode(s) précédement créé(s) :</h2>
<?php
$results = get_link_buttons();
foreach ($results as $row) {?>
<form method='post'>
<?php wp_nonce_field( 'vef_shortcode_action', 'vef_shortcode_nonce' );?>
<div><?php echo do_shortcode('[vef_button text="' . $row->text . '" color="' . $row->color . '"]');

echo '<div class="code-shortcode">[vef_button text="' . $row->text . '" color="' . $row->color . '"]</div>'?>
  <button class="delete-button button button-primary" data-button-id="<?php echo $row->id ?>">Supprimer</button>
  </div>
<?php
}