
//chargement de la première page pour chaque onglet

var tabsIds = ['members', 'partners', 'trials', 'datasets'];
for (var tabId of tabsIds) {
  loadTabElements(tabId, 1);
}



/*******************************************************************************
 Charge les elements du tableau à afficher en fonction du numéro de page
*******************************************************************************/
function loadTabElements(tab_id, num_page){
  var project_code = $('#hiddenProjectCode').val();
  $.ajax({
    url: SiteURL + '/projects/loadTabElements/' + num_page,
    type: 'POST',
    dataType: 'json',
    data: {
      'project_code': project_code,
      'tab_id': tab_id
    },
    success: function(response){
      $('#'+ tab_id + " .ajaxTable").html(response.generatedHtml);
    }
  });
}

/*******************************************************************************
  Handler pour les clics sur les numéros de pagination
*******************************************************************************/
$(".tab-content").on('click', '.pagination a',  function(e) {
 e.preventDefault();
  var num_page = $(this).attr('data-ci-pagination-page');
  var tab_id = $(this).closest('[tabId]').attr('tabId');
  loadTabElements(tab_id, num_page);
});
