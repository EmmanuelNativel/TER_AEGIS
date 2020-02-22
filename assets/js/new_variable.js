$(document).ready(function() {	
	$("#div_trait_form").hide();
	$("#div_method_form").hide();
	$("#div_scale_form").hide();


	function goToByScroll(id){
      // Remove "link" from the ID
	    $('html,body').animate({
	        scrollTop: $("#"+id).offset().top},
	        'fast');
	}


/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- TRAIT ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
	$("#btn_new_trait").on('click', function(e){
		$("#div_trait_form").slideDown();
	});
	$("#btn_cancel_new_trait").on('click', function(e){
		goToByScroll('div_trait');
		$("#div_trait_form").slideUp();
	});
	$("#btn_no_trait").on('click', function(e){
		$("#trait_code").selectpicker('deselectAll');
	});
	/* ------------------------------------- AJAX new Trait ------------------------------------------*/
	$("#btn_create_new_trait").on('click', function(e){
		
		trait_code = $("#add_trait_code").val();
		trait_name = $("#trait_name").val();
		trait_description = $("#trait_description").val();
		entity_code = $("#entity_code").val();
		target_name = $("#target_name").val();
		trait_author = $("#trait_author").val();


		if (trait_code != '' && trait_author != '') {
		    url = window.location.origin +"/daphne.cirad.fr/index.php/traits/create"; //local
		    //url = window.location.origin +"/index.php/traits/create";
		    $.ajax({
		      type: "POST",
		      url: url, 
		      data: {
		      	trait_code : trait_code,
		      	trait_name : trait_name,
				trait_description : trait_description,
				entity_code : entity_code,
				target_name : target_name,
				trait_author : trait_author
		      },
		      dataType: "json",
		      cache:false,
		      success: function(data){
		      	if (data == -1) {
		      		$("#error_add_trait").text("Le code trait existe déjà");
			        goToByScroll('div_trait');	
		      		
		      	}else{
		      		$("#add_trait_code").val('');
					$("#trait_name").val('');
					$("#trait_description").val('');
					$("#entity_code").val('');
					$("#target_name").val('');
					$("#trait_author").val('');
		      		$("#error_add_trait").text("");	
					
					$("#div_trait_form").hide();
			        goToByScroll('div_trait');	
		      	}
		        
		      },
		      error: function (data, status, err) {
		        console.log('Something went wrong', status, err);
		        console.log(data);
		        alert("Erreur lors de la création d'un trait");
		      }
		    });
		}else{
			$("#error_add_trait").text("Le code ainsi que l'auteur sont obligatoires");
			goToByScroll('div_trait');	

		}
	});
/* ------------------------------------- AJAX new Trait ------------------------------------------*/



/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- ENTITY ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
	$("#btn_add_entity").on('click', function(e){
		$('#addEntityModal').modal('show');
	});
/* ------------------------------------- AJAX Ajouter Entity ------------------------------------------*/
	$("#btn_confirm_add_entity").on('click', function(e){
		entity_code = $("#add_entity_code").val();
		entity_name = $("#add_entity_name").val();
		entity_definition = $("#add_entity_definition").val();
		if (entity_code != '' && entity_name != '' && entity_definition != '') {
		    url = window.location.origin +"/daphne.cirad.fr/index.php/entity/create"; //local
		    //url = window.location.origin +"/index.php/entity/create";
		    $.ajax({
		      type: "POST",
		      url: url, 
		      data: {
		      	entity_code : entity_code,
				entity_name : entity_name,
				entity_definition : entity_definition	        
		      },
		      dataType: "json",
		      cache:false,
		      success: function(data){
		      	if (data == -1) {
		      		$("#error_add_entity").text("Le code entité existe déjà");	
		      	}else{
		      		$("#add_entity_code").val("");
					$("#add_entity_name").val("");
					$("#add_entity_definition").val("");
					$("#error_add_entity").text("");
					$('#addEntityModal').modal('hide');
		      	}
		        
		      },
		      error: function (data, status, err) {
		        console.log('Something went wrong', status, err);
		        console.log(data);
		        alert("Erreur lors de la création d'une entité");
		      }
		    });
		}else{
			$("#error_add_entity").text("Tous les champs sont obligatoires");
		}
	});


/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- TARGET ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
	$("#btn_add_target").on('click', function(e){
		$('#addTargetModal').modal('show');
	});
/* ------------------------------------- AJAX new Target ------------------------------------------*/
	$("#btn_confirm_add_target").on('click', function(e){
		target_name = $("#add_target_name").val();
		if (target_name != '') {
		    url = window.location.origin +"/daphne.cirad.fr/index.php/targets/create"; //local
		    //url = window.location.origin +"/index.php/targets/create";
		    $.ajax({
		      type: "POST",
		      url: url, 
		      data: {
				target_name : target_name	        
		      },
		      dataType: "json",
		      cache:false,
		      success: function(data){
		      	if (data == -1) {
		      		$("#error_add_target").text("Le nom cible existe déjà");	
		      	}else{
					$("#add_target_name").val("");
					$("#error_add_target").text("");
					$('#addTargetModal').modal('hide');
		      	}
		        
		      },
		      error: function (data, status, err) {
		        console.log('Something went wrong', status, err);
		        console.log(data);
		        alert("Erreur lors de la création d'une cible");
		      }
		    });
		}else{
			$("#error_add_target").text("Le nom est obligatoire");
		}
	});


/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- METHOD ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
	$("#btn_new_method").on('click', function(e){
		$("#div_method_form").slideDown();
	});
	$("#btn_cancel_new_method").on('click', function(e){
		$("#div_method_form").slideUp();
		goToByScroll('div_method');
	});
	$("#btn_no_method").on('click', function(e){
		$("#method_code").selectpicker('deselectAll');
	});
/* ------------------------------------- AJAX new Method ------------------------------------------*/
	$("#btn_create_new_method").on('click', function(e){
		
		method_code = $("#add_method_code").val();
		method_name = $("#add_method_name").val();
		method_class = $("#add_method_class").val();
		method_subclass = $("#add_method_subclass").val();
		method_description = $("#add_method_description").val();
		method_formula = $("#add_method_formula").val();
		method_reference = $("#add_method_reference").val();
		method_type = $("#add_method_type").val();
		method_content_type = $("#add_method_content_type").val();
		method_author = $("#add_method_author").val();



		if (method_code != '' && method_author != '') {
		    url = window.location.origin +"/daphne.cirad.fr/index.php/methods/create"; //local
		    //url = window.location.origin +"/index.php/methods/create";
		    $.ajax({
		      type: "POST",
		      url: url, 
		      data: {
		      	method_code : method_code,
				method_name : method_name,
				method_class : method_class,
				method_subclass : method_subclass,
				method_description : method_description,
				method_formula : method_formula,
				method_reference : method_reference,
				method_type : method_type,
				method_content_type : method_content_type,
				method_author : method_author
		      },
		      dataType: "json",
		      cache:false,
		      success: function(data){		      	
		      	if (data == -1) {
		      		$("#error_add_method").text("Le code méthode existe déjà");
			        goToByScroll('div_method');	

		      	}else{
					$("#add_method_code").val("");
					$("#add_method_name").val("");
					$("#add_method_class").val("");
					$("#add_method_subclass").val("");
					$("#add_method_description").val("");
					$("#add_method_formula").val("");
					$("#add_method_reference").val("");
					$("#add_method_type").val("");
					$("#add_method_content_type").val("");
					$("#add_method_author").val("");
		      		$("#error_add_method").text("");	
					
					$("#div_method_form").hide();
			        goToByScroll('div_method');	
		      	}
		        
		      },
		      error: function (data, status, err) {
		        console.log('Something went wrong', status, err);
		        console.log(data);
		        alert("Erreur lors de la création d'une méthode");
		      }
		    });
		}else{
			$("#error_add_method").text("Le code ainsi que l'auteur sont obligatoires");
			goToByScroll('div_method');	

		}
	});

	$("#btn_no_scale").on('click', function(e){
		$("#scale_code").selectpicker('deselectAll');
	});


/* ------------------------------------- New Method Class ------------------------------------------*/
	$("#btn_add_method_class").on('click', function(e){
		$('#addMethodClassModal').modal('show');
	});
	$("#btn_confirm_add_method_class").on('click', function(e){		
		text = $("#add_method_class_name").val();
		if (text != null && text != '') {
			$("#add_method_class").append("<option value='"+text+"'>"+text+"</option>");
			$("#add_method_class").selectpicker('refresh');
			$("#error_add_method_class").text("");
			$('#addMethodClassModal').modal('hide');
		}else{
			$("#error_add_method_class").text("Le nom de la classe est obligatoire");
		}
	});

/* ------------------------------------- New Method Subclass ------------------------------------------*/
	$("#btn_add_method_subclass").on('click', function(e){
		$('#addMethodSubclassModal').modal('show');
	});
	$("#btn_confirm_add_method_subclass").on('click', function(e){
		text = $("#add_method_subclass_name").val();
		if (text != null && text != '') {
			$("#add_method_subclass").append("<option value='"+text+"'>"+text+"</option>");
			$("#add_method_subclass").selectpicker('refresh');
			$("#error_add_method_subclass").text("");
			$('#addMethodSubclassModal').modal('hide');
		}else{
			$("#error_add_method_subclass").text("Le nom de la sous-classe est obligatoire");
		}
	});


/* ----------------------------------------------------------------------------------------------------*/
/* ------------------------------------- SCALE ------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------*/
	$("#btn_new_scale").on('click', function(e){
		$("#div_scale_form").slideDown();
	});
	$("#btn_cancel_new_scale").on('click', function(e){
		$("#div_scale_form").slideUp();
		goToByScroll('div_scale');
	});
/* ------------------------------------- AJAX new Scale ------------------------------------------*/
	$("#btn_create_new_scale").on('click', function(e){
		scale_code = $("#add_scale_code").val();
		scale_name = $("#add_scale_name").val();
		scale_type = $("#add_scale_type").val();
		scale_level = $("#add_scale_level").val();
		if (scale_code != '' && scale_name != '' && scale_type != '' && scale_level != '') {
		    url = window.location.origin +"/daphne.cirad.fr/index.php/scales/create"; //local
		    //url = window.location.origin +"/index.php/scales/create";
		    $.ajax({
		      type: "POST",
		      url: url, 
		      data: {
		      	scale_code : scale_code,
				scale_name : scale_name,
				scale_type : scale_type,
				scale_level : scale_level		        
		      },
		      dataType: "json",
		      cache:false,
		      success: function(data){
		      	if (data == -1) {
		      		$("#error_add_scale").text("Le code unité existe déjà");	
		      	}else{
		      		$("#add_scale_code").val("");
					$("#add_scale_name").val("");
					$("#add_scale_type").val("");
					$("#add_scale_level").val("");
					
			        goToByScroll('div_scale');	
		      	}
		        
		      },
		      error: function (data, status, err) {
		        console.log('Something went wrong', status, err);
		        console.log(data);
		        alert("Erreur lors de la création d'une unité");
		      }
		    });
		}else{
			$("#error_add_scale").text("Tous les champs sont obligatoires");
		}
	});

	$("#btn_no_scale").on('click', function(e){
		$("#scale_code").selectpicker('deselectAll');
	});
});