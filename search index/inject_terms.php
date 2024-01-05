<h2>Injecter des résultats dans une recherche</h2>

<!-- Afficher le champ de recherche -->
<form role="search" method="post" class="search-form">
    <label>Entrez un mot-clé :</label>
    <input type="search" class="search-field" placeholder="Rechercher..." value="" name="s" id="keyword" title="Rechercher :">
</form><br/>
<!-- Afficher le formulaire pour entrer l'ID du post -->
<form method="post" class="post-link-form">
    <label for="post-id">Entrez l'ID du post :</label>
    <input type="text" id="post-id" name="post_id">
  </form><br/>
  <!-- Afficher le formulaire pour entrer la position du post dans les résultats de recherche -->
  <form method="post" class="inject-post-result" onsubmit="return false;">
    <label for="post-position">Entrez la position du post dans les résultats de recherche (entre 1 et 15) :</label>
    <input type="number" id="result-position" name="resultat_position" min="1" max="15">
  </form>
  <!-- Bouton pour valider les champs et enregistrer les données -->
  <button type="button" id="save-search-inject">Enregistrer</button>
  
  <!-- Afficher les résultats de recherche -->
<div class="post-link-results"></div>
<div class="search-results"></div>

<?php
global $wpdb;
$table_name = $wpdb->prefix . 'search_inject';
$results = $wpdb->get_results("SELECT * FROM $table_name");
?>
    <h3>Redirection(s) enregistrée(s) :</h3>
    <table id='liste_redirection' class='widefat fixed' cellspacing='0'>
        <thead>
            <tr>
                <th class="manage-column column-columnname keyword" style="width:20%">Mots-clés</th>
                <th class="manage-column column-columnname redirection">Post injecté</th>
                <th class="manage-column column-columnname position">Position</th>
                <th class="manage-column column-columnname suppression" style="width:26%">Supprimer l'injection'</th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($results as $result): ?>
        <tr>
            <td><?= $result->keyword ?></td>
            <td><?php echo get_the_title($result->post_id); ?></td>
            <td>
  <form class="update-position-form" data-id="<?php echo $result->id; ?>">
    <select name="resultat_position" id="resultat_position">
      <?php
        for ($i = 1; $i <= 15; $i++) {
          $selected = ($i == $result->resultat_position) ? 'selected="selected"' : '';
          echo "<option value=\"$i\" $selected>$i</option>";
        }
      ?>
    </select>
    <button class='modifier_position' type="submit">Enregistrer</button>
  </form>
</td>
            <td><button class="supprimer_inject" data-id="<?php echo $result->id; ?>" style="border:none; background:transparent; cursor:pointer">&#x274C;</button></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>