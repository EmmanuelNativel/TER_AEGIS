<select class="selectpicker" title="select a table" id="selectpickerTable" data-width="250px" data-live-search="true" data-selected-text-format="count > 3">
	<?php
		for ($i =0; $i < sizeof($table['available']) ; $i++){
			echo "<option value='".$table['available'][$i]."'>".$table['available'][$i]."</option>";
		}
	?>
</select>

<script>

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
		var tableLoaded = <?php echo json_encode($this->data['tableLoaded']); ?>;
		if(tableLoaded[target] == "true"){
			
			var needProject = <?php echo json_encode($this->data['sql_table_need_project']); ?>;
			
			$("#selectpickerTable").selectpicker('toggle');
			localStorage.setItem("selectedTable", target);
					
			$("#selectpickerProject").selectpicker({title: "Select A Project"});
			$("#selectpickerProject").selectpicker('render');
			
			if (needProject[target] == "false"){			
				
				
				if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
					var table = $('#tableValues').DataTable();
					resetTable(table);
				}
				
				localStorage.setItem("oldSelectedProject", "null");
				localStorage.setItem("selectedProject[]", "null");
				
				$("#selectpickerProject").selectpicker('val', '');
				
				$("#selectpickerProject").selectpicker({title: "No Project Needed"});
				$("#selectpickerProject").selectpicker('render');
		
				$('#selectpickerProject').attr('disabled',true);
				$('#selectpickerProject').selectpicker('refresh');
				
				updateTableViewerWithNoProject();
				
			}else{
				$('#selectpickerProject').attr('disabled',false);
				$('#selectpickerProject').selectpicker('refresh');
				
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
			}
			
			buildViewDataIndex(target);
		}else{
			// var selectpicker = $('#selectpickerTable');
			// selectpicker.selectpicker();
			// selectpicker.selectpicker({title: 'Cannot load : '+target}).selectpicker('render');
			$('#selectpickerProject').attr('disabled',true);
			$('#selectpickerProject').selectpicker('refresh');
			
			alert("la table ciblé ne s'est pas chargé correctement");
			localStorage.setItem("selectedTable", "null");
		}
		
		
	});
	
});

</script>