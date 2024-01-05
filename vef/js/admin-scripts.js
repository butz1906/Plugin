jQuery(document).ready(function($) {
    $('.delete-button').on('click', function() {
        var button_id = $(this).data('button-id');
        var confirm_delete = confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');
        if (confirm_delete) {
            var data = {
                'action': 'vef_delete_button',
                'button_id': button_id,
                'nonce': $('#vef_shortcode_nonce').val()
            };
            $.post(ajaxurl, data, function(response) {
                if (response === 'success') {
                    $('.delete-button[data-button-id="' + button_id + '"]').parent().remove();
                }
            }); 
        }       
    });

    $('.supprimer').on('click', function() {
        var button_id = $(this).data('id');
        var confirm_delete = confirm('Êtes-vous sûr de vouloir supprimer ce lien ?');
        if (confirm_delete) {
            var data = {
                'action': 'vef_delete_liens',
                'id': button_id,
                'nonce': $('#vef_liens_contributeurs_nonce').val()
            };
            $.post(ajaxurl, data, function(response) {
                if (response) {
                    location.reload();
                }
            });
        }       
    });

    $('.valider').on('click', function() {
        var button_id = $(this).data('id');
        var confirm_valider = confirm('Êtes-vous sûr de vouloir valider ce lien ?');
        if (confirm_valider) {
            var data = {
                'action': 'vef_validation_liens',
                'id': button_id,
                'nonce': $('#vef_liens_contributeurs_nonce').val()
            };
            $.post(ajaxurl, data, function(response) {
                if (response) {
                    location.reload();
                }
            });
        }       
    });

    $('#404-tag').click(function() {
        var tag = $('select[name="tag"]').val();
        var afficher = $('select[name="afficher"]').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'update_404_tag',
              tag: tag,
              afficher: afficher
            },
            success: function(response) {
              console.log(response);
            }
          });
        });

    $('#404-category').click(function() {
        var category = $('select[name="category"]').val();
        var afficher = $('select[name="afficher-cat"]').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_404_category',
                category: category,
                afficher: afficher
            },
            success: function(response) {
                console.log(response);
            }
            });
        });
        jQuery('#404-reco').on('click', function(){
            var recommandation = jQuery('#postReco').val();
            // Envoie les données au serveur via Ajax
            jQuery.ajax({
              url: ajaxurl,
              type: 'POST',
              data: {
                action: 'save_recommandation',
                recommandation: recommandation,
              },
              dataType: 'json',
              success: function(response) {
                console.log(response);
              },
              error: function(response) {
                console.log(response);
                alert('Une erreur s\'est produite. Veuillez réessayer.');
              }
            });
          });
          jQuery('.supprimer_reco').on('click', function() {
            var button_id = jQuery(this).data('id');
            var confirm_delete = confirm('Êtes-vous sûr de vouloir supprimer cette recommandation ?');
            if (confirm_delete) {
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data : {
                        action: 'reco_delete',
                        id: button_id,
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }       
        });
});
