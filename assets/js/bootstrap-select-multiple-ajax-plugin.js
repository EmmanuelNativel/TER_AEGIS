/**
 * Plugin pour bootstrap-select <https://silviomoreto.github.io/bootstrap-select/>
 * Permet d'actualiser la liste des options d'un selecteur en fonction d'un terme
 * recherché, via une requête AJAX.
 *
 * Usage: <select class="selectpicker" data-url="URL/DE/LA/REQUETE/AJAX" data-live-search="true"></select>
 *
 * Attention: La requete AJAX doit retourner une liste d'options au format JSON avec pour chaque option les clés "name" et "value".
 *
 *
 */

$(window).load(function() {

  // Déclaration des champs concernés
  var $input_field = $('.selectpicker[data-url]').parent().find('input[type="text"]');

  // Appel de la recherche en cas de saisie de texte
  $input_field.keyup(function(event) {

    var searched_term = $(this).val().trim();

    if (searched_term && $.inArray(event.which, [37,38,39,40]) == -1) // Evite la détection des arrowkeys pour permettre à l'utilisateur de naviguer dans les résultats
    {
      var $select_synchro = $(this).parent().parent().parent().children('select');
      var ajax_url = $select_synchro.data('url');
      var $loading_div = $(this).parent().parent().parent().find('.no-results');
      synchronize(searched_term, ajax_url, $select_synchro, $loading_div);
    }
  });

  // Modifie la liste des options en fonction du terme demandé
  function synchronize(searched_term, ajax_url, $select_synchro, $loading_div) {
        $loading_div.html('<img src="' + ImgURL + 'ajax-loader.gif' +'">');
        $.ajax({
                url: SiteURL + ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    term: searched_term
                }
            })
            .fail(function(err) {
              console.error(err);
              $loading_div.html('Echec de la connexion');
            })
            .done(function(res) {
              if (res.type !== 'error') {
                var list_options = res;
                var options_str = '';
                for (var i = 0; i < list_options.length; i++) {
                  option = list_options[i];
                  if (option.tag) {
                    options_str += '<option data-content="'+ option.name +' <span class=\'label label-'+ option.tag.type +'\'>'+ option.tag.label +'</span>" value=' + option.value + '>' + option.name + '</option>';
                  }
                  else {
                    options_str += '<option value=' + option.value + '>' + option.name + '</option>';
                  }

                }

                // Si l'utilisateur a selectionné des options
                var selected_values = $select_synchro.val();
                if (selected_values) {
                  var selected_options = "";

                  for (var j = 0; j < selected_values.length; j++) {
                    if ($($select_synchro.id + ' option[value="' + selected_values[j] + '"]')[0]) {
                      selected_options += $($select_synchro.id + ' option[value="' + selected_values[j] + '"]')[0].outerHTML;
                    }
                  }
                  options_str = selected_options + options_str;
                }

                $select_synchro.html(options_str).selectpicker('refresh');
              }
              else {
                console.error(res.message);
              }
            });
            // .always(function() {
            //   console.log('end');
            // });
    }
});
