	<select class="selectpicker" title="Show/Hide Index" id="selectpickerIndex" data-actions-box="true" data-width="250px" data-live-search="true" data-selected-text-format="static" multiple>
	</select>

<script>

$(function() {

	$("#selectpickerIndex").on('loaded.bs.select', function (e){
		$("#selectpickerIndex").selectpicker('ALL');
	});
	
	$("#selectpickerIndex").selectpicker({
		selectAllText: 'Show All Index',
		deselectAllText: 'Hide All Index'
	});
	
	$("#selectpickerIndex").selectpicker('render');
	
	$("#selectIndex  .bs-select-all").on('click', function() {
		localStorage.setItem("oldSelectedIndex", $("#selectpickerIndex").val());
		updateIndex("ALL", true);
	});
	
	$("#selectIndex .bs-deselect-all").on('click', function() {
		localStorage.setItem("oldSelectedIndex", $("#selectpickerIndex").val());
		updateIndex("ALL", false);
	});
	
	$(function () {
	  $("#selectpickerIndex").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
		var target = $(this).find('option').eq(clickedIndex).text();
		selectedIndex = $("#selectpickerIndex").val();
		if (selectedIndex !== null){
			localStorage.setItem("selectedIndex[]",selectedIndex);
		}else{
			localStorage.setItem("selectedIndex[]", null);
		}
		if (target != ""){
			updateIndex(target, newValue);
		}		
		localStorage.setItem("oldSelectedIndex", "null");
	  });
	});

});

</script>