$(document).ready(function() {
  const my_dataTable_language =  {
      "lengthMenu": "Afficher _MENU_ lignes par page",
      "loadingRecords": "Chargement des données...",
      "processing": "Chargement...",
      "zeroRecords": "Aucune entrée",
      "info": "Page _PAGE_ sur _PAGES_",
      "infoEmpty": "",
      "infoFiltered": "(filtrés à partir d'un total de _MAX_ entrées)",
      "search": "Recherche :",
      "paginate": {
          "first": "Début",
          "last": "Fin",
          "next": "Suivant",
          "previous": "Précédent"
      }
   }

   //Texte des boutons selectAll et deselectALL des selectPickers 
   $.fn.selectpicker.defaults = {
     selectAllText: 'Tout sélectionner',
     deselectAllText: 'Tout désélectionner'
   };

//==============================================================================
//        Initialisation de la table de l'onglet Dispositif Expérimental
//==============================================================================
  $('#table_disp_exp').DataTable({
    "scrollX": true,
    "language": my_dataTable_language
  });

//==============================================================================
//           Initialisation de la table de l'onglet Observations
//==============================================================================

  var trial_code = $('#dataviz').attr('trial_code')
  //Récupération des colonnes variables puis lors du success initialisation du datatable
  $.ajax( {
    url: SiteURL + '/Trials/ajaxLoadTrialVariablesName/' + trial_code,
    dataType: 'json',
    success: function ( response ) {

    //Initialisation des colonnes
    var dataTable_fixed_columns = [
      { "data" : "unit_code" , "title" : "Unité exp." },
      { "data" : "obs_date" , "title" : "Date" }
    ]

    var dataTable_variables_columns = []
    for (var columnName of response.columns) {
      dataTable_variables_columns.push({ "data" : columnName , "title" : columnName })
    }

    var dataTable_all_columns = dataTable_fixed_columns.concat(dataTable_variables_columns)


    $('#table_observations').DataTable( {
          "scrollX": true,
          "serverSide": true,
          "processing": true,
          "ajax": {
              url: SiteURL + '/Trials/ajaxLoadDatatableObservations/' + trial_code,
              type: "post",
              data: {
                fixedColumns: dataTable_fixed_columns
              }
          },
          "columns": dataTable_all_columns,
          "columnDefs": [
            {
               targets: '_all',
               defaultContent: ''
            }
         ],
         "language": my_dataTable_language
      } );

    }
  }); //end variables ajax call

//==============================================================================
//     Met à jour la taille du header de tous les datatables lors du clic sur un onglet.
//    (Car jquery n'arrive pas à calculer la bonne taille quand l'onglet n'est pas visible)
//==============================================================================

  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
     $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust();
  });

//==============================================================================
//           Lors de la séléction d'un type de visualisation à afficher
//==============================================================================

$('#datavizSelect').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

    var selected = $(this).find('option').eq(clickedIndex)
    var selectedDatavizCode = selected.val();
    var selectedDatavizTitle = selected.text();
    //console.log("hello");
    

    $.ajax({
      url: SiteURL + '/Trials/ajaxLoadDataviz/',
      data: {
        datavizCode: selectedDatavizCode,
        datavizTitle : selectedDatavizTitle,
        trial_code: trial_code
      },
      type: 'POST',
      dataType: 'json',
      success: function(response){
        //console.log(response);
        $('#datavizDiv').html(response ? response.datavizHtml : "");
        $('.selectpicker').selectpicker('render'); //réinitialisation des selectpicker sinon ne s'affichent pas
      }
    });
});

}); //end documentReady
