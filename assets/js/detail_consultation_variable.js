$(document).ready(function() {

	function toggleIcon(e) {
	    $(e.target)
	        .prev()
	        .find(".glyphicon")
	        .toggleClass('glyphicon-plus glyphicon-minus');
	}
	$('.div_collapse_body').on('hidden.bs.collapse', toggleIcon);
	$('.div_collapse_body').on('shown.bs.collapse', toggleIcon);

	$(".div_collapse_body").collapse('show');

	$('#div_var_onto .div_var_onto').not(":first-child").hide();

	$("#show_more_less_var_onto").on('click', function(e){
		if ($(this).text()=="Afficher plus") {
			$('#div_var_onto .div_var_onto').not(":first-child").show();
			$(this).text("Afficher moins");
		}else{
			$('#div_var_onto .div_var_onto').not(":first-child").hide();
			$(this).text("Afficher plus");
		}
	});

	$("#show_more_less_trait_onto").on('click', function(e){
		if ($(this).text()=="Afficher plus") {
			$('#div_trait_onto .div_trait_onto').not(":first-child").show();
			$(this).text("Afficher moins");
		}else{
			$('#div_trait_onto .div_trait_onto').not(":first-child").hide();
			$(this).text("Afficher plus");
		}
	});

	$("#show_more_less_entity_onto").on('click', function(e){
		if ($(this).text()=="Afficher plus") {
			$('#div_entity_onto .div_entity_onto').not(":first-child").show();
			$(this).text("Afficher moins");
		}else{
			$('#div_entity_onto .div_entity_onto').not(":first-child").hide();
			$(this).text("Afficher plus");
		}
	});

	$("#show_more_less_method_onto").on('click', function(e){
		if ($(this).text()=="Afficher plus") {
			$('#div_method_onto .div_method_onto').not(":first-child").show();
			$(this).text("Afficher moins");
		}else{
			$('#div_method_onto .div_method_onto').not(":first-child").hide();
			$(this).text("Afficher plus");
		}
	});

	$("#show_more_less_scale_onto").on('click', function(e){
		if ($(this).text()=="Afficher plus") {
			$('#div_scale_onto .div_scale_onto').not(":first-child").show();
			$(this).text("Afficher moins");
		}else{
			$('#div_scale_onto .div_scale_onto').not(":first-child").hide();
			$(this).text("Afficher plus");
		}
	});


	/*$( "#show_more_less_var_onto" ).toggle(function() {
		$('#div_var_onto .div_var_onto').not(":first-child").show();
	 	$(this).text("Afficher moins");
	}, function() {
	  $('#div_var_onto .div_var_onto').not(":first-child").hide();
	 	$(this).text("Afficher plus");
	});*/
});	