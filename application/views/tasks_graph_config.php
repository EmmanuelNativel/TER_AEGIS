//CONFIGURATION GRAPH-Projets/wp/taches

var nodes = [
	<?php
	$i=0;
	foreach ($list_project as $project) {
		echo '{id: "'.html_entity_decode($project['project_code']).'", label:"'.html_entity_decode($project['project_code']).'", title:"'.html_entity_decode($project['project_name']).'", group:"project"},';
		$i++;
	}

	foreach ($list_wp as $wp) {
		echo "{id:'wp_".html_entity_decode($wp['wp_code'])."', label:'".html_entity_decode($wp['wp_code'])."', title:'".html_entity_decode($wp['wp_description'])."', group:'wp'},";
		$i++;
	}

	foreach ($list_task as $task) {
		echo "{id:'task_".html_entity_decode($task['task_code'])."', label:'".html_entity_decode($task['task_code'])."', title:'".html_entity_decode($task['task_description'])."', group:'task'},";
		$i++;
	}
	unset($i);
	?>

];
var edges = [
	<?php
	foreach ($list_wp as $wp) {
		echo '{from: "'.$wp['project_code'].'", to: "wp_'.$wp['wp_code'].'"},';
	}

	foreach ($list_task as $task) {
		echo '{from: "wp_'.$task['wp_code'].'", to: "task_'.$task['task_code'].'"},';
	}
	?>
];

// create a network
var container = document.getElementById('mynetwork');
var data = {
	nodes: nodes,
	edges: edges
};
var options = {
	nodes: {
		shape: 'dot',
		size: 20,
		font: {
			size: 15,
			color: 'black'
		},
		borderWidth: 2,
	},
	edges: {
		width: 2,
		color: 'black'
	},
	groups: {
		wp: {
			color: '#9ddfb7',
			shape: 'square'
		},
		task: {
			color:'#d3e9c3',
			shape:'triangle'
		},

		project: {
			color:'#3d8f62'
		}
	},
	interaction: {
		navigationButtons: true,
		zoomView: false,
		hover: true
	}


};

var graph;

$('#see_graph_btn').click(function() {
	$('#see_graph_btn').hide();
	$('#graph_msg').html('Merci de patienter...');
	$('#loadingBar').show();

	var network = new vis.Network(container, data, options);

	graph = network;

	network.on("stabilizationProgress", function(params) {
		//var maxWidth = 496;
		//var minWidth = 20;
		var widthFactor = params.iterations/params.total;
		//var width = Math.max(minWidth,maxWidth * widthFactor);

		//document.getElementById('bar').style.width = width + 'px';
		document.getElementById('text').innerHTML = Math.round(widthFactor*100) + '%';
	});
	network.once("stabilizationIterationsDone", function() {
		document.getElementById('text').innerHTML = '100%';
		//document.getElementById('bar').style.width = '496px';
		//document.getElementById('loadingBar').style.opacity = 0;
		// really clean the dom element
		setTimeout(function () {document.getElementById('loadingBar').style.display = 'none';}, 500);
		$('#graph_msg').hide();
	});

	network.on("selectNode", function (params) {
		if(params.nodes[0].substring(0,5) == 'task_') {
			var task_code = params.nodes[0].substring(5);
			transfert_task_info(task_code);
		}
	});
});

function transfert_task_info( task_code ){
	$.ajax({
		url  : '<?php echo site_url('welcome/get_task_info/'); ?>' + '/' + task_code,
		dataType: 'json',
		success: function(task_info) {
			graph_selection_info = task_info;
			$select_project[0].selectize.setValue(task_info['project'][0]['project_code']);
		}
	});
}
