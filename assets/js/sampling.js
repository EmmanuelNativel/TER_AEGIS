/**
* Application pour la création d'echantillons dans DAPHNE.
*/
$(document).ready(function() {
	var keep_defaut_value = true;
	var selected_sample = [];
	var list_code_init = [];

	//recupération des codes qui sont présents dans le tableau principal
	$(".row_sample_table").each(function() {
		code=$(this).attr('id');
		list_code_init.push(code);
	});
	//s'il y a des élément dans le tableau principal
	//alors il faut selectionner les bonnes lignes dans le tableau du modal pour les mettre en évidence
	if (!jQuery.isEmptyObject(list_code_init)) {
		//pour chaque première case du tableau situé dans le modal
		$("#table_add_sample tbody tr > td:first-child").each(function() {
	    	code = $(this).text();
	    	//si le code de la ligne du tableau du modal est present dans l'array list_code_init
	    	//alors on le selectionne et on ajoute le code dans l'array selected_sample
	    	if(jQuery.inArray(code, list_code_init ) >= 0){
	    		$(this).parent().toggleClass('success');
	    		selected_sample.push([code]);
	    	}
		});
	}

	//initalisation de dataTables après avoir electionner les bonnes lignes dans le tableau du modal pour les mettre en évidence
	var table = $('#table_add_sample').DataTable({
        "language": {
            "lengthMenu": "Afficher _MENU_ lignes par page",
            "zeroRecords": "Aucune entrée",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoEmpty": "Aucune entrée",
            "search": "Recherche :",
            "paginate": {
		        "first": "Début",
		        "last": "Fin",
		        "next": "Suivant",
		        "previous": "Précédent"
		    }
        }
    });

	//set tous les champs number avec la valeur par défaut
	$("#btn_defaut_nb").on('click', function()
  	{
  		val = $("#defaut_nb").val();
  		if (val != null && val !='') {
  			$('#body_sample_table input[type=number]').each(function(){
			    $(this).val(val);
			});
  		}
  	});
  	//set tous les champs date avec la valeur par défaut
  	$("#btn_defaut_date").on('click', function()
  	{
  		val = $("#defaut_date").val();
  		if (val != null && val !='') {
  			$('#body_sample_table input[type=date]').each(function(){
			    $(this).val(val);
			});
  		}
  	});
  	$('#checkbox_defaut_value').on('change', function() {
  		keep_defaut_value = $(this).prop('checked');
    });

    $(document).on('click', '.span_remove_sample', function(event) {
  		code = $(this).attr('data-code_sample');
  		$("#"+code+"").remove();
  		/*test = $("#table_add_sample tbody tr > td:first-child:contains("+code+")");
  		test.parent().toggleClass('success');*/
  		// Recherche l'index de la ligne dont la première colone == code
		var indexes = table.rows().eq( 0 ).filter( function (rowIdx) {
		    return table.cell( rowIdx, 0 ).data() === ''+code+'' ? true : false;
		} );
		
		//change la classe de l'echantillon retiré dans le tableau principal
		table.rows( indexes )
		    .nodes()
		    .to$()
		    .toggleClass('success');
  		
  		cpt = 0;
		while(selected_sample[cpt][0] != code){
    		cpt ++;
    	}
    	if (cpt <= selected_sample.length) {
    		//retire du tableau selected_sample le sample désélectionné
    		selected_sample.splice(cpt,1); 
    	}
  	});

  	//selection des sample à prélever
	$('#table_add_sample tbody').on('click', 'tr', function (e) {
        $(this).toggleClass('success');
        if ($(this).hasClass("success")) {
        	code = $(this).children().first().text();
        	//sample_st = $(this).children().eq(7).text();

        	//ajoute dans le tableau selected_sample le sample sélectionné
        	selected_sample.push([code]);        	
        }else{
        	cpt = 0;
        	code = $(this).children().first().text();
        	while(selected_sample[cpt][0] != code){
        		cpt ++;
        	}
        	if (cpt <= selected_sample.length) {
        		//retire du tableau du formulaire la ligne correspondant au code d'échantillon
        		$("#"+code+"").remove();
        		//retire du tableau selected_sample le sample désélectionné
        		selected_sample.splice(cpt,1); 
        	}
        }
    });

	//ajoute au tableau principal les samples selectionnés dans le tableau du modal
    $('#addSampleStageModal').on('hide.bs.modal', function () {
	  	var content_table ="";
	  	var nb_val = "";
	  	var date_val = "";
	  	if (keep_defaut_value) {
	  		nb_val = $("#defaut_nb").val();
	  		date_val = $("#defaut_date").val();
	  	}
		$.each(selected_sample, function(index, sample_array) {
		  if($("#body_sample_table #"+sample_array[0]+"").length == 0) {
		  	input_number ="<input id='nb_"+sample_array[0]+"' class='form-control' type='number' min='0' name='nb_"+sample_array[0]+"' value='"+nb_val+"' >";
		  	input_date ="<input id='date_"+sample_array[0]+"' class='form-control' type='date' name='date_"+sample_array[0]+"' value='"+date_val+"' >";

		  	content_table = "<tr id="+sample_array[0]+"> <td>"+sample_array[0]+"</td>" ;
		  	content_table += "<td>"+input_number+"</td> <td>"+input_date+"</td>" ;
		  	content_table += "<td><div> <span title='Retirer' class='close span_remove_sample' data-code_sample='"+sample_array[0]+"'><span>×</span></span></div></td>" ;
		  	content_table += "</tr>" ;

			$('#body_sample_table').append(content_table);
		  }
		});
	});
});

