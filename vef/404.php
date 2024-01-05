<h2>Personnalisation de la page 404 :</h2>
<form method='POST' class="404tag">
    Sélectionner un tag :
    <select name='tag'>
        <?php
        $tags = get_tags();
        foreach ($tags as $tag) {
            echo '<option value="' . $tag->slug . '">' . $tag->name . '</option>';
        }
        ?>
    </select>
    Afficher :
    <select name='afficher'>
        <option value='oui'>Oui</option>
        <option value='non'>Non</option>
    </select>
    <button type="button" id="404-tag">Sauvegarder</button>
</form><br /><hr><br/>

<form method='POST' class='404category'>
    Sélectionner une catégorie :
    <select name='category'>
        <?php
        $categories = get_categories();
        foreach ($categories as $category) {
            echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
        }
        ?>
    </select>
    Afficher :
    <select name='afficher-cat'>
        <option value='oui'>Oui</option>
        <option value='non'>Non</option>
    </select>
    <button type="button" id="404-category">Sauvegarder</button>
</form><br/><hr><br/>


<form method='POST' id="404recommandation">
    <label for="postReco" id="postRecoLabel">Post :</label>
    <select id="postReco" name="recommandation" required>
        <option value="">Choisir un post</option>
        <?php reco_posts();?>
    </select>

    <button id="404-reco">Recommander ce post</button>
</form><br/>


<?php
global $wpdb;
$table_name = $wpdb->prefix . '404_reco';
$results = $wpdb->get_results("SELECT * FROM $table_name");
?>
    <h3>Post recommandé :</h3>
    <table id='liste_reco' class='widefat fixed' cellspacing='0'>
        <thead>
            <tr>
                <th class="manage-column column-columnname reco">Post</th>
                <th class="manage-column column-columnname suppression" style="width:26%">Supprimer la recommandation</th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($results as $result): ?>
        <tr>
            <td><a href='<?= $result->recommandation ?>' target='_blank'> <?= $result->recommandation ?></a></td>
            <td><button class="supprimer_reco" data-id="<?php echo $result->id; ?>" style="border:none; background:transparent; cursor:pointer">&#x274C;</button></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>