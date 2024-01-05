<?php
echo '<h1>Liens VEF</h1>';

$results = get_liens_contributeurs();
?>
<table id='liens-contributeurs' class="widefat fixed" cellspacing="0">
  <thead>
    <tr>
      <th class="manage-column column-columnname liens">Liens</th>
      <th class="manage-column column-columnname statut">Statuts</th>
      <th class="manage-column column-columnname origine">Page d'origine</th>
      <th class="manage-column column-columnname origine">Section</th>
      <th class="manage-column column-columnname action">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($results as $row) :

$url = $row->url;
if (filter_var($url, FILTER_VALIDATE_URL)) {
  // Si l'URL est une adresse web valide, utiliser le code existant
  if (strpos($url, 'https://') !== 0) {
    $url = 'https://' . $url;
  } ?>
  <tr>
    <td><img src='<?= $row->favicon ?>'><a target='blank' href='<?= $url ?>'><?= $row->url ?></a></td>
<?php } elseif (filter_var($url, FILTER_VALIDATE_EMAIL)) {
  // Si l'URL est une adresse email valide, créer un lien "mailto:"
  ?>
  <tr>
    <td><img src='<?= $row->favicon ?>'><a target='blank' href='mailto:<?= $url ?>'><?= $row->url ?></a></td>
<?php } else {
  // Si l'URL n'est ni une adresse web ni une adresse email, afficher la valeur dans un élément <p>
  ?>
  <tr>
    <td><p><?= $row->url ?></p></td>
<?php } ?>

        <td><?= $row->statut ?></td>
        <td><?= $row->article_title ?></td>
        <td><?= $row->titre_section ?></td>
        <td>
          <?php wp_nonce_field('vef_shortcode_action', 'vef_liens_contributeurs_nonce'); ?>
          <button class="valider" data-id="<?php echo $row->id; ?>">&#x2714;</button>
          <button class="supprimer" data-id="<?php echo $row->id; ?>">&#x274C;</button>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>