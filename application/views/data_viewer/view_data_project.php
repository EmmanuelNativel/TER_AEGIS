	<select class="selectpicker" id="selectpickerProject" data-actions-box="true" data-width="250px" data-live-search="true" data-selected-text-format="count > 3" multiple>
		<?php
			for ($i =0; $i < sizeof($project['available']) ; $i++){
				echo "<option value='".$project['available'][$i]."'>".$project['available'][$i]."</option>";
			}
		?>
		
	</select>
<script>

$(function() {

	$("#selectpickerProject").on('loaded.bs.select', function (e){
		if (localStorage.getItem("selectedProject[]") === "null"){
			$("#selectpickerProject").selectpicker({title: "Select A Project"});
		}
		$("#selectpickerProject").selectpicker('render');
	});
	
	$("#selectpickerProject").selectpicker({
		selectAllText: 'Select All Project',
		deselectAllText: 'Deselect All',
		countSelectedText: '{0} Projects Selected'	
	});

	$("#selectProject .bs-select-all").on('click', function() {
		localStorage.setItem("oldSelectedProject", $("#selectpickerProject").val());
		updateTable("ALL", true);
	});
	
	$("#selectProject .bs-deselect-all").on('click', function() {
		localStorage.setItem("oldSelectedProject", $("#selectpickerProject").val());
		updateTable("ALL", false);
	});
	
	$(function () {
	  $("#selectpickerProject").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
		var target = $(this).find('option').eq(clickedIndex).text();
		selectedProject = $("#selectpickerProject").val();
		if (selectedProject !== null){
			localStorage.setItem("selectedProject[]",selectedProject);
		}else{
			localStorage.setItem("selectedProject[]", null);
		}
		if (target != ""){
			updateTable(target, newValue);
		}		
		localStorage.setItem("oldSelectedProject", "null");
		//
	  });
	});

});

</script>