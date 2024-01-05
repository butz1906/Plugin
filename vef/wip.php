<?php
echo '<h2>Travaux en cours</h2>';
// Query pour récupérer tous les articles et pages non publiés
$args = array(
    'post_type'      => array('post', 'page'),
    'post_status'    => array('draft', 'pending', 'future'),
    'nopaging'       => true,
);
$query = new WP_Query($args);
?>

<!-- Tableau pour afficher les résultats -->
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th class="manage-column column-title column-primary sortable">
                <span>Titre de l'article/page</span>
            </th>
            <th class="manage-column column-author">Auteur</th>
            <th class="manage-column column-visibility">Visibilité</th>
            <th class="manage-column column-state">État</th>
        </tr>
    </thead>
    <tbody id="the-list">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
        <?php
            if (!get_post_meta(get_the_ID(), 'visibilite', true)) {
                add_post_meta(get_the_ID(), 'visibilite', 'masque');
            }
            if (!get_post_meta(get_the_ID(), 'etat', true)) {
                add_post_meta(get_the_ID(), 'etat', "<span class='dashicons dashicons-dismiss'></span>");
            }
            ?>
            <tr id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><a class="row-title" href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a></strong>
                </td>
                <td class="author column-author"><?php the_author(); ?></td>
                <td class="visibility column-visibility">
                    <?php $visibilite = get_post_meta(get_the_ID(), 'visibilite', true); ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="change_state_visibilite" />
                        <?php wp_nonce_field('vef_shortcode_action', 'nonce'); ?>
                        <input type="hidden" name="post_ID" value="<?php the_ID(); ?>" />
                        <select name="visibilite" onchange="this.form.submit()">
                            <option value="visible" <?php selected($visibilite, "visible"); ?>>Publique</option>
                            <option value="masque" <?php selected($visibilite, "masque"); ?>>Privé</option>
                        </select>
                    </form>
                </td>
                <?php

            ?>
                <td class="state column-state">
                    <?php $state = get_post_meta(get_the_ID(), 'etat', true); ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="change_state_state" />
                        <?php wp_nonce_field('vef_shortcode_action', 'nonce'); ?>
                        <input type="hidden" name="post_ID" value="<?php the_ID(); ?>" />
                        <select name="etat" onchange="this.form.submit()">
                            <option value="<span class='dashicons dashicons-dismiss'></span>" <?php selected($state, "<span class='dashicons dashicons-dismiss'></span>"); ?>>Non commencé</option>
                            <option value="<span class='dashicons dashicons-admin-collapse'></span>" <?php selected($state, "<span class='dashicons dashicons-admin-collapse'></span>"); ?>>En cours</option>
                            <option value="<span class='dashicons dashicons-yes-alt'></span>" <?php selected($state, "<span class='dashicons dashicons-yes-alt'></span>"); ?>>Terminé</option>
                        </select>
                    </form>

                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php wp_reset_postdata(); ?>