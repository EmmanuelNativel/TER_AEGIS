$(document).ready(function() {
  url_ajax = SiteURL + '/samples/ajax_get_sample_for_consultation'
  //url_ajax = window.location.origin +"/daphne.cirad.fr/index.php/samples/ajax_get_sample_for_consultation";
    //url_ajax = window.location.origin +"/index.php/samples/ajax_get_sample_for_consultation";
	var selected_sample = [];
	var ENTER_KEY = 13;
	var table;
	table = $('#table_consultation_sample').DataTable({
		"ajax": {
            url : url_ajax,
            type : 'GET'
        },
		scrollX: true,
		"dom" : "<'row'<'col-sm-3'l><'col-sm-9'f>>r"
		+"t"
		+"<'row'<'col-sm-3'i><'col-sm-9'p>>",
        "language": {
            "lengthMenu": "Afficher _MENU_ lignes par page",
            "loadingRecords": "Chargement des échantillons",
            "zeroRecords": "Aucune entrée",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoEmpty": "",
            "search": "Recherche :",
            "paginate": {
		        "first": "Début",
		        "last": "Fin",
		        "next": "Suivant",
		        "previous": "Précédent"
		    }
        }
    });
	$("#table_consultation_sample_length").addClass('xs_float_left');
	$("#table_consultation_sample_filter").addClass('xs_float_left');
	$("#table_consultation_sample_filter input").removeClass('input-sm');
	$("#table_consultation_sample_filter input").addClass('input-xs');

	$('#table_consultation_sample_filter input').on('keyup', function (event){

		if (event.keyCode == ENTER_KEY) {
			length_search = $('#table_consultation_sample_filter input').val().length;
			if (length_search == ENTER_KEY) {
				mString = $('#table_consultation_sample_filter input').val().substring(0,length_search-1);

				//$('#table_consultation_sample_filter input').val(mString);
				table.search(mString).draw();
			}
		}
	});


	$(document).on('click', '#btn_select_trial', function(event) {
  		essai = $("#select_essai").val();

  		if (essai != null && essai != '') {
  			table.rows().eq( 0 ).each( function (idx) {
			    if ( table.cell( idx, 7 ).data() === ''+essai+'' ) {
			    	code = table.cell( idx, 0 ).data();
			    	sample_type = table.cell( idx, 1 ).data();

			    	//ajoute dans le tableau selected_sample le sample sélectionné
		        	selected_sample.push([code, sample_type]);
		        	add_row_table_selected([code, sample_type]);
			        table.row( idx ).nodes().to$().addClass( 'success' );
			    }
			});
  		}
	});

	$(document).on('click', '#btn_deselect_trial', function(event) {
  		essai = $("#select_essai").val();

  		if (essai != null && essai != '') {
  			table.rows().eq( 0 ).each( function (idx) {
			    if ( table.cell( idx, 7 ).data() === ''+essai+'' ) {
			    	code = table.cell( idx, 0 ).data();

			    	//ajoute dans le tableau selected_sample le sample sélectionné
			    	cpt = 0;
		        	while(cpt < selected_sample.length && selected_sample[cpt][0] != code){
		        		cpt ++;
		        	}
		        	if (cpt <= selected_sample.length) {
		        		//retire du tableau du formulaire la ligne correspondant au code d'échantillon
		        		$("#"+code+"").remove();
		        		//retire du tableau selected_sample le sample désélectionné
		        		selected_sample.splice(cpt,1);
		        	}

			        if(table.row( idx ).nodes().to$().is('.success')){
			        	table.row( idx ).nodes().to$().removeClass('success');
			        }
			    }
			});
  		}
	});

	//selection des sample pour extraction de données ou de code-barres
	$('#table_consultation_sample tbody').on('click', 'tr', function (e) {
  		$("#p_btn_extract_error").text('');
        $(this).toggleClass('success');
        if ($(this).hasClass("success")) {
        	code = $(this).children().first().text();
        	sample_type = $(this).children().eq(1).text();

        	//ajoute dans le tableau selected_sample le sample sélectionné
        	selected_sample.push([code, sample_type]);
        	add_row_table_selected([code, sample_type]);
        }else{
        	cpt = 0;
        	code = $(this).children().first().text();
        	while(cpt < selected_sample.length && selected_sample[cpt][0] != code){
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

	//on click sur la croix, enlève un échantilon du tableau des échantillons sélectionnés
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
		while(cpt < selected_sample.length && selected_sample[cpt][0] != code){
    		cpt ++;
    	}
    	if (cpt <= selected_sample.length) {
    		//retire du tableau selected_sample le sample désélectionné
    		selected_sample.splice(cpt,1);
    	}
  	});
  	$(document).on('click', '#btn_deselect_field', function(event) {
  		$('#add_field_extract_code').selectpicker('deselectAll');
    	$('#add_field_extract_code').selectpicker('refresh');
  	});



  	$(document).on('click', '#btn_extract_code', function(event) {

  		if($("#body_selected_sample_table tr").length > 0){
  			$("#p_btn_extract_error").text('');
  			$('#ExtractCodeModal').modal('show');
  		}else{
  			$("#p_btn_extract_error").text("Choisissez au moins un échantillon");
  		}
  	});

  	$('#form_extract_code').submit(function(event) {
		$("input[type=hidden]").remove();
	    $.each($("#body_selected_sample_table tr > td:first-child"), function(i, v){
	    	code = $(this).text();

	        var input = $("<input>").attr({"type":"hidden","name":"list_selected_sample[]"}).val(code);
	        $('#form_extract_code').append(input);
	    });
	    $('#add_field_extract_code > option:selected').each(function() {
	    	text = $(this).text();
	    	var input = $("<input>").attr({"type":"hidden","name":"text_selected_field[]"}).val(text);
	        $('#form_extract_code').append(input);
	    });

	    return true;
	});

    function add_row_table_selected(tab_val){
    	if($("#body_selected_sample_table #"+tab_val[0]+"").length == 0) {
		  	content_table = "<tr id="+tab_val[0]+">";
		  	$.each(tab_val, function(index, value) {
		  		content_table += "<td>"+value+"</td>";
		  	});
		  	content_table += "<td><div> <span title='Retirer' class='close span_remove_sample' data-code_sample='"+tab_val[0]+"'><span>×</span></span></div></td>" ;
		  	content_table += "</tr>" ;

			$('#body_selected_sample_table').append(content_table);
		}
	}



});
