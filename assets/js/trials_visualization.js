/*il existre 1 tableau json encode passé de la vue (localisation de la vue : visualization/accueil.php) vers le javascript

	trials : tableau contenant les informations de tous les essais de la base de données extraite de la vue 'trials_visualization'

*/
$(document).ready(function() {
	var nb_per_page = 5;							//nombre de variable a afficher par page
	var current_page = 1;							//page consultée par l'utilisateur
	var nb_trials = trials.length;					//nombre de variable total
	var nb_page_max = Math.ceil(nb_trials/nb_per_page);		//nombre de page maximum
	var nb_trials_last_page = nb_trials%5;				//nombre de variable à afficher sur la dernière page
	var nb_current_plage = 0;						//plage actuelle à afficher
	var filtered_trials = trials;				//initalisation du tableau filtered_trials
	var list_filtered_selection = {
				"project_code" : "",
				"year": ""
	};	//initialisation des champs filtrés (utilisé pour les champs "simples")
	var selectedFactors = []; // contient les valeurs pour le filtre facteur
														// tableau d'objets du type { factorName : "factor_name",  selectedLevels : [ level1, level2, ... , leveln ] }

	//Chargement des facteurs sélectionnables dans le filtre "Facteurs étudiés"
	loadAvailableFactors()

	$(".indicateur_page").text("Page 1 sur "+nb_page_max);
	$("#div_panel_body_project").collapse('show');
	$("#div_panel_body_factor").collapse('show');
	$("#div_panel_body_year").collapse('show');
	$("#res_recherche").hide();

	set_pagination(current_page)
	display_page(1);

	$('.div_collapse_body').on('hidden.bs.collapse', toggleIcon);
	$('.div_collapse_body').on('shown.bs.collapse', toggleIcon);

	/* ===========================================================================
	 																Map d'essais
	 ===========================================================================*/
	//Coordonnées réunion = [-21.13, 55.50]
	var mymap = L.map('trialsMap', {minZoom: 2}).setView([19, 15], 2);
	mymap.setMaxBounds( [[-90,-180], [90,180]] )

	L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
		 attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(mymap);

	// var trials_markers = new L.LayerGroup().addTo(mymap);
	var trials_markers = new L.markerClusterGroup({
			maxClusterRadius: 120,
			iconCreateFunction: function (cluster) {
				var childCount = cluster.getChildCount();
				 var c = ' marker-cluster-';
				 if (childCount < 10) {
				   c += 'small';
				 }
				 else if (childCount < 100) {
				   c += 'medium';
				 }
				 else {
				   c += 'large';
				 }


				return L.divIcon({
					html: '<div><span>' + childCount + '</span></div>',
					className: 'marker-cluster' + c,
					iconSize: L.point(40, 40)
				});
			}
		});

	trials_markers.addTo(mymap);

	//Initialement on affiche tous les essais sur la carte
	drawTrialsMarkers(trials);

	function drawTrialsMarkers(trials_array) {
		trials_markers.clearLayers();
		$.each(trials_array, function(index, trial){
			var marker = L.marker([trial['site_lat'], trial['site_long']])
			var markerHtml = [
				'<b>' + trial['trial_code'] + '</b>',
				'<br>' + trial["site_name"] + " - " + trial["country"] + '</br>',
				'<br>' + trial["trial_description"] + '</br>'
			].join('\n')
			marker.bindPopup(markerHtml)

			trials_markers.addLayer(marker);
		});
	}

	 /* ===========================================================================
																	 Barre de recherche
		===========================================================================*/

	$("#btn_search").on('click',async function(e){

		value = $("#text_search").val().toLowerCase();
		if (value != '') {
			if ($("#keep_filter_var").prop('checked')) {
				await draw_trials_with_selected_filters(false); //false pour ne pas dessiner maintenant
			}else{
				$(".ul_filter_var").find(".li_active_filter").removeClass("li_active_filter");
				restore_trials();
			}


			filtered_trials = filtered_trials.filter(function (trial){ // parcours des essais filtrés ou non
				is_inside = false;
				$.each(trial, function(index, val){ //parcours des différentes valeurs dans les variables
					if (val != null && val != '') {
						var val = val.toString();
						if (val.toLowerCase().indexOf(value) >=0 ) {
							is_inside = true;
							return false; //break the each loop
						}
					}

				});
				return is_inside;
			});
		}

		$("#res_recherche").show();
		draw_filtered_data();
	});


	$("#btn_cancel_search").on('click', function(e){
		//reset champ recherche
		$("#text_search").val('');
		$("#res_recherche").hide();

		draw_trials_with_selected_filters()
	});

	 /* ===========================================================================
																	 Pagination
		===========================================================================*/

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

	/* ===========================================================================
	 																Filtres
	 ===========================================================================*/

	// ------------ Filtre projets ------------

	$(".li_filter_project").on('click', function(e){
		value = $(this).text();
		$("#res_recherche").hide();

		if (value == list_filtered_selection["project_code"]) {	//si click sur la class déjà active => enlève le filtre de la class
			$(this).removeClass("li_active_filter");
			list_filtered_selection["project_code"] = "";

		}else{	//si click sur nouveau filtre de projet
			$(this).parent().find(".li_active_filter").removeClass("li_active_filter");
			$(this).addClass('li_active_filter');
			list_filtered_selection["project_code"] = value;

		}

		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');

	});

	// ------------ Filtre année ------------

	$(".li_filter_year").on('click', function(e){
		if ($(this).hasClass("disabled_li_filter") == false){
			value = $(this).text();
			$("#res_recherche").hide();
			if (value == list_filtered_selection["year"]) { //toggle active year
				$(this).removeClass("li_active_filter");
				list_filtered_selection["year"] = "";
			} else {
				$(this).parent().find(".li_active_filter").removeClass("li_active_filter");
				$(this).addClass('li_active_filter');
				list_filtered_selection["year"] = value;
			}

			draw_trials_with_selected_filters()
			//reset du champ de recherche
			$("#text_search").val('');
		}
	});


	// ------------ Filtre facteurs étudiés ------------

	/*
		Lors de la sélection d'un nouveau facteur
	*/
	$('#factorSelectpicker').on('change', function(e){
		var selectedFactorIndice = $(this).val();
		if (selectedFactorIndice) {
			var selectedFactorName = $(this).find('option:selected').text();
			appendNewFactor(selectedFactorName)
			loadFactorsFilteredTrialsCode()
		}
		//Réinitialisation du selectpicker
		$(this).val('default').selectpicker("refresh");

		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');
	});

	/*
		Lors du clic du bouton "ajout d'un facteur"
	*/
	/*
	$("#btnAddFactor").on('click', function(e){
		var selectedFactorIndice = $('#factorSelectpicker').val();
		if (selectedFactorIndice) {
			var selectedFactorName = $('#factorSelectpicker option:selected').text();
			appendNewFactor(selectedFactorName)
			loadFactorsFilteredTrialsCode()
		}
		//Réinitialisation du selectpicker
		$('#factorSelectpicker').val('default').selectpicker("refresh");

		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');
	});
	*/

	/*
		Lors du clic du bouton supprimer un facteur de la liste
	*/
	$("#selectedFactors").on('click', '.btnRemoveFactor',  function(e) {
		var factorNameToRemove = $(this).parent().find('.factorName').text();
		removeSelectedFactor(factorNameToRemove)
		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');
	});

	/*
		Lors de la sélection d'un nouveau level pour un facteur
	*/
	$("#selectedFactors").on('change', '.levelSelectpicker',  function(e) {
		console.log("bob");
		var factorName = $(this).attr('factorName');
		var selectedLevelIndice = $(this).val();
		if (selectedLevelIndice) { //Si un level est séléctionné
			var selectedLevelName = $(this).find("option:selected").text();
			appendNewLevel(selectedLevelName, factorName)
		}
		//Réinitialisation du selectpicker
		$(this).val('default').selectpicker("refresh");

		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');
	});
	/*
	$("#selectedFactors").on('click', '.btnAddLevel',  function(e) {
		var factorName = $(this).attr('factorName');
		var levelSelectpicker = $('.levelSelectpicker[factorName='+ factorName +']')

		var selectedLevelIndice = levelSelectpicker.val();
		if (selectedLevelIndice) { //Si un level est séléctionné
			var selectedLevelName = levelSelectpicker.find("option:selected").text();
			appendNewLevel(selectedLevelName, factorName)
		}
		//Réinitialisation du selectpicker
		levelSelectpicker.val('default').selectpicker("refresh");

		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');
	});
	*/

	/*
		Lors du clic sur le bouton supprimer un level d'un facteur
	*/
	$("#selectedFactors").on('click', '.btnRemoveLevel',  function(e) {
		var factorName = $(this).attr('factorName');
		var levelName = $(this).attr('levelName');
		removeSelectedLevel(levelName, factorName)
		draw_trials_with_selected_filters()
		//reset du champ de recherche
		$("#text_search").val('');
	});

	/*
		Ajoute un nouveau facteur dans le tableau global et l'affiche dans le filtre
	*/
	function appendNewFactor(factor_name) {
		//Quand un facteur est ajouté on l'ajoute à notre variable globale s'il n'existe pas déjà
		var factorExists = selectedFactors.some(factor => factor.factorName === factor_name);
		if (!factorExists) {
				selectedFactors.push({ factorName: factor_name , selectedLevels : [] }) //Ajout dans le tableau global
				var newfactorHTML =
				`
				<div id="${factor_name}">
					<li class="factorLi">
								<span class="factorName">${factor_name}</span>
								<span class="btnRemoveFactor glyphicon glyphicon-remove"></span>
					</li>
					<div class="levelsDiv">
						<ul class="levelUL">

								<div class="selectedLevels"></div>

								<li class="col-xs-12 levelLi factorSelectLevel">
									<select class="selectpicker levelSelectpicker" factorName="${factor_name}" data-live-search="true" data-width="100%" data-size="5" name="datasetType" data-title="Level...">
									</select>
								</li>

						</ul>
					</div>
				</div>
				`

				// $<span
				// 	class="btnAddLevel glyphicon glyphicon-plus pull-right"
				// 	factorName="${factor_name}" >
				// </span>

				$('#selectedFactors').append(newfactorHTML);
				//Décalage du selectPicker pour les facteurs et du bouton associé (pour éviter que ça soit trop collé avec la liste)
				$('.factorSelect').attr('style', 'margin-top: 20px !important');
				$('#btnAddFactor').attr('style', 'margin-top: 30px !important');

				//Chargement des levels disponibles dans le selectPicker venant d'être ajouté
				loadAvailableLevels(factor_name)

				$(".selectpicker").selectpicker(); //Initialise tous les selectPicker (sinon ils ne s'affichent pas)
		} else {
			showAlertModal("Le facteur <b>" + factor_name + "</b> a déjà été ajouté !")
		}
	}

	/*
		Supprime un facteur séléctionné dans le tableau global et l'affiche dans le filtre
	*/
	function removeSelectedFactor(factor_name) {
		//Suppression du facteur et de ses info de notre tableau global
		var indiceToDelete = selectedFactors.findIndex(factor => factor.factorName === factor_name)
		selectedFactors.splice(indiceToDelete, 1)
		$("#" + factor_name).remove() //suppression dans le DOM

		if (selectedFactors.length <= 0) {
			//On supprime les styles qu'on aurait appliqué précédemment
			$('.factorSelect').removeAttr("style")
			$('#btnAddFactor').removeAttr("style")
		}
	}

	/*
		Ajoute un nouveau level à un facteur dans le tableau global et l'affiche dans le filtre
	*/
	function appendNewLevel(level_name, factor_name) {
		//Quand un level est ajouté on l'ajoute à notre variable globale dans le bon facteur s'il n'existe pas déjà
		var factorIndex = selectedFactors.findIndex(factor => factor.factorName == factor_name);
		var levelExists = selectedFactors[factorIndex].selectedLevels.includes(level_name);
		if (!levelExists) {
				//Ajout et récupération de l'indice du level
				var level_indice = selectedFactors[factorIndex].selectedLevels.push(level_name) - 1;

				var newLevelHTML =
				`
				<div>
					<li class="col-xs-12 levelLi"> ${level_name}
						<span
							class="btnRemoveLevel glyphicon glyphicon-minus pull-right"
							factorName="${factor_name}"
							levelName=${level_name} >
						</span>
					</li>
				<div>
				`

				$('#' + factor_name + " .selectedLevels").append(newLevelHTML);
		} else {
			showAlertModal("Le level <b>" + level_name + "</b> a déjà été ajouté !")
		}
	}

	/*
		Supprime un factor_level séléctionné dans le tableau global et l'affiche dans le filtre
	*/
	function removeSelectedLevel(level_name, factor_name) {
		//Suppression du level de notre tableau global
		var factorIndice = selectedFactors.findIndex(factor => factor.factorName === factor_name)
		var levelIndiceToDelete = selectedFactors[factorIndice].selectedLevels.indexOf(level_name)
		selectedFactors[factorIndice].selectedLevels.splice(levelIndiceToDelete, 1)

		$("span[factorName=" + factor_name +"][levelName="+ level_name +"]").parent().remove()
	}

 /* ===========================================================================
																 Fonctions
	===========================================================================*/

	/*
		Fonction ajax permettant de récupérer les trial_code à afficher pour le
		filtre facteur étudiés.
	*/
	async function loadFactorsFilteredTrialsCode() {
		var trialsCode = null;
		await $.ajax({
			url: SiteURL + '/visualization/ajaxLoadFactorsFilteredTrialsCode',
			data: {factors: JSON.stringify(selectedFactors)},
			type: 'POST',
			dataType: 'json',
			success: function(response){
				//console.log("get page response !");
				//console.log(response);
				trialsCode = response.trials_code.map( element => element.trial_code);
			}
		});

		return trialsCode;
	}

	/*
		Fonction ajax permettant de charger la liste des facteurs qu'on peut séléctionner
	*/
	function loadAvailableFactors() {
		$.ajax({
			url: SiteURL + '/visualization/ajaxLoadFactors',
			type: 'get',
			dataType: 'json',
			success: function(response){
				//console.log("get page response !");
				//console.log(response);

				//On remplace les élements du selectPicker par ceux qu'on vient d'obtenir
				var generatedHtml = `${
					response.factors.map((element, element_indice) =>
						`<option value=${element_indice} >${element.factor}</option>`
						).join('\n')
					}`;

				$("#factorSelectpicker").html(generatedHtml);
				$('#factorSelectpicker').selectpicker('refresh');
			}
		});
	}

	/*
		Fonction ajax permettant de charger la liste des levels qu'on peut séléctionner
		pour un facteur donné.
	*/
	function loadAvailableLevels(factor_name) {
		$.ajax({
			url: SiteURL + '/visualization/ajaxLoadLevels/' + factor_name,
			type: 'get',
			dataType: 'json',
			success: function(response){
				//console.log("get page response !");
				//console.log(response);

				//On remplace les élements du selectPicker par ceux qu'on vient d'obtenir
				var generatedHtml = `${
					response.levels.map((element, element_indice) =>
						`<option data-subtext="${element.factor_level_description || ''}" >${element.factor_level}</option>`
						).join('\n')
					}`;

				var levelSelectpicker = $(".selectpicker[factorName="+ factor_name +"]")
				levelSelectpicker.html(generatedHtml);
				levelSelectpicker.selectpicker('refresh');
			}
		});
	}

	function showAlertModal(htmlMessage) {
		$('#myModal .modal-body').html(htmlMessage);
		$('#myModal').modal('show');
	}

	 function toggleIcon(e) {
			 $(e.target)
					 .prev()
					 .find(".glyphicon")
					 .toggleClass('glyphicon-plus glyphicon-minus');
	 }

	function display_page(page){
		if (nb_trials > 0) {
			nb_deb = (page - 1) *5;
			nb_fin = nb_deb + nb_per_page;

			//nb_trials_last_page == 0 on a pas besoin de limiter le nombre de variable à afficher car la dernière page est obligatoirement complète
			if(nb_trials_last_page != 0)
			{
				if (nb_fin > (nb_page_max-1)*5) {nb_fin = nb_deb + nb_trials_last_page;}
			}
			s ="";

			//création de la chaine de caractère a afficher dans le dom pour les variables
			for (var i = nb_deb; i < nb_fin; i++) {
				trial = filtered_trials[i];
				trial_code = trial['trial_code'];
				lien_trial_code = SiteURL + "trials/display/" + trial_code;
				lien_project_code = SiteURL + "projects/display/" + trial['project_code'];

				s += "<div class='row'>"+
						"<div class='col-xs-12'>"+
							"<h3><a class='link_var_href' target='_blank' href='"+ lien_trial_code + "'>"+ trial_code +"</a>"+"</h3>"+
						"</div>"+
						"<div class='col-xs-12 col-md-6'>"+
							"<dl class='dl-horizontal'>"+
							"<dt>Description</dt>" +
							"<dd>" + trial["trial_description"] + "</dd>" +
							"<dt> Date de début </dt>" +
							"<dd>" +trial["starting_date"]+ "</dd>" +
							"<dt> Date de fin </dt>" +
							"<dd>"+trial["ending_date"]+"</dd>" +
							"<dt>Commentaire</dt>" +
							"<dd> "+ trial["commentary"] + "</dd>" +
							"</dl>"+
						"</div>"+
						"<div class='col-xs-12 col-md-6'>"+
							"<dl class='dl-horizontal'>"+
								"<dt>Code projet</dt>" +
								"<dd>" + "<a class='link_var_href' target='_blank' href='"+ lien_project_code + "'>"+ trial['project_code'] + "</a>" + "</dd>" +
								"<dt>Lieu</dt>" +
								"<dd>" + trial["site_name"] + " - " + trial["country"] + "</dd>" +
								"<dt>Envt contrôlé</dt>" +
								"<dd>" + (trial["controlled_environment"] == 't' ? "oui" : "non") + "</dd>" +
								"<dt>Essai irrigué</dt>" +
								"<dd>" + (trial["irrigated"] == 't' ? "oui" : "non") + "</dd>" +
								"<dt>Essai fertilisé</dt>" +
								"<dd>" + (trial["fertilization"] == 't' ? "oui" : "non") + "</dd>" +
							"</dl>"+
						"</div>"+
					"</div>"+
				"</div>"+
				"<hr>";

			}

			$("#div_trials").empty();
			$("#div_trials").append(s);
		}else{
			$("#div_trials").empty();
		}
	}

	function set_pagination(current_page){

		// Affichage des boutons de pagination, indicateurs de page etc car peut être cachés.
		$(".pagination").show();
		$(".indicateur_page").show();
		$('#noResultMessage').hide();

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
		$('[id^="li_page_"].active').removeClass('active');
		$("#li_page_"+nb_active+"").addClass('active');

		//actualise page ... sur ...
		if (nb_trials == 0) {
			$(".indicateur_page").hide(); //cache les 2 indicateurs de page
			$(".pagination").hide(); //cache le bloc de bouton de la pagination
			$('#noResultMessage').show();
		}else{
			$(".indicateur_page").text("Page "+current_page+" sur "+nb_page_max);
		}
	}

	async function draw_trials_with_selected_filters(draw=true) {
		if(draw) $("#div_trials").empty();
		filtered_trials = trials;
		if (list_filtered_selection["project_code"]!= "") {
			await set_filter_project_result(list_filtered_selection["project_code"]);
		}
		if (list_filtered_selection["year"]!= "") {
			await set_filter_year_result(list_filtered_selection["year"]);
		}
		if (selectedFactors.length > 0) {
			await set_filter_factors_result();
		}
		if(draw) draw_filtered_data();
	}

	async function set_filter_project_result(selectedProjectCode){
		filtered_trials = filtered_trials.filter(function (trial){
			return trial["project_code"] === selectedProjectCode;
		});
	}
	async function set_filter_year_result(selectedYear){
		filtered_trials = filtered_trials.filter(function (trial){
			var trialYear = trial['starting_date'].substring(0,4); //4 premiers chars == année
			return trialYear === selectedYear;
		});
	}

	async function set_filter_factors_result(){
		//On trie en demandant au serveur de nous retourner la liste des trial_code à afficher
		// de cette façon on délègue le travail à PostgreSQL qui est plus rapide.
		var selected_trials_code = await loadFactorsFilteredTrialsCode()
		//On filtre le tableau global grâce à cette liste
		filtered_trials = filtered_trials.filter(function (trial){
			return selected_trials_code.includes(trial['trial_code']);
		});
	}

	function draw_filtered_data(){
		//set current page 1
		current_page = parseInt(1);
		//set max page
		nb_trials = filtered_trials.length;
		nb_page_max = Math.floor(nb_trials/5)+1;
		nb_trials_last_page = nb_trials%5;
		if (nb_trials_last_page == 0) {nb_page_max--;}
		nb_current_plage = 0;	//nb_current_plage = 0 pour re-dessiner la pagination si jamais il y a moins de 5 page

		//draw les 5 premier
		display_page(current_page);
		set_pagination(current_page);

		//On update la map également de l'onglet Géolocalisation
		drawTrialsMarkers(filtered_trials)

	}

	function restore_trials(){
		current_page = 1;
		nb_trials = trials.length;
		nb_page_max = Math.floor(nb_trials/5)+1;
		nb_trials_last_page = nb_trials%5;
		nb_current_plage = 1;
		filtered_trials = trials;
		list_filtered_selection = {
			"project_code" : "",
			"year": ""
		};
	}

});
