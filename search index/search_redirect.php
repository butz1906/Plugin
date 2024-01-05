<h2>Redirection des recherches</h2>

<form id="myForm">
    <label for="keyword">Mot-clé :</label>
    <input type="text" id="keyword" name="keyword" required><br><br>

    <label for="selectType">Type :</label>
    <select id="selectType" name="redirect_type" onchange="showOptions()" required>
        <option value="">Choisir un type</option>
        <option value="page">Page</option>
        <option value='post'>Article</option>
        <option value="categorie">Catégorie</option>
        <option value="tag">Tag</option>
    </select><br><br>

    <label for="selectOption" id="selectOptionLabel">Option :</label>
    <select id="selectOption" name="redirect_option" disabled required>
        <option value="">Choisir une option</option>
    </select><br><br>

    <button id="lier">Lier le mot-clé</button>
</form><br/>


<?php
global $wpdb;
$table_name = $wpdb->prefix . 'search_redirection';
$results = $wpdb->get_results("SELECT * FROM $table_name");
?>
    <h3>Redirection(s) enregistrée(s) :</h3>
    <table id='liste_redirection' class='widefat fixed' cellspacing='0'>
        <thead>
            <tr>
                <th class="manage-column column-columnname keyword" style="width:20%">Mots-clés</th>
                <th class="manage-column column-columnname redirection">URL de redirection</th>
                <th class="manage-column column-columnname suppression" style="width:26%">Supprimer la redirection</th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($results as $result): ?>
        <tr>
            <td><?= $result->keyword ?></td>
            <td><?= $result->redirect_option ?></td>
            <td><button class="supprimer_redirect" data-id="<?php echo $result->id; ?>" style="border:none; background:transparent; cursor:pointer">&#x274C;</button></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>