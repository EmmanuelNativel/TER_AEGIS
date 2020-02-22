$(document).ready(function() {
	var selected_sample = [];
	var list_code_init = [];
	var select_picker_lab = "";
	var nb_sample_output = $('#body_sample_output_table tr').length;
	$("#btn_addSampleOutput").tooltip('disable');

	//enlève les messages d'erreurs à l'interieur d'un modal lors de la fermeture de celui-ci
	$('#addOperationModal').on('hidden.bs.modal', function () {
  		$('#error_add_operation_type').text('');
	});
	$('#updateOperationModal').on('hidden.bs.modal', function () {
  		$('#error_update_operation_type').text('');
  		$('#error_update_operation_name').text('');
	});
	$('#deleteOperationModal').on('hidden.bs.modal', function () {
  		$('#error_delete_operation_type').text('');
	});


	//recupération des codes qui sont présents dans le tableau principal
	$(".row_sample_input_table").each(function() {
		code=$(this).attr('id');
		list_code_init.push(code);
	});
	//s'il y a des élément dans le tableau principal
	//alors il faut selectionner les bonnes lignes dans le tableau du modal pour les mettre en évidence
	if (!$.isEmptyObject(list_code_init)) {
		//pour chaque première case du tableau situé dans le modal
		$("#table_add_sample_input_list tbody tr > td:first-child").each(function() {
	    	code = $(this).text();
	    	//si le code de la ligne du tableau du modal est present dans l'array list_code_init
	    	//alors on le selectionne et on ajoute le code dans l'array selected_sample
	    	if(jQuery.inArray(code, list_code_init ) >= 0){
	    		nb = $(this).parent().children().eq(2).text();
	    		$(this).parent().toggleClass('success');
	    		selected_sample.push([code, nb]);
	    	}
		});

	}
	//initalisation de dataTables après avoir electionner les bonnes lignes dans le tableau du modal pour les mettre en évidence
	var table = $('#table_add_sample_input_list').DataTable({
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

	//on click x remove sample from sample table input
	$(document).on('click', '.span_remove_sample', function(event) {
  		code = $(this).attr('data-code_sample');
  		$("#"+code+"").remove();
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

	//on click x remove sample from sample table output
  	$(document).on('click', '.span_remove_sample_output', function(event) {
  		nb_output = $(this).attr('data-nb_output');
  		$("#"+nb_output+"").remove();
  	});

	//selection des sample input
	$('#table_add_sample_input_list tbody').on('click', 'tr', function (e) {
        $(this).toggleClass('success');
        if ($(this).hasClass("success")) {
        	code = $(this).children().first().text();
        	nb = $(this).children().eq(2).text();
        	
        	//ajoute dans le tableau selected_sample le sample sélectionné
        	selected_sample.push([code, nb]);
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
    $('#addSampleInput').on('hide.bs.modal', function () {
	  	var content_table ="";

		$.each(selected_sample, function(index, sample_array) {
		  if($("#body_sample_input_table #"+sample_array[0]+"").length == 0) {
		  	input_number_remove ="<input id='nb_remove_input-"+sample_array[0]+"' class='form-control' type='number' min='0' name='nb_remove_input-"+sample_array[0]+"' >";
		  	input_labo = create_select_lab(sample_array[0]);
		  	input_sample_nb = "<input id='nb_input-"+sample_array[0]+"' class='form-control' type='number' min='0' name='nb_input-"+sample_array[0]+"' value='"+sample_array[1]+"' readonly>";

		  	content_table = "<tr id="+sample_array[0]+" class='row_sample_input_table'>";

		  	content_table += "<td>"+sample_array[0]+"</td>";	//sample code
		  	content_table += "<td>"+input_labo+"</td>";			//select lab
		  	content_table += "<td>"+input_sample_nb+"</td>"; 	//NB
		  	content_table += "<td>"+input_number_remove+"</td>";		//input number
		  	content_table += "<td><div> <span title='Retirer' class='close span_remove_sample' data-code_sample='"+sample_array[0]+"'><span>×</span></span></div></td>" ;
		  	content_table += "</tr>" ;

			$('#body_sample_input_table').append(content_table);
  			$('.selectpicker').selectpicker(); //initalisation des selectpickers

		  }
		});
	});

    $('#btn_addSampleOutput').on('click', function (e) {
    	sample_type = $('#sample_type').val();
    	sample_nb = $('#sample_nb').val();

    	if(sample_type == '' || sample_nb=='' || sample_nb <0){
			$("#btn_addSampleOutput").tooltip('enable');
			$("#btn_addSampleOutput").tooltip('show');
			$("#btn_addSampleOutput").tooltip('disable');

    	}else{
    		var content_table ="";
    		nb_sample_output ++;
    		input_sample_type = "<input id='sample_output_type-"+nb_sample_output+"' class='form-control' type='text' name='sample_output_type-"+nb_sample_output+"' value='"+sample_type+"' readonly>";
			input_sample_nb = "<input id='sample_output_nb-"+nb_sample_output+"' class='form-control' type='number' min='0' name='sample_output_nb-"+nb_sample_output+"' value='"+sample_nb+"' readonly>";
		  	input_labo = create_select_sample_output_lab("nb_"+nb_sample_output+"");

		  	content_table = "<tr id=nb_"+nb_sample_output+">";

		  	content_table += "<td>"+input_sample_type+"</td>";	//sample type
		  	content_table += "<td>"+input_sample_nb+"</td>";		//sample nb
		  	content_table += "<td>"+input_labo+"</td>";		//sample labo
		  	content_table += "<td><div> <span title='Retirer' class='close span_remove_sample_output' data-nb_output='nb_"+nb_sample_output+"'><span>×</span></span></div></td>" ;
		  	content_table += "</tr>";
			$('#body_sample_output_table').append(content_table);
			$('#sample_type').val('');
    		$('#sample_nb').val('');
  			$('.selectpicker').selectpicker(); //initalisation des selectpickers
    	}

    });

	function create_select_lab(id_code) {
		var string_select_return ="<select class='selectpicker' id='lab_select-"+id_code+"'";
		string_select_return += "data-size='5' name='lab_select-"+id_code+"' data-live-search='true' data-title='Labo'>";
		$.each(list_lab, function(index, value){
			string_select_return +="<option value='"+value['lab_id']+"'> "+value['lab_code']+" </option>";
		});

		string_select_return +="</select>";
		return string_select_return;
	}
	function create_select_sample_output_lab(id_code) {
		var string_select_return ="<select class='selectpicker' id='lab_select_output-"+id_code+"'";
		string_select_return += "data-size='5' name='lab_select_output-"+id_code+"' data-live-search='true' data-title='Labo'>";
		$.each(list_lab, function(index, value){
			string_select_return +="<option value='"+value['lab_id']+"'> "+value['lab_code']+" </option>";
		});

		string_select_return +="</select>";
		return string_select_return;
	}

	function add_operation_type_in_select(id, operation_name){
		$.each($('.selectpicker_operation'), function (i, item) {
		    $(this).append($('<option>', { 
		        value: id,
		        text : operation_name 
		    }));
		    $(this).selectpicker('refresh');
		});
	}
	function update_operation_type_in_select(id, operation_name){
		$.each($('.selectpicker_operation option[value="'+id+'"]'), function (i, item) {
		    $(this).text(operation_name);
		});
		$('.selectpicker_operation').selectpicker('refresh');
	}
	function delete_operation_type_in_select(id){
		$.each($('.selectpicker_operation option[value="'+id+'"]'), function (i, item) {
		    $(this).remove();
		});
		$('.selectpicker_operation').selectpicker('refresh');
	}


/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- Ajouter un type d'opération ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
  $(document).on('click', "#btn_add_operation", function(e) {
    operation_name = $("#add_operation_name").val();
	if (operation_name != '') {
	    url = window.location.origin +"/daphne.cirad.fr/index.php/operations/create_operation_type";
	    //url = window.location.origin +"/index.php/operations/create_operation_type";
	    $.ajax({
	      type: "POST",
	      url: url, 
	      data: {
	        operation_name: operation_name
	      },
	      dataType: "json",
	      cache:false,
	      success: function(id_operation){
	        
	        add_operation_type_in_select(id_operation, operation_name);
			$("#error_add_operation_type").text("");
	        $('#addOperationModal').modal('hide');

	      },
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(JSON.stringify(data));
	        alert("Impossible de créer une nouvelle opération");
	      }
	    });
	}else{
		$("#error_add_operation_type").text("Le nom de l'opération est obligatoire");
	}
  });

/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- Modifier un type d'opération ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
  $(document).on('click', "#btn_update_operation", function(e) {
    operation_id = $("#update_operation_type").val();
    operation_name = $("#update_operation_name").val();
    if (operation_id != null && operation_id > 0 && operation_name != '') {
	    url = window.location.origin +"/daphne.cirad.fr/index.php/operations/update_operation_type";
	    //url = window.location.origin +"/index.php/operations/update_operation_type";
	    $.ajax({
	      type: "POST",
	      url: url, 
	      data: {
	      	operation_id: operation_id,
	        operation_name: operation_name
	      },
	      dataType: "json",
	      cache:false,
	      success: function(data){
	        
	        update_operation_type_in_select(operation_id, operation_name);
	        $('#updateOperationModal').modal('hide');

	      },
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(JSON.stringify(data));
	        alert("Impossible de modifer l'opération");
	      }
	    });
	}else{
		$("#error_update_operation_type").text("");
		$("#error_update_operation_name").text("");
		if (operation_id == null || operation_id <= 0) {
			$("#error_update_operation_type").text("Veuillez choisir un type d'opération à modifier");
		}
		if (operation_name == '') {
			$("#error_update_operation_name").text("Le nom de l'opération est obligatoire");
		}
	}
  });

/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- Supprimer un type d'opération ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
  $(document).on('click', "#btn_delete_operation", function(e) {
    operation_id = $("#delete_operation_type").val();
	if (operation_id != null && operation_id > 0){
	    url = window.location.origin +"/daphne.cirad.fr/index.php/operations/delete_operation_type";
	    //url = window.location.origin +"/index.php/operations/delete_operation_type";
	    $.ajax({
	      type: "POST",
	      url: url, 
	      data: {
	      	operation_id: operation_id,
	      },
	      dataType: "json",
	      cache:false,
	      success: function(data){
	        
	        delete_operation_type_in_select(operation_id);
	        $('#deleteOperationModal').modal('hide');

	      },
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(JSON.stringify(data));
	        alert("Impossible de supprimer l'opération");
	      }
	    });
	}else{
		$("#error_delete_operation_type").text("Veuillez choisir un type d'opération à supprimer");
	}
  });

});