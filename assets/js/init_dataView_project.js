$(window).load(function() {
	
$(function() {
	
	if (localStorage.getItem("selectedTable") === "null"){
		$("#selectpickerTable").selectpicker({title: "Select A Table"});
	}else{
		$("#selectpickerTable").selectpicker({title: localStorage.getItem("selectedTable")});
		localStorage.setItem("oldSelectedProject", "null");
		localStorage.setItem("selectedProject[]", "null");
	}
	
	$("#selectpickerTable").selectpicker({
		countSelectedText: '{0} Table Selected'
	});
	
	$("#selectpickerTable").selectpicker('render');

	$("#selectpickerTable").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
		var target = $(this).find('option').eq(clickedIndex).text();
		$("#selectpickerTable").selectpicker('toggle');
		localStorage.setItem("selectedTable", target);
		if (localStorage.getItem("selectedProject[]") !== null){
			var tabProject_toSplit = localStorage.getItem("selectedProject[]");
			var tabProject = [];
			if (tabProject_toSplit.length != 0){
				tabProject = tabProject_toSplit.split(",");
			}
			if (tabProject.length != 0){
				updateTableViewerWithMultipleProject(tabProject);
			}
		}
	});
	
});
}