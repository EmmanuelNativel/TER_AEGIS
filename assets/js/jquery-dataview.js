//au chargement de la page, on affecte vide à la variable local tab_table
function initializeLocalStorageOnLoad(){
	localStorage.setItem("tab_table","");
	localStorage.setItem("myTableHeaderIsset", false);
	//localStorage.setItem("selectedTable", "null")
}

//cette fonction est appellé quand une table est séléctionner
//appel de la fonction << updateTable >>, pour chaque project actuellement selectionner dans le menu déroulant
function updateTableViewerWithMultipleProject(tabProject){
	
	if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
		var table = $('#tableValues').DataTable();
		resetTable(table);
	}
	var actualProject = "";
	if(tabProject != "null"){
		while(tabProject.length > 0){
			actualProject = tabProject.shift();
			updateTable(actualProject, true);
		}
	}
}

function updateTable(selectedProject, projectIsChecked){
	var selectedTable = localStorage.getItem("selectedTable");
	
	// si le project selectionné est all project
	if (selectedProject == "ALL"){
		var tabProject = <?php echo json_encode($this->data['project']['available']); ?>;
		var length_tabProject = tabProject.length;
		var oldProject_toSplit = localStorage.getItem("oldSelectedProject");
		var oldProject = [];
		//si selectAll est selectionné 
		if (projectIsChecked === true){
			if (oldProject_toSplit.length != 0){
				oldProject = oldProject_toSplit.split(",");
				while(oldProject.length > 0){
					tabProject = remove(tabProject,oldProject.shift());
				}
			}
			length_tabProject = tabProject.length;
			for (i=0;i<length_tabProject;i++){	
				createTable(selectedTable, tabProject[i], projectIsChecked);
			}
			
		}else{ // si deselectAll est selectionné
			if (oldProject_toSplit.length != 0){
				oldProject = oldProject_toSplit.split(",");
				if (oldProject != "null"){
					var shifted_oldProject = '';
					while(oldProject.length > 0){
						shifted_oldProject = oldProject.shift();
						if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
							deleteTable(shifted_oldProject);
						}
					}
				}
			}
		}
	}
	else{ // si le projet selectionné est singulier
		if (projectIsChecked === true){
			
			createTable(selectedTable, selectedProject,projectIsChecked);
		}else{
			if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
				deleteTable(selectedProject);
			}
		}
	}
}

function deleteTable(selectedProject){
	var table = $('#tableValues').DataTable();
	table.rows(".row_"+selectedProject).remove().draw();
	if (table.rows().count() == 0){
		resetTable(table);
		localStorage.setItem("myTableHeaderIsset", false);
	}
}

function createTable(selectedTable, selectedProject, projectIsChecked){
	/*
	alert("finir les requetes BD, , , , ");
	alert("organiser les requetes BD");
	alert("Design Pattern");
	alert("Organiser le code, script dans fichier script, style dans fichier style");
	alert("faire des commentaires");
	alert("enregistrer la version puis faire un compte rendu");
	*/
	var i = 0;
	var j = 0;
	
	var allIndexOfProject = <?php echo json_encode($this->data['dataProject']); ?>;
	var indexOfProject = allIndexOfProject[selectedTable]['indexOfProject'][selectedProject];
	
	var length_indexOfProject = indexOfProject.length;
	
	var allDataOfProject = <?php echo json_encode($this->data['dataProject']); ?>;
	var dataOfProject = allDataOfProject[selectedTable]['project'][selectedProject];
	var length_dataOfProject = dataOfProject.length;
	
	var myTableHeaderIsset = localStorage.getItem("myTableHeaderIsset");
	
	var myTableHead = document.getElementById('tableValues').getElementsByTagName('thead')[0];
	var myBodyRow = [];
	var myBodyRowFormated = [];
	
	if (length_dataOfProject > 0){
		
		if (myTableHeaderIsset == "false" ){
			var myHeaderRow = [];
			var myHeaderRowFormated = [];
			var columnObject = {};
	
			for (i=0;i<length_indexOfProject;i++){
				
				columnObject = {};
				myHeaderRow.push(indexOfProject[i]);
				
				columnObject["title"] = myHeaderRow[i];
				columnObject["data"] = myHeaderRow[i];
				
				myHeaderRowFormated.push(columnObject);
			}			
			$('#tableValues').DataTable({
				"lengthMenu": [ [7, 25, 50, 100, -1], [7, 25, 50, 100, "All"] ],
				"iDisplayLength": 7,
				"dom": 'lrtip',
				"searchFilter" : true,
				"paging": true,
				"columns": myHeaderRowFormated
			});
			
			localStorage.setItem("myTableHeaderIsset", true);
			var newSearchRow = myTableHead.insertRow(1);
			
			newSearchRow.setAttribute("name", "headerRow");
			for (i=0;i<length_indexOfProject;i++){
				newSearchRow.insertCell(i).outerHTML = "<td><input type='text' id='"+i+"' ondblclick='clearAndSeachFilter(this)' onkeyup='searchFilter(this.id, this.value)' data-type='input'></td>";
			}
		}
		var table = $('#tableValues').DataTable();
		
		for (i=0;i<length_dataOfProject;i++){

			myBodyRow = [];
			myBodyRowObject = {};
			
			myBodyRowObject["DT_RowClass"] = "row_"+selectedProject + " tableData";
			myBodyRowObject["DT_RowName"] = "";
			for (j=0;j<length_indexOfProject;j++){
				
				if (dataOfProject[i][indexOfProject[j]] == null){
					myBodyRow.push("");
				}
				else{
					myBodyRow.push(dataOfProject[i][indexOfProject[j]]);
				}
				myBodyRowObject[indexOfProject[j]] = myBodyRow[j];
			}
			table.rows.add([myBodyRowObject]);
		}
	table.draw();
	}
}

function resetTable(table){
	
	table.clear().destroy();
	var thoseTrWillBeDeleted = document.getElementsByName('headerRow');
	var trLen = thoseTrWillBeDeleted.length;
	while( trLen > 0){
		trLen = trLen -1;
		thoseTrWillBeDeleted[trLen].remove();
	}
	document.getElementById('tableValues').deleteRow(0);
	localStorage.setItem("myTableHeaderIsset", false);
}

function clearAndSeachFilter(column){
	column.value='';
	searchFilter(column.id,column.value);
}

function searchFilter(id, target) {
	$('#tableValues').DataTable().column(id).search(target,true, false, true).draw();
	
	if (target == ''){
		document.getElementById(id).style.backgroundColor = "white";
	}else{
		document.getElementById(id).style.backgroundColor = "#F0F8FF";
	}
}

function remove(array, element) {
	return array.filter(e => e !== element);
}