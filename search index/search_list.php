<h2>Liste des mots recherchés</h2>

<div class="tabs">
    <a href="#" class="tab-link active" data-tab="tab-1">7 derniers jours</a>
    <a href="#" class="tab-link" data-tab="tab-2">Mois dernier</a>
    <a href="#" class="tab-link" data-tab="tab-3">Cette année</a>
    <a href="#" class="tab-link" data-tab="tab-4">Sélectionner des dates</a>
</div>

<div id="tab-1" class="tab-content active">
    <?php // Récupérer les résultats de recherche avec une date de moins de 7 jours
    global $wpdb;
    $table_name = $wpdb->prefix . 'search_index';
    $results = $wpdb->get_results(
        "SELECT search_term, COUNT(*) AS count
     FROM $table_name
     WHERE search_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     GROUP BY search_term
     ORDER BY count DESC"

    );

    // Afficher les résultats
    if ($results) {
        echo '<h3>Recherche(s) de ces 7 derniers jours :</h3>';
        echo '<ul>';
        foreach ($results as $result) {
            echo '<li>' . esc_html($result->search_term) . ' (' . $result->count . ' fois)</li>';
        }
        echo '</ul>';
    } else {
        echo 'Aucun résultat trouvé';
    } ?>
</div>

<div id="tab-2" class="tab-content">
    <?php // Récupérer les résultats de recherche avec une date de moins de 1 mois
    global $wpdb;
    $table_name = $wpdb->prefix . 'search_index';
    $results = $wpdb->get_results(
        "SELECT search_term, COUNT(*) AS count
     FROM $table_name
     WHERE search_date >= DATE_SUB(NOW(), INTERVAL 31 DAY)
     GROUP BY search_term
     ORDER BY count DESC"
    );

    // Afficher les résultats
    if ($results) {
        echo '<h3>Recherche(s) du mois dernier :</h3>';
        echo '<ul>';
        foreach ($results as $result) {
            echo '<li>' . esc_html($result->search_term) . ' (' . $result->count . ' fois)</li>';
        }
        echo '</ul>';
    } else {
        echo 'Aucun résultat trouvé';
    } ?>
</div>

<div id="tab-3" class="tab-content">
    <?php // Récupérer les résultats de recherche avec une date de moins de un an
    global $wpdb;
    $table_name = $wpdb->prefix . 'search_index';
    $results = $wpdb->get_results(
        "SELECT search_term, COUNT(*) AS count
     FROM $table_name
     WHERE search_date >= DATE_SUB(NOW(), INTERVAL 365 DAY)
     GROUP BY search_term
     ORDER BY count DESC"
    );

    // Afficher les résultats
    if ($results) {
        echo '<h3>Recherche(s) de cette année :</h3>';
        echo '<ul>';
        foreach ($results as $result) {
            echo '<li>' . esc_html($result->search_term) . ' (' . $result->count . ' fois)</li>';
        }
        echo '</ul>';
    } else {
        echo 'Aucun résultat trouvé';
    } ?></div>


<div id="tab-4" class="tab-content">
    <h3>Sélectionnez un intervalle de date :</h3>
    <div>
        <label for="start-date">Date de début :</label>
        <input type="date" id="start-date" name="start-date">
    </div>
    <div>
        <label for="end-date">Date de fin :</label>
        <input type="date" id="end-date" name="end-date">
    </div><br />
    <button id="submit-btn">Afficher les résultats</button><br><br>
    <div id="search-results"></div>
</div>

<form method="post" action="">
    <label for="search_term">Terme de recherche :</label>
    <select name="search_term" id="search_term">
        <option>
            <?php
            // Récupérer tous les termes de recherche distincts dans la base de données
            $terms = $wpdb->get_col(
                "SELECT DISTINCT search_term FROM $table_name ORDER BY search_term"
            );

            // Afficher les options de sélection pour chaque terme de recherche
            foreach ($terms as $term) {
                echo '<option value="' . htmlentities($term) . '">' . esc_html($term) . '</option>';
            }
            ?>
    </select>

    <label for="delete_before">Supprimer les résultats avant le :</label>
    <input type="date" name="delete_before" id="delete_before">

    <input type="submit" name="delete_data" value="Supprimer">
</form>
<?php
if (isset($_POST['delete_data'])) {
    // Récupérer les valeurs soumises par l'utilisateur
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
    $delete_before = isset($_POST['delete_before']) ? sanitize_text_field($_POST['delete_before']) : '';

    // Vérifier si le terme et la date ont été sélectionnés
    if (empty($search_term) || empty($delete_before)) {
        echo '<p style="color:red">Veuillez sélectionner un terme de recherche ou une date avant de supprimer des données.</p>';
    } else {
        // Supprimer les données correspondantes de la base de données
        $where_clause = '';
        $params = array();

        if (!empty($search_term)) {
            $where_clause .= "search_term = %s";
            $params[] = $search_term;
        }

        if (!empty($delete_before)) {
            if (!empty($where_clause)) {
                $where_clause .= ' AND ';
            }

            $where_clause .= "search_date < %s";
            $params[] = $delete_before;
        }
        
        if (!empty($where_clause)) {
            $deleted_rows = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM $table_name
                     WHERE $where_clause",
                    $params
                )
            );

            // Afficher un message de confirmation
            if ($deleted_rows > 0) {
                echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
            } else {
                echo '<p>Aucun résultat à supprimer pour ce terme de recherche et cette date.</p>';
            }
        }
    }
}
?>