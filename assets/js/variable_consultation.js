/*il existre 2 tableau json encode passé de la vue (localisation de la vue : variable/consultation.php) vers le javascript

	varesult : tableau contenant les informations de toutes les variables de la base de données extraite de la vue 'varesult'
	class_subclass_domain : tableau contenant les différentes class, subclass et domain des variables de la base de données.
		Ce tableau permet de facilité la mise à jour des liste permettant de filtrer les variables

	site_url : chaine de caractère contenante l'url du site. Utilisé pour les href des variables
*/
$(document).ready(function() {
	var nb_per_page = 5;							//nombre de variable a afficher par page
	var current_page = 1;							//page consultée par l'utilisateur
	var nb_var = varesult.length;					//nombre de variable total
	var nb_page_max = Math.floor(nb_var/5)+1;		//nombre de page maximum
	var nb_var_last_page = nb_var%5;				//nombre de variable à afficher sur la dernière page
	var nb_current_plage = 1;						//plage actuelle à afficher
	var filtered_varesult = varesult;				//initalisation du tableau filtered_varesult
	var list_filtered_selection = 	{"class" : "",
									 "subclass" : "",
									 "domain": ""};	//initalisation des champs filtrés
	/*var LIEN_HREF = window.location.origin+"/daphne.cirad.fr/index.php/variables/consultation";*/
	//var LIEN_HREF = window.location.origin+"/daphne.cirad.fr/index.php/variables/consultation"; //Local
	var LIEN_HREF = SiteURL +"/variables/consultation";
	/*var LIEN_HREF = window.location.origin+"/index.php/variables/consultation";*/	//Serveur
	console.log(LIEN_HREF);


	$(".indicateur_page").text("Page 1 sur "+nb_page_max);
	$("#div_panel_body_class").collapse('show');
	$("#div_panel_body_subclass").collapse('show');
	$("#div_panel_body_domain").collapse('show');
	$("#res_recherche").hide();

	function toggleIcon(e) {
	    $(e.target)
	        .prev()
	        .find(".glyphicon")
	        .toggleClass('glyphicon-plus glyphicon-minus');
	}
	$('.div_collapse_body').on('hidden.bs.collapse', toggleIcon);
	$('.div_collapse_body').on('shown.bs.collapse', toggleIcon);

	$("#btn_search").on('click', function(e){

		value = $("#text_search").val().toLowerCase();
		if (value != '') {
			if ($("#keep_filter_var").prop('checked')) {
				filtered_var_with_all_filter_selected();
			}else{
				$(".ul_filter_var").find(".li_active_filter").removeClass("li_active_filter");
				restore_varesult();
				$('.li_filter_subclass').removeClass('disabled_li_filter');	//remove disable sur sublcass
				$('.li_filter_domain').removeClass('disabled_li_filter');	//remove disable sur domain
				$(".ul_filter_var").find(".li_active_filter").removeClass("li_active_filter"); //remove active sur tous les filtres
			}


			filtered_varesult = filtered_varesult.filter(function (variable){ // parcours des variables filtrés ou non
				is_inside = false;
				$.each(variable, function(index, val){ //parcours des différentes valeurs dans les variables
					if (val != null && val != '') {
						if (val.toLowerCase().indexOf(value) >=0 ) {
							is_inside = true;
							return false; //break the each loop
						}
					}

				});
				return is_inside;
			});
		}
		/*$("#text_recherche").text("Résultat de la recherche");*/
		$("#res_recherche").show();

		draw_filtered_data();
	});
	$("#btn_cancel_search").on('click', function(e){
		//reset champ recherche
		$("#text_search").val('');
		$("#res_recherche").hide();

		filtered_var_with_all_filter_selected();
		draw_filtered_data();
	});

	$(".btn_pagination").on('click', function(e){
		val_click = $(this).attr('data-value');

		if(val_click=="1" || val_click=="deb"){//page début
			if(current_page != 1){
				current_page = parseInt(1);

				//afficher page current page
				display_page(current_page);
				//actualiser la pagination
				set_pagination(current_page);
			}
		}else
		if(val_click=="fin"){//page fin
			if(current_page != nb_page_max){
				current_page = parseInt(nb_page_max);

				display_page(current_page);
				set_pagination(current_page);
			}
		}else
		if(val_click=="-5"){//page -5
			if ( current_page != 1) {
				current_page = parseInt(current_page) -5;
				if (current_page <1) {current_page = 1;}

				display_page(current_page);
				set_pagination(current_page);
			}
		}else
		if(val_click=="+5"){//page +5
			if(current_page != nb_page_max){
				current_page = parseInt(current_page) +5;
				if (current_page > nb_page_max) {current_page = nb_page_max;}

				display_page(current_page);
				set_pagination(current_page);
			}
		}else
		if(val_click=="-1"){//page -1
			if(current_page != 1){
				current_page = parseInt(current_page) -1;
				if (current_page <1) {current_page = parseInt(1);}

				display_page(current_page);
				set_pagination(current_page);
			}
		}else
		if(val_click=="+1"){//page +1
			if(current_page != nb_page_max){
				current_page = parseInt(current_page) +1;
				if (current_page > nb_page_max) {current_page = nb_page_max;}

				display_page(current_page);
				set_pagination(current_page);
			}
		}else
		if (current_page != val_click) {
			current_page = parseInt(val_click);

			display_page(current_page);
			set_pagination(current_page);
		}
	});

	$(".li_filter_class").on('click', function(e){
		value = $(this).text();
		$("#res_recherche").hide();
		$(".ul_filter_var").find(".li_active_filter").removeClass("li_active_filter"); //remove active sur tous les filtres

		if (value == list_filtered_selection["class"]) {	//si click sur la class déjà active => enlève le filtre de la class + subclass + domain
			$(this).removeClass("li_active_filter");
			list_filtered_selection["class"] = "";
			$('.li_filter_subclass').removeClass('disabled_li_filter');	//remove disable sur sublcass
			$('.li_filter_domain').removeClass('disabled_li_filter');	//remove disable sur domain

		}else{	//si click sur nouveau filtre de class
			$(this).parent().find(".li_active_filter").removeClass("li_active_filter");
			$(this).addClass('li_active_filter');
			list_filtered_selection["class"] = value;

			$('.li_filter_subclass').addClass('disabled_li_filter');
			$('.li_filter_domain').addClass('disabled_li_filter');
			$.each(class_subclass_domain, function(index, val){ // mise a jour des subclass et domain disponibles en fonction de la class choisie
				if (value == val['class']) {
					$('.li_filter_subclass:contains("'+val['subclass']+'")').removeClass('disabled_li_filter');
					$('.li_filter_domain:contains("'+val['domain']+'")').removeClass('disabled_li_filter');
				}
			});
		}

		//reset les filtres sur subclass et domain
		list_filtered_selection["subclass"] = "";
		list_filtered_selection["domain"] = "";

		filtered_var_with_all_filter_selected();
		draw_filtered_data();
		//reset du champ de recherche
		$("#text_search").val('');

	});

	$(".li_filter_subclass").on('click', function(e){
		if ($(this).hasClass("disabled_li_filter") == false){	//si l'élément est cliquable
			value = $(this).text();
			$("#res_recherche").hide();
			$("#li_filter_domain").parent().find(".li_active_filter").removeClass("li_active_filter");

			// si click sur la subclass déjà active => enlève le filtre du domain
			// actualisation des domain disponnible en fonction de la class et subclass choisie
			if (value == list_filtered_selection["subclass"]) {
				list_filtered_selection["subclass"] = "";
				$(this).removeClass("li_active_filter");
				if (list_filtered_selection["class"] != "") { //class déjà séléctionnée
					$.each(class_subclass_domain, function(index, val){ // mise a jour des subclass et domain disponibles en fonction de la class choisie
						if (list_filtered_selection["class"] == val['class']) {
							$('.li_filter_domain:contains("'+val['domain']+'")').removeClass('disabled_li_filter');
						}
					});
				}else{
					$('.li_filter_domain').removeClass('disabled_li_filter');
				}


			}else{	// si click sur nouvelle subclass
				//change l'élément actif
				$(this).parent().find(".li_active_filter").removeClass("li_active_filter");
				$(this).addClass('li_active_filter');

				list_filtered_selection["subclass"] = value;
				$('.li_filter_domain').addClass('disabled_li_filter');

				if (list_filtered_selection["class"] != "") { //class déjà séléctionnée
					$.each(class_subclass_domain, function(index, val){ // mise a jour du domain disponibles en fonction de la class et subclass choisies
						if (list_filtered_selection["class"] == val['class'] && value == val['subclass']) {
							$('.li_filter_domain:contains("'+val['domain']+'")').removeClass('disabled_li_filter');
						}
					});
				}else{
					$.each(class_subclass_domain, function(index, val){ // mise a jour du domain disponibles en fonction de la class et subclass choisies
						if (value == val['subclass']) {
							$('.li_filter_domain:contains("'+val['domain']+'")').removeClass('disabled_li_filter');
						}
					});

				}
			}
			list_filtered_selection["domain"] = "";

			filtered_var_with_all_filter_selected();
			draw_filtered_data();
			//reset du champ de recherche
			$("#text_search").val('');
		}

	});

	$(".li_filter_domain").on('click', function(e){
		if ($(this).hasClass("disabled_li_filter") == false){
			value = $(this).text();
			$("#res_recherche").hide();
			if (value == list_filtered_selection["domain"]) {
				$(this).removeClass("li_active_filter");
				list_filtered_selection["domain"] = "";
			}else{
				$(this).parent().find(".li_active_filter").removeClass("li_active_filter");
				$(this).addClass('li_active_filter');
				list_filtered_selection["domain"] = value;
			}
			filtered_var_with_all_filter_selected();
			draw_filtered_data();
			//reset du champ de recherche
			$("#text_search").val('');
		}
	});

	function display_page(page){
		if (nb_var > 0) {
			nb_deb = (page - 1) *5;
			nb_fin = nb_deb + nb_per_page;

			//nb_var_last_page == 0 on a pas besoin de limiter le nombre de variable à afficher car la dernière page est obligatoirement complète
			if(nb_var_last_page != 0)
			{
				if (nb_fin > (nb_page_max-1)*5) {nb_fin = nb_deb + nb_var_last_page;}
			}
			s ="";

			//création de la chaine de caractère a afficher dans le dom pour les variables
			for (var i = nb_deb; i < nb_fin; i++) {
				variable = filtered_varesult[i];
				without_slash = variable['variable_code'].split("/");
				encode_variable_code = '';
				$.each(without_slash, function( index, value ){
					encode_variable_code = encode_variable_code +"/"+encodeURIComponent(value);
				});

				s += "<div class='row'>"+
						"<div class='col-xs-12'>"+
							"<h3><a class='link_var_href' target='_blank' href='"+LIEN_HREF+''+encode_variable_code+"'>"+variable['variable_code']+"</a>"+
								"<button class='btn btn-warning btn_add_cart_var' data-var_name='"+variable['variable_code']+"'><span class='glyphicon glyphicon-shopping-cart' aria-hidden='true'></span></button>"+
							"</h3>"+
						"</div>"+
						"<div class='col-xs-12 col-md-6'>"+
							"<dl class='dl-horizontal'>"+
								"<dt>Classe</dt>"+
								"<dd>"+ variable['class']+"</dd>"+
								"<dt>Sous classe</dt>"+
								"<dd>"+ variable['subclass']+"</dd>"+
								"<dt>Domaine</dt>"+
								"<dd>"+ variable['domain']+"</dd>"+
								"<dt>Nom trait</dt>"+
								"<dd>"+ variable['trait_name']+"</dd>"+
								"<dt>Entité trait</dt>"+
								"<dd>"+ variable['trait_entity']+"</dd>"+
								"<dt>Trait cible</dt>"+
								"<dd>"+ variable['trait_target']+"</dd>"+
							"</dl>"+
						"</div>"+
						"<div class='col-xs-12 col-md-6'>"+
							"<dl class='dl-horizontal'>"+
								"<dt>Classe méthode</dt>"+
								"<dd>"+ variable['method_class']+"</dd>"+
								"<dt>Nom méthode</dt>"+
								"<dd>"+ variable['method_name']+"</dd>"+
								"<dt>Formule</dt>"+
								"<dd>"+ variable['method_formula']+"</dd>"+
								"<dt>Unité</dt>"+
								"<dd>"+ variable['scale_name']+"</dd>"+
								"<dt>Type d'unité</dt>"+
								"<dd>"+ variable['scale_type']+"</dd>"+
								"<dt>Auteur</dt>"+
								"<dd>"+ variable['author']+"</dd>"+
							"</dl>"+
						"</div>"+
					"</div>"+
				"</div>"+
				"<hr>";

			}

			$("#div_var").empty();
			$("#div_var").append(s);
		}else{
			$("#div_var").empty();
		}
	}

	function set_pagination(current_page){
		//actualise la liste de pagination
		cpt = current_page-1;
		new_nb_plage = Math.floor((cpt)/5)+1;

		if (new_nb_plage != nb_current_plage) {
			$(".btn_pagination_page").parent().show();
			$("#page_1").attr('data-value', (new_nb_plage*5) - 4);
			$("#page_1").text((new_nb_plage*5) - 4);
			$("#page_2").attr('data-value', (new_nb_plage*5) - 3);
			$("#page_2").text((new_nb_plage*5) - 3);
			$("#page_3").attr('data-value', (new_nb_plage*5) - 2);
			$("#page_3").text((new_nb_plage*5) - 2);
			$("#page_4").attr('data-value', (new_nb_plage*5) - 1);
			$("#page_4").text((new_nb_plage*5) - 1);
			$("#page_5").attr('data-value', (new_nb_plage*5));
			$("#page_5").text((new_nb_plage*5));

			//on enlève l'excédent des boutton de pagination en fonction de la plage où l'on se situe
			if (new_nb_plage > nb_page_max/5) {
				$(".btn_pagination_page").each(function() {
				    if ($(this).attr('data-value') > nb_page_max) {
				    	$(this).parent().hide();
				    }
				});
			}
			nb_current_plage = new_nb_plage ;
		}

		//set active
		nb_active = current_page % 5;
		if (nb_active == 0) { nb_active = 5;}
		$("li.active").removeClass('active');
		$("#li_page_"+nb_active+"").addClass('active');

		//actualise page ... sur ...
		if (nb_var == 0) {
			$(".indicateur_page").text("Aucun résultat");
		}else{
			$(".indicateur_page").text("Page "+current_page+" sur "+nb_page_max);
		}
	}


	function filtered_var_with_all_filter_selected(){
		filtered_varesult = varesult;
		if (list_filtered_selection["class"]!= "") {
			set_filter_class_result(list_filtered_selection["class"]);
		}
		if (list_filtered_selection["subclass"]!= "") {
			set_filter_subclass_result(list_filtered_selection["subclass"]);
		}
		if (list_filtered_selection["domain"]!= "") {
			set_filter_domain_result(list_filtered_selection["domain"]);
		}
	}

	function set_filter_class_result(value){
		filtered_varesult = filtered_varesult.filter(function (n){
			return n.class===value;
		});
	}
	function set_filter_subclass_result(value){
		filtered_varesult = filtered_varesult.filter(function (n){
			return n.subclass===value;
		});
	}
	function set_filter_domain_result(value){
		filtered_varesult = filtered_varesult.filter(function (n){
			return n.domain===value;
		});
	}

	function draw_filtered_data(){
		//set current page 1
		current_page = parseInt(1);
		//set max page
		nb_var = filtered_varesult.length;
		nb_page_max = Math.floor(nb_var/5)+1;
		nb_var_last_page = nb_var%5;
		if (nb_var_last_page == 0) {nb_page_max--;}
		nb_current_plage = 0;	//nb_current_plage = 0 pour re-dessiner la pagination si jamais il y a moins de 5 page

		//draw les 5 premier
		display_page(current_page);
		set_pagination(current_page);

	}

	function restore_varesult(){
		current_page = 1;
		nb_var = varesult.length;
		nb_page_max = Math.floor(nb_var/5)+1;
		nb_var_last_page = nb_var%5;
		nb_current_plage = 1;
		filtered_varesult = varesult;
		list_filtered_selection = 	{"class" : "",
										 "subclass" : "",
										 "domain": ""};
	}

/* ------------------------------------- AJAX var in session ------------------------------------------*/
	$(document).on("click", ".btn_add_cart_var", function(e){
		var_name = $(this).attr('data-var_name');
	    parent = $(this).parent();

	    //url = window.location.origin +"/daphne.cirad.fr/index.php/variables/add_var_in_session"; //local
			url = SiteURL + "/variables/add_var_in_session"
			//url = window.location.origin +"/index.php/variables/add_var_in_session";
	    $.ajax({
	      type: "POST",
	      url: url,
	      data: {
	      	var_name : var_name,
	      },
	      dataType: "text",
	      cache:false,
	      success: function(data){
	      	if (data == -1) {
	      		message = "Problème lors de l'ajout de la variable";
	      		display_msg_cart("danger", message, parent);

	      	}else if (data == 1) {
	      		message = "La variable a bien été ajoutée au panier";
	      		display_msg_cart("success", message, parent);
	      		if ($("#notification_nb_var").length == 0) {
	      			span = "<span id='notification_nb_var' class='badge'>1</span>";
	      			$("<span id='notification_nb_var' class='badge'>1</span>").insertAfter($('#shopping_cart_nav'));
	      		}else{
	      			nb = parseInt($("#notification_nb_var").text());
	      			$("#notification_nb_var").text(nb+1);
	      		}

	      	}else if (data == 2) {
	      		message = "La variable est déjà dans le panier";
	      		display_msg_cart("success", message, parent);
	      	}
	      },
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(data);
	        alert("Erreur lors de l'ajout de la variable dans le panier");
	      }
	    });
	});

	function display_msg_cart(type, message, location){
		div = "<div class='alert alert-"+type+"' role='alert' style='display: none;'>"+
  				message+
			  "</div>";
	    new_div = $(div).insertBefore(location);
	    new_div.slideDown().delay(5000).slideUp();
	}
});
