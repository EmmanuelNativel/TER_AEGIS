<div class="fullwidth-block">
    <div class="container">
        <h2 class="section-title">Visualization</h2>
        <?php echo form_open('data_viewer/visualize', 'class="contact-form"'); ?>
        <?php echo validation_errors() ?>

		<div class="btn-group btn-group-toggle" id="buttonPage" data-toggle="buttons">
			<label class="btn btn-secondary active" id="label_visualize" onClick="loadPage_visualize(this)">
				<input type="radio" name="options" id="button_visualize" autocomplete="off" checked> Data-Table
			</label>
			<label class="btn btn-secondary" id="label_dataviz" onClick="loadPage_dataviz(this)">
				<input type="radio" name="options" id="button_dataViz" autocomplete="off"> Dataviz
			</label>
			<label class="btn btn-secondary" id="label_requeteur" onClick="loadPage_requeteur(this)">
				<input type="radio" name="options" id="button_requeteur" autocomplete="off"> Query Tools
			</label>
		</div>

		<div class="boxed-content" id="dataViewBox">

			<?php
			$scripts = array('selectize.min');

			// data_table
			$view['view_data_table'] = $this->view('data_viewer/view_data_table',$table['available'], TRUE);
			$view['view_data_project'] = $this->view('data_viewer/view_data_project',$project['available'], TRUE);
			$view['view_data_index'] = $this->view('data_viewer/view_data_index', '', TRUE);

			echo "<p class='myDataViewMenu'> table : </p> <div class='myDataViewMenu' id ='selectTable'>";
			echo $view['view_data_table'];
			echo "</div>";
			echo "<p class='myDataViewMenu'> projects : </p> <div class='myDataViewMenu' id ='selectProject'>";
			echo $view['view_data_project'];
			echo "</div>";
			echo "<p class='myDataViewMenu'> index : </p> <div class='myDataViewMenu' id='selectIndex'>";
			echo $view['view_data_index'];
			echo "</div>";

			// dataVide


			// query builder
			$view['query_builder'] = $this->view('data_viewer/query_builder', '', TRUE, $scripts);
			$view['query_tool']['load'] = $this->view('data_viewer/view_query_tool_load_json', '', TRUE, $scripts);
			$view['query_tool']['save'] = $this->view('data_viewer/view_query_tool_save_json', '', TRUE, $scripts);
			// echo $view['query_builder'];

			?>
		</div>
        <div class="boxed-content" id="dataViewResult">
			<?php
			$view['view_data_values'] = $this->view('data_viewer/view_data_values', '', TRUE);
			echo $view['view_data_values'];
			$view['query_text_area'] = $this->view('data_viewer/query_text_area', '', TRUE);
			// echo $view['query_text_area'];
			?>
        </div>
    </div>
</div>

<script type="text/javascript">

window.onload  = initializeLocalStorageOnLoad();

//**********
//Initialize
//**********
//
//
//au chargement de la page, on affecte vide à la variable local tab_table
function initializeLocalStorageOnLoad(){

	localStorage.setItem("tab_table","");
	localStorage.setItem("myTableHeaderIsset", false);
	localStorage.setItem("selectedTable", "null")
	localStorage.setItem("selectedProject[]", "null");
	localStorage.setItem("selectedLoadQuery", "null");
	localStorage.setItem("autoCompleteDictionnary[]", JSON.stringify({}));
	localStorage.setItem("globalJSON", "null");
	localStorage.setItem("userJSON", "null");
	localStorage.setItem("popularJSON", "null");
	initialize_operator_meaning();
}

//
//
//**********


//**********
//DataTable
//**********
//
//

//cette fonction est appellé quand une table est sélectionné
//appel de la fonction << updateTable >>, pour chaque project actuellement selectionner dans le menu déroulant
function updateTableViewerWithMultipleProject(tabProject){
	var tableIsEmpty = 0;
	if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
		var table = $('#tableValues').DataTable();
		resetTable(table);
	}
	var actualProject = "";
	if(tabProject != "null"){
		while(tabProject.length > 0){
			actualProject = tabProject.shift();
			updateTable(actualProject, true);
			tableIsEmpty = tableIsEmpty + countValues(actualProject);
		}
		if(tableIsEmpty == 0){
			alert("la table sélectionné : " + localStorage.getItem("selectedTable") + " est vide");
		}
	}
}

function updateTableViewerWithNoProject(){
	createTable(localStorage.getItem("selectedTable"), "noProjectNeeded",true);
}

//
//
//**********

//**********
// Ces fonctions sont appellée lorsque le menu index est sélectionné
// Modification des index

function buildViewDataIndex(selectedTable){
	var i = 0;
	var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
	var indexOfProject = allIndexOfProject[selectedTable];
	var length_IndexOfProject = indexOfProject.length;

	var myTableIndex = document.getElementById('selectpickerIndex');
	var tabOptions = [];
	var newOption = '';

	// reset table
	if (myTableIndex.options.length != 0){
		$("#selectpickerIndex").find('options').remove();
		localStorage.setItem("selectedIndex[]", null);
	}
	for (i=0;i<length_IndexOfProject;i++){
		newOption = "<option selected value='"+indexOfProject[i]+"'>"+indexOfProject[i]+" </option>";
		tabOptions.push(newOption);
	}
	$("#selectpickerIndex").html(tabOptions);
	$("#selectpickerIndex").selectpicker('refresh');

}

function buildQueryDataIndex(selectedTable){
	var i = 0;
	var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
	var indexOfProject = allIndexOfProject[selectedTable];
	var length_IndexOfProject = indexOfProject.length;

	var myTableIndex = document.getElementById('selectpickerIndex');
	var tabOptions = [];
	var newOption = '';

	// reset table
	if (myTableIndex.options.length != 0){
		$("#selectpickerIndex").find('options').remove();
	}

	for (i=0;i<length_IndexOfProject;i++){
		newOption = "<option selected value='"+indexOfProject[i]+"'>"+indexOfProject[i]+"</option>";
		tabOptions.push(newOption);
	}
	$("#selectpickerIndex").html(tabOptions);
	$("#selectpickerIndex").selectpicker('refresh');

}

function updateIndex(selectedColumn, columnIsChecked){

	var table = $('#tableValues').DataTable();
	var columnIndexStorage = JSON.parse(localStorage.getItem('columnIndexStorage'));
	var columnIndex = '' ;
	var columnSearchStorage = JSON.parse(localStorage.getItem('columnSearchStorage'));
	var columnSearchIndex = '' ;
	var row_searchInput = document.getElementsByName('headerRow');
	var i = 0;
	// si le project selectionné est all project
	if (selectedColumn == "ALL"){
			// faire l'ajout des input search  ainsi que leurs suppression pour 'ALL'

		var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
		var tabIndex = allIndexOfProject[localStorage.getItem("selectedTable")];

		if (columnIsChecked === true){
			updateIndex_allSelected_columnIsChecked(table, row_searchInput, tabIndex, columnIndexStorage, columnSearchStorage);
		}else{
			updateIndex_allSelected_columnIsNotChecked(table, row_searchInput, columnIndexStorage);
		}
	}else{

		columnIndex = columnIndexStorage[selectedColumn.trim()];
		columnSearchIndex = columnSearchStorage[selectedColumn.trim()];
		var tabColName = '';

		table.column(columnIndex).visible(columnIsChecked);
		if (columnIsChecked === true){
			updateIndex_singleSelected_columnIsChecked(row_searchInput, selectedColumn, columnIndex, columnIndexStorage, columnSearchStorage);
		}else{
			updateIndex_singleSelected_columnIsNotChecked(row_searchInput, columnSearchIndex);
		}
	}
}

function updateIndex_allSelected_columnIsChecked(table, row_searchInput, tabIndex, columnIndexStorage, columnSearchStorage){

	var oldIndex_toSplit = localStorage.getItem("oldSelectedIndex");
	var oldIndex = [];
	var columnIndex = '';

	if (oldIndex_toSplit.length != 0){
		oldIndex = oldIndex_toSplit.split(",");
		while(oldIndex.length > 0){
			tabIndex = remove(tabIndex,oldIndex.shift());
		}
	}
	var length_tabIndex = tabIndex.length;
	for (i=0;i<length_tabIndex;i++){
		columnIndex = columnIndexStorage[tabIndex[i].trim()];
		selectedColumn = tabIndex[i].trim();
		table.column(columnIndex).visible(true);
		updateIndex_singleSelected_columnIsChecked(row_searchInput, selectedColumn, columnIndex, columnIndexStorage, columnSearchStorage)
	}
}

function updateIndex_allSelected_columnIsNotChecked(table, row_searchInput, columnIndexStorage){

	var oldIndex_toSplit = localStorage.getItem("oldSelectedIndex");
	var oldIndex = [];
	var columnIndex = '';

	if (oldIndex_toSplit.length != 0){
		oldIndex = oldIndex_toSplit.split(",");
		if (oldIndex != "null"){
			var shifted_oldIndex = '';
			while(oldIndex.length > 0){
				shifted_oldIndex = oldIndex.shift();
				//alert(shifted_oldIndex);
				columnIndex = columnIndexStorage[shifted_oldIndex.trim()];
				table.column(columnIndex).visible(false);
			}
		}
	}
	while(row_searchInput[0].cells.length > 0){
		row_searchInput[0].cells[0].remove();
	}
	localStorage.setItem('columnSearchStorage', JSON.stringify({}));
}

function updateIndex_singleSelected_columnIsChecked(row_searchInput, selectedColumn, columnIndex, columnIndexStorage, columnSearchStorage){
	var i = 0;

	if(row_searchInput[0].cells.length == 0){
		// si la valeur à ajouter est vide on créé simplement la ligne,
		createIndexColumn(0,selectedColumn.trim());
	}else{
		var searchFirstName = $("#"+row_searchInput[0].cells[0].cellIndex).prop('name');
		var searchFirstIndex = columnIndexStorage[searchFirstName.trim()];
		var searchLastName = $("#"+row_searchInput[0].cells[row_searchInput[0].cells.length-1].cellIndex).prop('name');
		var searchLastIndex = columnIndexStorage[searchLastName.trim()];
		// si la valeur a ajouter est au début de la liste on l'ajoute avant tout simplement
		if (columnIndex < searchFirstIndex){
			for(i=row_searchInput[0].cells.length-1;i >= 0;i--){
				updateLocalStorageIndex(i+1,$("#"+row_searchInput[0].cells[i].cellIndex).prop('name'));
				$("#"+i).prop("id",i+1);
			}
			createIndexColumn(0,selectedColumn.trim());
		}
		else if (columnIndex > searchLastIndex){
			createIndexColumn(row_searchInput[0].cells.length,selectedColumn.trim());
		}else{
			// si la valeur à ajouter ce situe entre 2 colonne nous cherchons avant qu'elle colonne elle doit se trouver, et on l'ajoute à cette endroit
			var searchActualName = '';
			var searchActualIndex = '';
			var newSearchIndex = 0;
			var indexFind = false;

			i = 0;
			while((i < row_searchInput[0].cells.length) && (indexFind === false)){
				 searchActualName = $("#"+row_searchInput[0].cells[i].cellIndex).prop('name');
				 searchActualIndex = columnIndexStorage[searchActualName.trim()];
				 if (columnIndex < searchActualIndex){
					indexFind = true;
					newSearchIndex = columnSearchStorage[searchActualName.trim()];
				 }
				 i++;
			}
			for(i=row_searchInput[0].cells.length-1;i >= newSearchIndex;i--){
				updateLocalStorageIndex(i+1,$("#"+row_searchInput[0].cells[i].cellIndex).prop('name'));
				$("#"+i).prop("id",i+1);
			}
			createIndexColumn(newSearchIndex,selectedColumn.trim());
		}
	}
}

function updateIndex_singleSelected_columnIsNotChecked(row_searchInput, columnSearchIndex){
	var tabColIndex = 0;
	var tabColName = '';
	var columnPosition = {};

	row_searchInput[0].cells[columnSearchIndex].remove();
	if(row_searchInput[0].cells.length == 0){
		localStorage.setItem('columnSearchStorage', JSON.stringify(columnPosition));
	}else{
		while(columnSearchIndex < row_searchInput[0].cells.length){
			columnSearchIndex = columnSearchIndex + 1;
			$("#"+columnSearchIndex).prop("id",columnSearchIndex-1);
		}
		for(i=0;i< row_searchInput[0].cells.length; i++){
			tabColIndex = row_searchInput[0].cells[i].cellIndex;
			tabColName = $("#"+row_searchInput[0].cells[i].cellIndex).prop('name');
			columnPosition[tabColName] = tabColIndex;
		}
		localStorage.setItem('columnSearchStorage', JSON.stringify(columnPosition));
	}
}

function createIndexColumn(index,name){
	updateLocalStorageIndex(index,name);
	document.getElementsByName('headerRow')[0].insertCell(index).outerHTML =
	"<td><input list='dictionnary_"+name+"' name='"+name+"' id='"+index+"' placeholder='"+name+"'"+
	"ondblclick='clearAndSeachFilter(this)' onkeyup='searchFilter(this.id, this.name, this.value)'/></td>";
}

function updateLocalStorageIndex(index, name){
	var columnPosition = {};
	columnPosition = JSON.parse(localStorage.getItem('columnSearchStorage'));
	columnPosition[name] = index;
	localStorage.setItem('columnSearchStorage', JSON.stringify(columnPosition));
}

///////////////////////

function updateTable(selectedProject, projectIsChecked){
	var selectedTable = localStorage.getItem("selectedTable");
	var tableIsEmpty = 0;
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
				tableIsEmpty = tableIsEmpty + countValues(tabProject[i]);
			}
			if(tableIsEmpty == 0){
				alert("la table sélectionné : " + selectedTable + " est vide");
			}
		}else{ // si deselectAll est selectionné
			if (oldProject_toSplit.length != 0){
				oldProject = oldProject_toSplit.split(",");
				if (oldProject != "null"){
					var shifted_oldProject = '';
					while( (oldProject.length > 0) && ($.fn.DataTable.isDataTable( '#tableValues' )) ){
						shifted_oldProject = oldProject.shift();
						if($('#tableValues').DataTable().rows(".row_"+shifted_oldProject).count() != 0){
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
	updateAutoCompleteDictionnary(selectedProject);
}

function createTable(selectedTable, selectedProject, projectIsChecked){
	var i = 0;
	var j = 0;

	var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
	var indexOfProject = allIndexOfProject[selectedTable];

	var length_indexOfProject = indexOfProject.length;

	var allDataOfProject = <?php echo json_encode($this->data['dataProject']); ?>;
	var dataOfProject = allDataOfProject[selectedTable]['project'][selectedProject];
	var length_dataOfProject = dataOfProject.length;

	var myTableHeaderIsset = localStorage.getItem("myTableHeaderIsset");

	var myTableHead = document.getElementById('tableValues').getElementsByTagName('thead')[0];
	var myBodyRow = [];
	var dataTableValues = [];
	var autoCompleteDictionnary = {};
	autoCompleteDictionnary = JSON.parse(localStorage.getItem('autoCompleteDictionnary[]'));

	if (length_dataOfProject > 0){

		if (myTableHeaderIsset == "false" ){
			var myHeaderRow = [];
			var myHeaderRowFormated = [];
			var columnObject = {};
			var columnPosition = {};

			for (i=0;i<length_indexOfProject;i++){

				columnObject = {};
				myHeaderRow.push(indexOfProject[i]);

				columnObject["title"] = myHeaderRow[i];
				columnObject["data"] = myHeaderRow[i];
				columnPosition[myHeaderRow[i]] = i;

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
			// Put the object into storage
			localStorage.setItem('columnIndexStorage', JSON.stringify(columnPosition));
			localStorage.setItem('columnSearchStorage', JSON.stringify(columnPosition));

			localStorage.setItem("myTableHeaderIsset", true);
			var newSearchRow = myTableHead.insertRow(1);

			newSearchRow.setAttribute("name", "headerRow");
			newSearchRow.setAttribute("id", "headerRow");

			for (i=0;i<length_indexOfProject;i++){
				newSearchRow.insertCell(i).outerHTML = "<td><input list='dictionnary_"+indexOfProject[i]+"' name='"+indexOfProject[i]+"'"+
				"id='"+i+"' placeholder='"+indexOfProject[i]+"' ondblclick='clearAndSeachFilter(this)'"+
				"onkeyup='searchFilter(this.id, this.name, this.value)'/></td>";
			}
			// initialise le dictionnaire d'autocompletion
			for (i=0;i<length_indexOfProject;i++){
				autoCompleteDictionnary[indexOfProject[i]] = [];
			}
		}
		var table = $('#tableValues').DataTable();
		// pour chaque données remplir le body du tableau ainsi que remplir la variable dictionnaire d'autocompletion
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
					autoCompleteDictionnary[indexOfProject[j]].push(myBodyRow[j]);
				}
				myBodyRowObject[indexOfProject[j]] = myBodyRow[j];
			}
			dataTableValues.push(myBodyRowObject);
		}
		dataTableValues = uniqueObject(dataTableValues);
		for (i=0;i<length_indexOfProject;i++){
			autoCompleteDictionnary[indexOfProject[i]] = unique(autoCompleteDictionnary[indexOfProject[i]]);
			setAutoCompleteDictionnary(indexOfProject[i], autoCompleteDictionnary[indexOfProject[i]]);
		}
		localStorage.setItem("autoCompleteDictionnary[]", JSON.stringify(autoCompleteDictionnary));
		table.rows.add(dataTableValues).draw();
	}
}

function createQueryToolsTable(query_data,query_data_index){
	var i = 0;
	var j = 0;

	var length_query_data = query_data.length;
	var length_query_data_index = query_data_index.length;
	var myTableHead = document.getElementById('tableValues').getElementsByTagName('thead')[0];
	var myBodyRow = [];
	var dataTableValues = [];
	var autoCompleteDictionnary = {};
	autoCompleteDictionnary = JSON.parse(localStorage.getItem('autoCompleteDictionnary[]'));

	if (length_query_data > 0){

		var myHeaderRow = [];
		var myHeaderRowFormated = [];
		var columnObject = {};
		var columnPosition = {};
		for (i=0;i<length_query_data_index;i++){

			columnObject = {};
			myHeaderRow.push(query_data_index[i]);

			columnObject["title"] = myHeaderRow[i];
			columnObject["data"] = myHeaderRow[i];
			columnPosition[myHeaderRow[i]] = i;

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

		// bug sur le filtre
		/*
		var newSearchRow = myTableHead.insertRow(1);

		newSearchRow.setAttribute("name", "headerRow");
		newSearchRow.setAttribute("id", "headerRow");

		for (i=0;i<length_query_data_index;i++){
			newSearchRow.insertCell(i).outerHTML = "<td><input list='dictionnary_"+query_data_index[i]+"' name='"+query_data_index[i]+"'"+
			"id='"+i+"' placeholder='"+query_data_index[i]+"' ondblclick='clearAndSeachFilter(this)'"+
			"onkeyup='searchFilter(this.id, this.name, this.value)'/></td>";
		}
		*/
		// initialise le dictionnaire d'autocompletion
		for (i=0;i<length_query_data_index;i++){
			autoCompleteDictionnary[query_data_index[i]] = [];
		}
		var table = $('#tableValues').DataTable();
		// pour chaque données remplir le body du tableau ainsi que remplir la variable dictionnaire d'autocompletion
		for (i=0;i<length_query_data;i++){

			myBodyRow = [];
			myBodyRowObject = {};

			myBodyRowObject["DT_RowClass"] = "row_query tableData";
			myBodyRowObject["DT_RowName"] = "";

			for (j=0;j<length_query_data_index;j++){

				if (query_data[i][query_data_index[j]] == null){
					myBodyRow.push("");
				}
				else{
					myBodyRow.push(query_data[i][query_data_index[j]]);
					autoCompleteDictionnary[query_data_index[j]].push(myBodyRow[j]);
				}
				myBodyRowObject[query_data_index[j]] = myBodyRow[j];
			}
			dataTableValues.push(myBodyRowObject);
		}
		dataTableValues = uniqueObject(dataTableValues);
		for (i=0;i<length_query_data_index;i++){
			autoCompleteDictionnary[query_data_index[i]] = unique(autoCompleteDictionnary[query_data_index[i]]);
			setAutoCompleteDictionnary(query_data_index[i], autoCompleteDictionnary[query_data_index[i]]);
		}
		localStorage.setItem("autoCompleteDictionnary[]", JSON.stringify(autoCompleteDictionnary));
		table.rows.add(dataTableValues).draw();
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

///////////////////////


function updateAutoCompleteDictionnary(selectedProject){

	var selectedProject_toSplit = localStorage.getItem("selectedProject[]");
	var selectedProject = [];
	var i = 0;
	var j = 0;
	var k = 0;

	if (selectedProject_toSplit.length != 0){

		selectedProject = selectedProject_toSplit.split(",");
		var selectedTable = localStorage.getItem("selectedTable");

		var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
		var indexOfProject = allIndexOfProject[selectedTable];
		var length_indexOfProject = indexOfProject.length;

		var allDataOfProject = '';
		var dataOfProject = '';
		var length_dataOfProject = '' ;

		var autoCompleteDictionnary = {};
		for (i=0;i<length_indexOfProject;i++){
			autoCompleteDictionnary[indexOfProject[i]] = [];
		}
		for(k=0;k< selectedProject.length;k++){

			allDataOfProject = <?php echo json_encode($this->data['dataProject']); ?>;
			dataOfProject = allDataOfProject[selectedTable]['project'][selectedProject[k]];
			length_dataOfProject = dataOfProject.length;
			for (i=0;i<length_dataOfProject;i++){
				for (j=0;j< length_indexOfProject;j++){
					if (dataOfProject[i][indexOfProject[j]] != null){
						autoCompleteDictionnary[indexOfProject[j]].push(dataOfProject[i][indexOfProject[j]]);
					}
				}
			}
		}
		for(k=0;k< length_indexOfProject;k++){
			autoCompleteDictionnary[indexOfProject[k]] = unique(autoCompleteDictionnary[indexOfProject[k]]);
			setAutoCompleteDictionnary(indexOfProject[k],autoCompleteDictionnary[indexOfProject[k]]);
		}
	}
}

function setAutoCompleteDictionnary(inputName, dictionnary){
	var i = 0;

	var dictionnary_id= "dictionnary_"+inputName;
	if ($.isNumeric(dictionnary[0])){
		dictionnary.sort(function(a, b){return a - b});
	}else{
		dictionnary.sort();
	}
	var len_dictionnary = dictionnary.length;
	var dictionnaryOptions = '';

	for(i = 0; i< len_dictionnary;i++){
		dictionnaryOptions = dictionnaryOptions+"<option value='"+dictionnary[i]+"'/>";
	}

	if(!($('#'+dictionnary_id).length)){
		$("<datalist class='dictionnaryList' id='"+dictionnary_id+"'>"+dictionnaryOptions+"</datalist>").appendTo("body");
	}else{
		$('#'+dictionnary_id).html(dictionnaryOptions);
	}
}


///////////////////////



function clearAndSeachFilter(column){
	column.value='';
	searchFilter(column.id,column.name,column.value);
}


function searchFilter(id, column, target) {
	var table = $('#tableValues').DataTable();
	var columnStorage = JSON.parse(localStorage.getItem('columnSearchStorage'));

	var newDictionnary = {};
	var index = 0;
	// rajouter un \devant le + car la regex le considère comme un + alors que c'est simplement ? un string ?

	var unchangedId = JSON.parse(localStorage.getItem('columnIndexStorage'))[column];
	$('#tableValues').DataTable().column(unchangedId).search(target,true, false, true).draw();

	// applique le filtre
	var dictionnary = table.rows( { filter : 'applied'} ).data();

	if (target == ''){
		document.getElementById(id).style.backgroundColor = "white";
	}else{
		document.getElementById(id).style.backgroundColor = "#F0F8FF";
	}
	// /*
	// var dictionnary = []
	for (var actualColumn in columnStorage) {
		newDictionnary[actualColumn] = [];
		for(index=0;index<dictionnary.length;index++){
			newDictionnary[actualColumn].push(dictionnary[index][actualColumn]);
		}
		newDictionnary[actualColumn] = unique(newDictionnary[actualColumn]);
	}
	for (var actualColumn in newDictionnary) {
		setAutoCompleteDictionnary(actualColumn, newDictionnary[actualColumn]);
	}

}


///////////////////////


function countValues(selectedProject){

	var selectedTable = localStorage.getItem("selectedTable");

	var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
	var indexOfProject = allIndexOfProject[selectedTable];

	var length_indexOfProject = indexOfProject.length;

	var allDataOfProject = <?php echo json_encode($this->data['dataProject']); ?>;
	var dataOfProject = allDataOfProject[selectedTable]['project'][selectedProject];

	return dataOfProject.length;

}

/*
*
* Chargement des différentes pages : visualize, datavid, requeteur
*
*/

function loadPage_deleteTable(){
	var table = $('#tableValues').DataTable();
	table.rows(".row_"+localStorage.getItem("selectedProject[]")).remove().draw();
	resetTable(table);
}

function loadPage_visualize(){

	if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
		loadPage_deleteTable();
	}

	var content = document.getElementById("dataViewBox");
	deleteContent(content);
	var boxResult = document.getElementById("dataViewResult");
	deleteContent(boxResult);

	// if(document.getElementById("dataViewQueryToolsSelecter")){
		// document.getElementById("dataViewQueryToolsSelecter").remove();
	// }
	initializeLocalStorageOnLoad();

	var tabTable = <?php echo json_encode($view['view_data_table']); ?>;
	var tabProject = <?php echo json_encode($view['view_data_project']); ?>;
	var tabIndex = <?php echo json_encode($view['view_data_index']); ?>;

	var myDataViewMenu = [];

	myDataViewMenu.push("<p class='myDataViewMenu'> table : </p> <div class='myDataViewMenu' id ='selectTable'>"+tabTable+"</div>");
	myDataViewMenu.push("<p class='myDataViewMenu'> projects : </p> <div class='myDataViewMenu' id ='selectProject'>"+tabProject+"</div>");
	myDataViewMenu.push("<p class='myDataViewMenu'> index : </p> <div class='myDataViewMenu' id='selectIndex'>"+tabIndex+"</div>");

	$(content).html(myDataViewMenu);

	myDataViewMenu = [];

	var tabBoxResult = <?php echo json_encode($view['view_data_values']); ?>;

	myDataViewMenu.push(tabBoxResult);

	$(boxResult).html(myDataViewMenu);

}

function loadPage_dataviz(){

	if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
		loadPage_deleteTable();
	}

	var content = document.getElementById("dataViewBox");
	deleteContent(content);
	deleteContent(document.getElementById("dataViewResult"));
	// if(document.getElementById("dataViewQueryToolsSelecter")){
		// document.getElementById("dataViewQueryToolsSelecter").remove();
	// }

}

function loadPage_requeteur(){

	var myDataViewMenu = [];
	var boxContent = document.getElementById("dataViewBox");
	var boxResult = document.getElementById("dataViewResult");

	if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
		loadPage_deleteTable();
	}

	deleteContent(boxContent);
	deleteContent(boxResult);

	// if(document.getElementById("dataViewQueryToolsSelecter")){
		// document.getElementById("dataViewQueryToolsSelecter").remove();
	// }

	// $("<div class='boxed-content' id='dataViewQueryToolsSelecter'></div>").insertBefore("#dataViewBox");
	var view_query_tools = <?php echo json_encode($view['query_builder']); ?>;

	myDataViewMenu.push(view_query_tools);

	$(boxContent).html(myDataViewMenu);

	myDataViewMenu = [];

	var tabBoxResult = <?php echo json_encode($view['query_text_area']); ?>;

	myDataViewMenu.push(tabBoxResult);

	$(boxResult).html(myDataViewMenu);

	initialize_query_tools_selecter();
	initialize_query_tools();
}


///////////////////////


function initialize_query_tools_selecter(){

	var boxContent = document.getElementById("dataViewQueryToolsSelecter");
	var myDataViewMenu = [];

	var allTableAvailable = <?php echo json_encode($this->data['table']['available']); ?>;
	var allColumn = <?php echo json_encode($_SESSION['indexProject']); ?>;
	var tabIndex = [];
	var options = '';
	var selectPickerIndex = '';
	// allIndexOfProject[localStorage.getItem("selectedTable")];
	var index_table = 0;
	var index_column = 0;

	for (index_table = 0; index_table < allTableAvailable.length; index_table++){
		tabIndex = allColumn[allTableAvailable[index_table]];
		selectPickerIndex = "selectPicker_"+ allTableAvailable[index_table];
		options = '';
		for (index_column = 0; index_column < tabIndex.length; index_column++){
			options = options + "<option>"+tabIndex[index_column]+"</option>";
		}
		// alert(selectPickerIndex);
		myDataViewMenu.push("<select class='selectpicker' id='"+selectPickerIndex+"' data-actions-box='true' data-width='250px' data-live-search='true' data-selected-text-format='static' multiple>"+options+"</select> ");
		// alert(myDataViewMenu);
	}
	$(boxContent).html(myDataViewMenu);

}

function initialize_query_tools(){

	var i = 0;
	var allDictionnary = [];
	var dictionnary_table = load_query_tools_dictionnary_table();
	var dictionnary_column = load_query_tools_dictionnary_column();
	allDictionnary.push(dictionnary_column);

	var builder_filter = initialize_builder_filters(allDictionnary);

	var rules_basic = {
		rules: [{
			id: '-1'
		}]
	};

		// Fix for Selectize
	$('#builder').on('afterCreateRuleInput.queryBuilder', function(e, rule) {
	  if (rule.filter.plugin == 'selectize') {
		rule.$el.find('.rule-value-container').css('min-width', '200px')
		  .find('.selectize-control').removeClass('form-control');
	  }
	});

	$('#builder').queryBuilder({
		filters: builder_filter,
		rules: rules_basic
	});

	// When rules change update the assistance input
	$('#builder').on('afterDeleteGroup.queryBuilder afterAddRule.queryBuilder afterUpdateRuleFilter.queryBuilder afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function(e) {
		var queryToolsRequest = $('#builder').queryBuilder('getRules');
		if (!$.isEmptyObject(queryToolsRequest)) {
			request = query_tools_clause_into_SQLRequest(queryToolsRequest);
			fill_query_tools_text_area(request);
		}
	});

	$('#btn_reset_query').on('click', function() {

		if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
			loadPage_deleteTable();
		}

		deleteContent(document.getElementById("dataViewResult"));
		delete_query_tool_tabs();

		$(document.getElementById("dataViewResult")).html(<?php echo json_encode($view['query_text_area']); ?>);

		$('#builder').queryBuilder('reset');
		var textArea = document.getElementById("query_textarea");
		textArea.value = '';
	});

	$('#btn_save_query').on('click', function() {

		if (document.getElementById("tableValues")){
			if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
				loadPage_deleteTable();
			}
			deleteContent(document.getElementById("dataViewResult"));
			$(document.getElementById("dataViewResult")).html(<?php echo json_encode($view['query_text_area']); ?>);
		}
		delete_query_tool_tabs();

		var boxResult = document.getElementById("dataViewBox");
		var view_query_tool_load = <?php echo json_encode($view['query_tool']['save']); ?>;
		$(boxResult).append("<div class='boxed-content' id='dataViewSaveOrLoadBox'/>");

		var boxLoadResult = document.getElementById("dataViewSaveOrLoadBox");
		$(boxLoadResult).append(view_query_tool_load);

	});

	$('#btn_load_query').on('click', function() {

		if (document.getElementById("tableValues")){
			if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
				loadPage_deleteTable();
			}
			deleteContent(document.getElementById("dataViewResult"));
			$(document.getElementById("dataViewResult")).html(<?php echo json_encode($view['query_text_area']); ?>);
		}
		delete_query_tool_tabs();

		var boxResult = document.getElementById("dataViewBox");
		var view_query_tool_load = <?php echo json_encode($view['query_tool']['load']); ?>;
		$(boxResult).append("<div class='boxed-content' id='dataViewSaveOrLoadBox'/>");

		var boxLoadResult = document.getElementById("dataViewSaveOrLoadBox");
		$(boxLoadResult).append(view_query_tool_load);

		//load query_tools
		query_tools_loadDB();

	});

	$('#btn_execute_query').on('click', function() {
		var queryToolsRequest = $('#builder').queryBuilder('getRules');
		if (!$.isEmptyObject(queryToolsRequest)) {
			var myDataViewMenu = [];
			if (document.getElementById("tableValues")){
				if ($.fn.DataTable.isDataTable( '#tableValues' ) ) {
					loadPage_deleteTable();
				}
			}
			delete_query_tool_tabs();
			var textArea = document.getElementById("dataViewResult");
			deleteContent(textArea);

			request = query_tools_clause_into_SQLRequest2(queryToolsRequest);
			query_tools_executeQuery(request);
		}
	});

}

function query_cancel_button(){
	delete_query_tool_tabs();
	fill_query_tools_text_area('');
}

function load_query_submit_button(){
	var loaded_rules = JSON.parse(localStorage.getItem("selectedLoadQuery"));
	$('#builder').queryBuilder('setRules',loaded_rules);
	delete_query_tool_tabs();
}

function save_query_submit_button(){
	if (test_save_query() === 'valid'){
		var DAOQueryToolsSave = {};

		var query_id_value = "not_known_yet";
		var query_name = document.getElementById("query_tool_save_name").value;
		var queryToolsRequest = $('#builder').queryBuilder('getRules');
		var creator = <?php echo json_encode($_SESSION['pseudo']); ?>;

		DAOQueryToolsSave['query_tool_id'] = query_id_value;
		DAOQueryToolsSave['query_tool_name'] = query_name;
		DAOQueryToolsSave['query_tool_json'] = JSON.stringify(queryToolsRequest);
		DAOQueryToolsSave['creator'] = creator;

		query_tools_saveDB(DAOQueryToolsSave);
	}else{
		alert(test_save_query());
	}
}

function test_save_query(){
	var result = '';
	var test_valid = true;

	var query_id = 0;
	var query_name = document.getElementById("query_tool_save_name").value;
	var queryToolsRequest = $('#builder').queryBuilder('getRules');
	var creator = <?php echo json_encode($_SESSION['pseudo']); ?>;

	// test on query_id
	if (creator === null){
		result = 'creator is null';
		test_valid = false;
	}

	if (test_valid === true){
		result = 'valid';
	}
	return result;
}

function query_tools_loadDB(){
	// url = window.location.origin +"/daphne.cirad.fr/index.php/data_viewer/load_query_tool";
	url = window.location.origin +"/index.php/data_viewer/load_query_tool";
	$.ajax({
	  type: "POST",
	  url: url,
	  dataType: "json",
	  cache:false,
	  success: function(result){
		  localStorage.setItem("globalJSON", JSON.stringify(result['global']));
		  localStorage.setItem("userJSON", JSON.stringify(result['user']));
		  localStorage.setItem("popularJSON", JSON.stringify(result['popular']));
		  if (result !== null){
			  load_query_tools_load_dropdown_button(result['global'],result['user'],result['popular']);
			fill_query_tools_text_area("successfully loaded");
		  }else{
			  fill_query_tools_text_area("can't load request");
		  }
	},
	  error: function (data, status, err) {
		console.log('Something went wrong', status, err);
		console.log(JSON.stringify(data));
		fill_query_tools_text_area(JSON.stringify(err));
	  }
	});
}

function load_query_tools_load_dropdown_button(globalJSON,userJSON,popularJSON){

	var length_userJSON = userJSON.length;
	var length_popularJSON = popularJSON.length;


	var myUserJSONSelectPicker = document.getElementById('selectpickerQueryToolLoadIndividualJSON');
	var popularJSONSelectPicker = document.getElementById('selectpickerQueryToolLoadPopularJSON');

	var tabOptions = [];
	var newOption = '';

	// reset table
	if (myUserJSONSelectPicker.options.length != 0){
		$("#selectpickerQueryToolLoadIndividualJSON").find('options').remove();
	}

	if (popularJSONSelectPicker.options.length != 0){
		$("#selectpickerQueryToolLoadPopularJSON").find('options').remove();
	}


	for (i=0;i<length_userJSON;i++){
		newOption = "<option selected value='"+userJSON[i]['query_tool_name']+"'>"+userJSON[i]['query_tool_name']+"</option>";
		tabOptions.push(newOption);
	}

	$("#selectpickerQueryToolLoadIndividualJSON").html(tabOptions);
	$("#selectpickerQueryToolLoadIndividualJSON").val('default');
	$("#selectpickerQueryToolLoadIndividualJSON").selectpicker('refresh');

	tabOptions = [];

	for (i=0;i<length_popularJSON;i++){
		newOption = "<option selected value='"+popularJSON[i]['query_tool_name']+"'>"+popularJSON[i]['query_tool_name']+"</option>";
		tabOptions.push(newOption);
	}

	$("#selectpickerQueryToolLoadPopularJSON").html(tabOptions);
	$("#selectpickerQueryToolLoadPopularJSON").val('default');
	$("#selectpickerQueryToolLoadPopularJSON").selectpicker('refresh');
}

function query_tools_saveDB(target){
	if (target != '') {
	    // url = window.location.origin +"/daphne.cirad.fr/index.php/data_viewer/save_query_tool";
	    url = window.location.origin +"/index.php/data_viewer/save_query_tool";
	    $.ajax({
	      type: "POST",
	      url: url,
	      data: {
	        dao_query_tool: target
	      },
	      dataType: "json",
	      cache:false,
	      success: function(result){
			  if (result !== null){
				fill_query_tools_text_area("successfully saved");
			  }else{
				  fill_query_tools_text_area("can't save request");
			  }
		},
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(JSON.stringify(data));
			fill_query_tools_text_area(JSON.stringify(err));
	      }
	    });
	}else{
		fill_query_tools_text_area("error : request has no name");
		// $("#error_add_operation_type").text("Le nom de l'opération est obligatoire");
	}
}

function query_tools_executeQuery(target){
	// alert(target);
	if (target != '') {
	    // url = window.location.origin +"/daphne.cirad.fr/index.php/data_viewer/execute_query_tool";
	    url = window.location.origin +"/index.php/data_viewer/execute_query_tool";
	    $.ajax({
	      type: "POST",
	      url: url,
	      data: {
	        request: target
	      },
	      dataType: "json",
	      cache:false,
	      success: function(result){
			  if (result !== null){
				query_tools_showQuery(result['data'],result['index']);
			  }else{
				  // fill_query_tools_text_area("can't exec request");
			  }
		},
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(JSON.stringify(data));
			// fill_query_tools_text_area(JSON.stringify(err));
	      }
	    });
	}else{
		// fill_query_tools_text_area("error : request has no name");
		// $("#error_add_operation_type").text("Le nom de l'opération est obligatoire");
	}
}

function query_tools_showQuery(data,data_index){

	var table = <?php echo json_encode($view['view_data_values']); ?>;
	var index = <?php echo json_encode($view['view_data_index']); ?>;
	var boxTable = '';
	var boxResult = document.getElementById("dataViewResult");

	$(boxResult).html(table);

	createQueryToolsTable(data,data_index);
	// $(boxResult).prepend("<div id=query_index style='text-align:right;'>"+index+"</div>");
	// buildQueryDataIndex('observation');
}

function query_tools_clause_into_SQLRequest(queryToolsRequest){

	var simplifiedRequest = {};
	var result = '';
	var tabClause = [];
	var clause = {};
	var index = 0;

	if (queryToolsRequest['rules'].length >= 2){
		simplifiedRequest['condition'] = queryToolsRequest['condition'];
	}else{
		simplifiedRequest['condition'] = "null";
	}
	for(index = 0; index < queryToolsRequest['rules'].length; index++){
		if (typeof queryToolsRequest['rules'][index]['id'] !== 'undefined'){
			clause = {};
			clause['id'] = queryToolsRequest['rules'][index]['id'];
			clause['operator'] = queryToolsRequest['rules'][index]['operator'];
			clause['value'] = queryToolsRequest['rules'][index]['value'];
			tabClause.push(clause);
		}
		if(queryToolsRequest['rules'][index]['rules']){
			tabClause.push(get_query_tools_clause(queryToolsRequest['rules'][index]));
		}
	}
	simplifiedRequest['rules'] = tabClause;
	result = stringifyJSONRequest(simplifiedRequest);

	return result;
}

function query_tools_clause_into_SQLRequest2(queryToolsRequest){

	var simplifiedRequest = {};
	var result = '';
	var tabClause = [];
	var clause = {};
	var index = 0;

	if (queryToolsRequest['rules'].length >= 2){
		simplifiedRequest['condition'] = queryToolsRequest['condition'];
	}else{
		simplifiedRequest['condition'] = "null";
	}
	for(index = 0; index < queryToolsRequest['rules'].length; index++){
		if (typeof queryToolsRequest['rules'][index]['id'] !== 'undefined'){
			clause = {};
			clause['id'] = queryToolsRequest['rules'][index]['id'];
			clause['operator'] = queryToolsRequest['rules'][index]['operator'];
			clause['value'] = queryToolsRequest['rules'][index]['value'];
			tabClause.push(clause);
		}
		if(queryToolsRequest['rules'][index]['rules']){
			tabClause.push(get_query_tools_clause(queryToolsRequest['rules'][index]));
		}
	}
	simplifiedRequest['rules'] = tabClause;
	result = stringifyJSONRequest2(simplifiedRequest);

	return result;
}

function get_query_tools_clause(queryToolsRequest){
	var request = {};
	var tabClause = [];
	var clause = {};
	var index = 0;

	if (queryToolsRequest['rules'].length >= 2){
		request['condition'] = queryToolsRequest['condition'];
	}else{
		request['condition'] = "null";
	}
	for(index = 0; index < queryToolsRequest['rules'].length; index++){
		if (typeof queryToolsRequest['rules'][index]['id'] !== 'undefined'){
			clause = {};
			clause['id'] = queryToolsRequest['rules'][index]['id'];
			clause['operator'] = queryToolsRequest['rules'][index]['operator'];
			clause['value'] = queryToolsRequest['rules'][index]['value'];
			tabClause.push(clause);
		}
		if(queryToolsRequest['rules'][index]['rules']){
			tabClause.push(get_query_tools_clause(queryToolsRequest['rules'][index]));
		}
	}
	request['rules'] = tabClause;

	return request;
}

function searchJSONRequest(target){
	var globalJSONData = JSON.parse(localStorage.getItem("globalJSON"));
	var length_userJSONData = globalJSONData.length;
	var request = {};
	var index = 0;
	var target_found = false;
	while ((target_found === false) && (index < length_userJSONData) ){
		if (target === globalJSONData[index]['query_tool_name']){
			request = JSON.parse(globalJSONData[index]['query_tool_json']);
			target_found = true;
		}
		index = index + 1 ;
	}

	localStorage.setItem("selectedLoadQuery", JSON.stringify(request));

	return request;
}

function stringifyJSONRequest(request){
	var result = '';
	if (request !== ''){
		// pattern composite
		var operator = JSON.parse(localStorage.getItem("operator[]"));
		var actualColumn = request['rules'][0]['id'].split(/_(.+)/)[0];
		var actualIndex = '';
		var type_column = <?php echo json_encode($this->data['type_column']); ?>;
		var name_column = <?php echo json_encode($this->data['sql_table_index_belong_to']); ?>;
		var from_column = <?php echo json_encode($this->data['sql']); ?>;
		var select_result = '';
		var from_result = '';
		var index = 0;
		var i = 0;

		for (index = 0; index < from_column[actualColumn]['select'].length ; index ++){
			select_result = select_result + from_column[actualColumn]['select'][index];
		}
		for (index = 0; index < from_column[actualColumn]['from'].length ; index ++){
			from_result = from_result + from_column[actualColumn]['from'][index];
		}
		// result = result + select_result;
		// result = result + from_result;
		// result = result + "WHERE";

		result = "I want all " + request['rules'][0]['id'].split(/_(.+)/)[0] + " where";
		for(index=0;index < request['rules'].length; index++){
			if(request['rules'][index]['rules']){
				if (request['condition'] !== null && index != 0){
					result = result + ' ' + request['condition'];
				}
				result = result + JSONRequestRules(request['rules'][index], name_column, type_column);
			}
			else{
				if (request['condition'] !== null && index != 0){
					result = result + ' ' + request['condition'];
				}
				result = result + stringifyJSONRules(request['rules'][index], name_column, type_column);
			}
		}
	}
	return result;
}

function stringifyJSONRequest2(request){
	var result = '';
	if (request !== ''){
		// pattern composite
		var operator = JSON.parse(localStorage.getItem("operator[]"));
		var actualColumn = request['rules'][0]['id'].split(/_(.+)/)[0];
		var actualIndex = '';
		var type_column = <?php echo json_encode($this->data['type_column']); ?>;
		var name_column = <?php echo json_encode($this->data['sql_table_index_belong_to']); ?>;
		var from_column = <?php echo json_encode($this->data['sql']); ?>;
		var select_result = '';
		var from_result = '';
		var index = 0;
		var i = 0;

		for (index = 0; index < from_column[actualColumn]['select'].length ; index ++){
			select_result = select_result + from_column[actualColumn]['select'][index];
		}
		for (index = 0; index < from_column[actualColumn]['from'].length ; index ++){
			from_result = from_result + from_column[actualColumn]['from'][index];
		}
		result = result + select_result;
		result = result + from_result;
		result = result + "WHERE";

		// result = "I want all " + request['rules'][0]['id'].split(/_(.+)/)[0] + " where";
		for(index=0;index < request['rules'].length; index++){
			if(request['rules'][index]['rules']){
				if (request['condition'] !== null && index != 0){
					result = result + ' ' + request['condition'];
				}
				result = result + JSONRequestRules2(request['rules'][index], name_column, type_column);
			}
			else{
				if (request['condition'] !== null && index != 0){
					result = result + ' ' + request['condition'];
				}
				result = result + stringifyJSONRules2(request['rules'][index], name_column, type_column);
			}
		}
	}
	return result;
}

function JSONRequestRules(request, name_column, type_column){
	var index=0;
	var result = '';
	for(index=0;index < request['rules'].length; index++){
		if(request['rules'][index]['rules']){
			if (request['condition'] !== null && index != 0){
				result = result + ' ' + request['condition'];
			}
			result = result + JSONRequestRules(request['rules'][index], name_column, type_column);
		}else{
			if (request['condition'] !== null && index != 0){
				result = result + ' ' + request['condition'];
			}
			result = result + stringifyJSONRules(request['rules'][index], name_column, type_column);
		}
	}

	return result;
}

function JSONRequestRules2(request, name_column, type_column){
	var index=0;
	var result = '';
	for(index=0;index < request['rules'].length; index++){
		if(request['rules'][index]['rules']){
			if (request['condition'] !== null && index != 0){
				result = result + ' ' + request['condition'];
			}
			result = result + JSONRequestRules2(request['rules'][index], name_column, type_column);
		}else{
			if (request['condition'] !== null && index != 0){
				result = result + ' ' + request['condition'];
			}
			result = result + stringifyJSONRules2(request['rules'][index], name_column, type_column);
		}
	}

	return result;
}

function stringifyJSONRules(rules, name_column, type_column){
	var index = 0;
	var result = '';
	var operator = JSON.parse(localStorage.getItem("operator[]"));
	var data_column_name = <?php echo json_encode($this->data['sql_table_name']); ?>;
	var actualColumn = rules['id'].split(/_(.+)/)[0];
	var actualIndex = '';

	actualIndex = rules['id'].split(/_(.+)/)[1];
	// result = result + ' ' + data_column_name[name_column[actualColumn][actualIndex]] + '.' + actualIndex;
	result = result + ' ' + actualIndex;
	result = result +  ' ' + operator[rules['operator']];

	if (rules['value'] !== null){
		if ((typeof rules['value'].length !== 'undefined') && (type_column[actualColumn][actualIndex] === 'integer')){
			for (i=0;i<rules['value'].length-1;i++){
				result = result +  ' ' + rules['value'][i] + ' AND';
			}
			result = result +  ' ' + rules['value'][rules['value'].length-1];
		}
		else{
			result = result +  ' ' + rules['value'];
		}
	}

	return result;
}

function stringifyJSONRules2(rules, name_column, type_column){
	var index = 0;
	var result = '';
	var operator = JSON.parse(localStorage.getItem("operator[]"));
	var data_column_name = <?php echo json_encode($this->data['sql_table_name']); ?>;
	var actualColumn = rules['id'].split(/_(.+)/)[0];
	var actualIndex = '';

	actualIndex = rules['id'].split(/_(.+)/)[1];
	result = result + ' ' + data_column_name[name_column[actualColumn][actualIndex]] + '.' + actualIndex;
	// result = result + ' ' + actualIndex;
	result = result +  ' ' + operator[rules['operator']];

	if (rules['value'] !== null){
		if ((typeof rules['value'].length !== 'undefined') && (type_column[actualColumn][actualIndex] === 'integer')){
			for (i=0;i<rules['value'].length-1;i++){
				result = result +  ' ' + rules['value'][i] + ' AND';
			}
			result = result +  ' ' + rules['value'][rules['value'].length-1];
		}
		else{
			result = result +  " '" + rules['value'] +"'";
		}
	}

	return result;
}

function fill_query_tools_text_area(text){
	var textArea = document.getElementById("query_textarea");
	textArea.value = text;
}


///


function load_query_tools_dictionnary_table(){

	var dictionnary_table = [];
	var tmp_table = <?php echo json_encode($this->data['table']['available']); ?>;
	var len = tmp_table.length;
	var i = 0;

	for (i = 0; i < len ; i++){
		dictionnary_table.push(tmp_table[i]);
	}

	return dictionnary_table;

}

function load_query_tools_dictionnary_column(){

	var dictionnary_table = load_query_tools_dictionnary_table();
	var dictionnary_column = {};
	var table_len = dictionnary_table.length;

	var allIndexOfProject = <?php echo json_encode($_SESSION['indexProject']); ?>;
	var indexOfProject = '';
	var index_len = 0;

	for (i = 0; i < table_len ; i++){
		index_len = allIndexOfProject[dictionnary_table[i]].length;
		dictionnary_column[dictionnary_table[i]] = [];

		for (j = 0; j < index_len ; j++){
			dictionnary_column[dictionnary_table[i]].push(allIndexOfProject[dictionnary_table[i]][j]);
		}
	}

	return dictionnary_column;

}

function initialize_builder_filters(allDictionnary){
	var filterResult = [];

	var filterOptions = [];
	var filterOptions_values = {};

	var dataOfProject = [];
	var index = 0;
	var i = 0;
	var name_column = [];
	var type_column = <?php echo json_encode($this->data['type_column']); ?>;

	var allDataOfProject = <?php echo json_encode($this->data['dataProject']); ?>;
	var len_data = allDataOfProject.length;
	var tabProject = <?php echo json_encode($this->data['project']['available']); ?>;
	var noProject = <?php echo json_encode($this->data['sql_table_need_project']); ?>;
	var len_tabProject = tabProject.length;

	for (var actualColumn in allDictionnary[0]) {
		filterOptions_values = {};
		dataOfProject = [];
		name_column = [];
		//obtention des données détaillé de la 3eme colonnes
		if (noProject[actualColumn] == "false"){
			dataOfProject = allDataOfProject[actualColumn]['project']["noProjectNeeded"];
		}
		else{
			for(index=0;index<len_tabProject;index++){
				for(i=0;i<allDataOfProject[actualColumn]['project'][tabProject[index]].length; i++){
					dataOfProject.push(allDataOfProject[actualColumn]['project'][tabProject[index]][i]);
				}

			}
			//elimination des duplicata possible entre projets
			dataOfProject = uniqueObject(dataOfProject);
		}

		for(index=0;index<allDictionnary[0][actualColumn].length;index++){
			name_column[index] = allDictionnary[0][actualColumn][index];
			filterOptions_values[name_column[index]] = [];
		}
		len_data = dataOfProject.length;
		//rempli les colonnes avec les données
		for(index=0;index<len_data;index++){
			for(i=0;i<name_column.length;i++){
				filterOptions_values[name_column[i]].push(dataOfProject[index][name_column[i]]);
			}
		}

		// enleve les duplicata présent dans chaques colonnes
		for(index=0;index<name_column.length;index++){
			filterOptions_values[name_column[index]] = unique(filterOptions_values[name_column[index]]);
			if ($.isNumeric(filterOptions_values[name_column[index]][0])){
				filterOptions_values[name_column[index]].sort(function(a, b){return a - b});
			}else{
				filterOptions_values[name_column[index]].sort();
			}
		}
		//obtention des données des colonnes recherchable
		for(index=0;index<allDictionnary[0][actualColumn].length;index++){

			filterOptions = {};
			name_column = allDictionnary[0][actualColumn][index];
			filterOptions["id"] = actualColumn+"_"+ name_column;
			filterOptions["label"] = actualColumn+" "+ name_column;

			if(type_column[actualColumn][name_column] == "integer"){
				filterOptions["type"] = "integer";
			}else if(type_column[actualColumn][name_column] == "date"){
				filterOptions["type"] = "date";
			}else{
				filterOptions["type"] = "string";
			}
			filterOptions["plugin"] = "selectize";
			filterOptions["input"]	= "select";

			if(filterOptions["type"] == "integer" || filterOptions["type"] == "date"){
				filterOptions["operators"] = ['equal', 'not_equal', 'in', 'not_in', 'less', 'less_or_equal', 'greater', 'greater_or_equal',
				'between', 'not_between', 'is_null', 'is_not_null'];
			}else{
				filterOptions["operators"] = ['equal', 'not_equal', 'in', 'not_in', 'begins_with', 'not_begins_with',
				'contains', 'not_contains', 'ends_with', 'not_ends_with', 'is_empty', 'is_not_empty', 'is_null', 'is_not_null'];
			}

			filterOptions["values"] = filterOptions_values[name_column];
			filterResult.push(filterOptions);
		}
	}

	return filterResult;
}

function initialize_operator_meaning(){

	var tabOperator = {};

	tabOperator['equal'] = '=';
	tabOperator['not_equal'] = '!=';
	tabOperator['in'] = 'IN';
	tabOperator['not_in'] = 'NOT IN';
	tabOperator['less'] = '<';
	tabOperator['less_or_equal'] = '<=';
	tabOperator['greater'] = '>';
	tabOperator['greater_or_equal'] = '>=';
	tabOperator['between'] = 'BETWEEN';
	tabOperator['not_between'] = 'NOT BETWEEN';
	tabOperator['is_null'] = 'IS NULL';
	tabOperator['is_not_null'] = 'IS NOT NULL';
	tabOperator['begins_with'] = 'LIKE';
	tabOperator['not_begins_with'] = 'LIKE';
	tabOperator['contains'] = 'LIKE';
	tabOperator['not_contains'] = 'LIKE';
	tabOperator['ends_with'] = 'LIKE';
	tabOperator['not_ends_with'] = 'LIKE';
	tabOperator['is_empty'] = "= ''";
	tabOperator['is_not_empty'] = "!= ''";

	localStorage.setItem("operator[]", JSON.stringify(tabOperator));

}


function deleteContent(content){
	if(content.childNodes.length > 0){
		content.innerHTML = '';
	}
}

function delete_query_tool_tabs(){
	var boxLoadResult = document.getElementById("dataViewSaveOrLoadBox");
	if (boxLoadResult !== null){
		boxLoadResult.remove();
	}
}

function unique(list) {
	var result = [];
	$.each(list, function(i, e) {
		if ($.inArray(e, result) == -1) result.push(e);
	});
	return result;
}

function uniqueObject(list){
	var result = [];
	var i = 0;
	$.each(list, function(i, e) {
		if ($.inArray(JSON.stringify(e), result) == -1) result.push(JSON.stringify(e));
	});

	result = list_string_to_list_object(result);

	return result;
}

function list_string_to_list_object(list){
	var result = [];
	var i = 0;

	for(i=0;i< list.length; i++){
		result.push(JSON.parse(list[i]));
	}
	return result;
}

function remove(array, element) {
	return array.filter(e => e !== element);
}

</script>

<style>

#builder .form-control{
	min-width:25px;
	max-width:500px;
}

.myDataViewMenu{
	display:inline;
	padding:5px;
}

hr{
	display: block;
	height: 1px;
	border: 0;
	border-top: 1px solid #ccc;
	margin: 1em 0;
	padding: 0;
}

</style>
