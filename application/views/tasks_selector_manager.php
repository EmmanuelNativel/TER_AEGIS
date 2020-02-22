//GESTIONNAIRE D'INTERFACE POUR LA SELECTION DES PROJETS/WP/TACHES

var $select_org = $('#select-organization').selectize({
	sortField: {
		field: 'text',
		direction: 'asc'
	}
});

$select_org[0].selectize.setValue(<?php echo '"'.set_value('organization').'"'; ?>);

var $select_project = $('#select-project').selectize({
	sortField: {
		field: 'text',
		direction: 'asc'
	}
});

$select_project[0].selectize.setValue(<?php echo '"'.set_value('project').'"'; ?>);

var $select_wp = $('#select-wp').selectize({
	sortField: {
		field: 'text',
		direction: 'asc'
	}
});

$select_wp[0].selectize.setValue(<?php echo '"'.set_value('wp').'"'; ?>);

var $select_task = $('#select-task').selectize({
	sortField: {
		field: 'text',
		direction: 'asc'
	}
});

$select_task[0].selectize.setValue(<?php echo '"'.set_value('task').'"'; ?>);

$select_task[0].selectize.disable();
$select_wp[0].selectize.disable();
$('#add_task_btn').prop('disabled', true);

var graph_selection_info = null;

$select_project[0].selectize.on('change', function() {
	var selected_project = $select_project[0].selectize.getValue();
	if(selected_project != '') {

		$select_wp[0].selectize.enable();
		$select_wp[0].selectize.clearOptions();
		call_wp_options(selected_project, $select_wp);
	}
	else {
		$select_task[0].selectize.setValue('');
		$select_wp[0].selectize.setValue('');

		$select_task[0].selectize.disable();
		$select_wp[0].selectize.disable();
	}
});

$select_wp[0].selectize.on('change', function() {
	var selected_wp = $select_wp[0].selectize.getValue();
	if(selected_wp != ''){
		$select_task[0].selectize.enable();
		$select_task[0].selectize.clearOptions();
		call_task_options(selected_wp, $select_task);
	}
	else {
		$select_task[0].selectize.setValue('');
		$select_task[0].selectize.disable();
	}
});

$select_task[0].selectize.on('change', function() {
	var selected_task = $select_task[0].selectize.getValue();
	if(selected_task != '') {
		$('#add_task_btn').prop('disabled', false);
		$('#add_task_btn').removeClass("disabled");

		graph.focus('task_'+ selected_task, {
			scale: 0.5,
			animation: true
		});
	}
	else {
		$('#add_task_btn').prop('disabled', true);
		$('#add_task_btn').addClass("disabled");
	}
});

function call_wp_options( project, select_wp) {
	$.ajax({
		url  : '<?php echo site_url('welcome/get_wp_options/'); ?>' + '/' + project,
		dataType: 'json',
		success: function(newOptions) {

			$.each(newOptions, function(i, val) {
				select_wp[0].selectize.addOption({
					text: i,
					value: val
				});
			});

			if(graph_selection_info != null) {
				$select_wp[0].selectize.setValue(graph_selection_info['wp'][0]['wp_code']);
			}
		}
	});
}

function call_task_options( wp, select_task) {
	$.ajax({
		url  : '<?php echo site_url('welcome/get_task_options/'); ?>' + '/' + wp,
		dataType: 'json',
		success: function(newOptions) {
			$.each(newOptions, function(i, val) {
				select_task[0].selectize.addOption({
					text: i,
					value: val
				});
			});

			if(graph_selection_info != null) {
				$select_task[0].selectize.setValue(graph_selection_info['task'][0]['task_code']);
				graph_selection_info = null;
			}
		}
	});
}

var cart = <?php if(isset($cart)) echo $cart; else echo '{}';?>;
refresh_cart_view();

//Ajoute la tache sélectionnée au panier
function add_to_cart() {
	var selected_task_code = $select_task[0].selectize.getValue();
	$.ajax({
		url  : '<?php echo site_url('welcome/get_task_info/'); ?>' + '/' + selected_task_code,
		dataType: 'json',
		success: function(task_info) {
			if( cart[selected_task_code] == undefined) {
				cart[selected_task_code] = { 'project': task_info['project'][0]['project_name'],
																	 'wp': task_info['wp'][0]['wp_name'],
																	 'task': task_info['task'][0]['task_description'],
																	 'access': 'r'};
				refresh_cart_view();

				//Re-init selection
				$select_project[0].selectize.setValue('');
				$select_task[0].selectize.setValue('');
				$select_wp[0].selectize.setValue('');

				$select_task[0].selectize.disable();
				$select_wp[0].selectize.disable();
			}
		}
	});
}

// Supprime une tache du panier
function remove_from_cart( key ) {
	delete cart[key];
	refresh_cart_view();
}

//Change les droits d'accès d'une tâche
function change_task_access(key) {
	if(cart[key]['access'] != 'r')	cart[key]['access'] = 'r';
	else														cart[key]['access'] = 'w';
}

//Rafraichi l'affichage du panier
function refresh_cart_view() {
	if(Object.keys(cart).length == 0){
		$('#cart_view').html('');
	}
	else {
		var html_cart = '';
		var status = '';
		for (var key in cart) {
			if(cart[key]['access'] == 'w') status = 'checked';
			html_cart += '<tr><td>' + cart[key]['project'] + '</td><td>' + cart[key]['wp'] + '</td><td>'+ cart[key]['task'] + '</td><td><input id="task_'+ key +'_access" class="access" type="checkbox" '+ status +' onclick="change_task_access('+ key +');" /><label for="task_'+ key +'_access"><span class="ui"></span></label></td><td><button type="button" onclick="remove_from_cart('+ key +')" class="remove glyphicon glyphicon-remove"></button></td></tr>';
			status = '';
		}
		$('#cart_view').html('<tr><th>Projet</th><th>WP</th><th>Tâche</th><th>Accés</th><th>Annuler</th></tr>' + html_cart);
	}
}

//Ajoute les données du panier comme données du formulaire à la soumission.
$('#main-form').submit(function(){ //listen for submit event

	var params = [
		{
			name: "cart_data",
			value: JSON.stringify(cart)
		}
	];

	$.each(params, function(i,param){
		$('<input />').attr('type', 'hidden')
		.attr('name', param.name)
		.attr('value', param.value)
		.appendTo('#main-form');
	});

	return true;
});
