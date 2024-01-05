// Cacher tous les éléments de contenu sauf le premier
jQuery('.tab-content').not(':first').hide();

// Gérer les clics sur les liens d'onglets
jQuery('.tab-link').click(function() {
    // Récupérer l'ID de l'élément de contenu à afficher
    var tabId = jQuery(this).attr('data-tab');

    // Masquer tous les éléments de contenu et supprimer la classe "active" des liens d'onglets
    jQuery('.tab-content').hide();
    jQuery('.tab-link').removeClass('active');

    // Afficher l'élément de contenu correspondant et ajouter la classe "active" au lien d'onglet correspondant
    jQuery('#' + tabId).show();
    jQuery(this).addClass('active');

    // Empêcher le comportement par défaut du lien
    return false;
});
jQuery('#submit-btn').on('click', function(e) {
    e.preventDefault();
    var startDate = jQuery('#start-date').val();
    var endDate = jQuery('#end-date').val();
    var data = {
        'action': 'search_data',
        'start_date': startDate,
        'end_date': endDate,
    };
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: data,
        success: function(response) {
            jQuery('#search-results').html(response);
        },
        error: function(xhr, status, error) {
            console.log(error);
        }})
});

function showOptions() {
    var selectType = document.getElementById("selectType");
    var selectOption = document.getElementById("selectOption");
    var selectOptionLabel = document.getElementById("selectOptionLabel");

    selectOption.disabled = false;

    if (selectType.value === "page") {
        selectOptionLabel.innerHTML = "Page :";
        getPageOptions();
    } else if (selectType.value === "post") {
        selectOptionLabel.innerHTML = "Articles :";
        getPostOptions();
    } else if (selectType.value === "categorie") {
        selectOptionLabel.innerHTML = "Catégorie :";
        getCategoryOptions();
    } else if (selectType.value === "tag") {
        selectOptionLabel.innerHTML = "Tag :";
        getTagOptions();
    } else {
        selectOption.disabled = true;
    }
}

function getPageOptions() {
    jQuery(document).ready(function($) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_get_pages'
            },
            success: function(response) {
                $('#selectOption').html(response);
            }
        });
    });
}

function getPostOptions() {
    jQuery(document).ready(function($) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_get_posts'
            },
            success: function(response) {
                $('#selectOption').html(response);
            }
        });
    });
}

function getCategoryOptions() {
    jQuery(document).ready(function($) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_get_categories'
            },
            success: function(response) {
                $('#selectOption').html(response);
            }
        });
    });
}

function getTagOptions() {
    jQuery(document).ready(function($) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_get_tags'
            },
            success: function(response) {
                $('#selectOption').html(response);
            }
        });
    });
}

// Récupère les valeurs des champs "keyword" et "redirect_option"
jQuery('#lier').on('click', function(){
    var keyword = jQuery('#keyword').val();
    var redirect_option = jQuery('#selectOption').val();
    var title = jQuery('#selectOption option:selected').text();
    // Vérifie si les variables ne sont pas vides
  if (keyword === '') {
    alert('Veuillez entrer un mot-clé');
    return;
  } 
  if (redirect_option === '') {
    alert('Veuillez sélectionner une page de redirection');
    return;
  }
    // Envoie les données au serveur via Ajax
    jQuery.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'save_redirection',
        nonce: search_index_object.nonce,
        keyword: keyword,
        redirect_option: redirect_option,
        title: title
      },
      dataType: 'json',
      success: function(response) {
        location.reload();
      },
      error: function(response) {
        console.log(response);
        alert('Une erreur s\'est produite. Veuillez réessayer.');
      }
    });
  });

//Suppression d'une redirection
jQuery('.supprimer_redirect').on('click', function() {
    var button_id = jQuery(this).data('id');
    var confirm_delete = confirm('Êtes-vous sûr de vouloir supprimer cette redirection ?');
    if (confirm_delete) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data : {
                action: 'redirect_delete',
                id: button_id,
            },
            success: function(response) {
                location.reload();
            }
        });
    }       
});

jQuery('.search-form').submit(function(e) {
    e.preventDefault(); // Empêcher le formulaire de se soumettre normalement
    var searchTerm = jQuery('.search-field').val(); // Récupérer le terme de recherche saisi par l'utilisateur
    jQuery.ajax({
        type: 'POST',
        url: '/wp-json/my-plugin/v1/search', // URL de l'API REST de WordPress
        data: {
            search_term: searchTerm // Terme de recherche à envoyer
        },
        success: function(response) {
            jQuery('.search-results').html('<h2>Résultat de la recherche :</h2>' + response); // Afficher les résultats de recherche
        },
        error: function(xhr, status, error) {
            console.log('Erreur AJAX : ' + status + ' - ' + error);
        }
    });
});

jQuery('.post-link-form').submit(function(e) {
    e.preventDefault(); // Empêcher le formulaire de se soumettre normalement
    var postId = jQuery('#post-id').val(); // Récupérer l'ID du post saisi par l'utilisateur
    jQuery.ajax({
        type: 'POST',
        url: '/wp-json/my-plugin/v1/get-post-by-id',
        data: {
          post_id: postId // ID du post à envoyer
        },
        success: function(response) {
          jQuery('.post-link-results').html('<h2>Post :</h2><a href="' + response.link + '">' + response.title + '</a>'); // Afficher le lien du post
        },          
        error: function(xhr, status, error) {
          alert('Article non trouvé');
        }
    });
});

jQuery(document).ready(function($) {
    // Sélectionnez les éléments HTML
    var $post_id = $('#post-id');
    var $post_position = $('#post-position');
    var $save_button = $('#save-search-inject');

    // Fonction pour vérifier si les champs sont renseignés
    function checkFields() {
        return $post_id.val() !== '' && $post_position.val() !== '';
    }

    // Désactiver le bouton au chargement de la page
    $save_button.prop('disabled', true);

    // Activer / désactiver le bouton en fonction des changements dans les champs
    $('.inject-post-result').on('input', function(event) {
        if (checkFields()) {
            $save_button.prop('disabled', false);
        } else {
            $save_button.prop('disabled', true);
        }
    });})

// Enregistrer les données lorsque le bouton est cliqué
jQuery('#save-search-inject').on('click', function(event) {
    var keyword = jQuery('#keyword').val();
    var post_id = jQuery('#post-id').val();
    var resultat_position = jQuery('#result-position').val();
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                'action': 'save_search_inject',
                'keyword': keyword,
                'post_id': post_id,
                'resultat_position': resultat_position
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                location.reload();
            }
        });
    }
);

//Suppression d'une injection
jQuery('.supprimer_inject').on('click', function() {
    var button_id = jQuery(this).data('id');
    var confirm_delete = confirm('Êtes-vous sûr de vouloir supprimer cette injection ?');
    if (confirm_delete) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data : {
                action: 'inject_delete',
                id: button_id,
            },
            success: function(response) {
                location.reload();
            }
        });
    }       
});

// Gérer le changement de valeur du select
jQuery(document).ready(function($) {
    $('.modifier_position').on('click', function(e) {
        e.preventDefault();
        var id = $(this).closest('tr').find('.update-position-form').data('id');
        var position = $(this).closest('tr').find('#resultat_position').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_position',
                id: id,
                position: position
            },
            success: function(response) {
                location.reload();
            }
        });
    });
});