<select class="selectpicker" title="My Request" id="selectpickerQueryToolLoadIndividualJSON" data-actions-box="true" data-width="350px" data-live-search="true">
	</select>

<select class="selectpicker" title="Popular Request" id="selectpickerQueryToolLoadPopularJSON" data-actions-box="true" data-width="350px" data-live-search="true">
	</select>
	
<button class="btn btn-primary parse-json" data-target="basic" id="btn_load_query_submit" onClick="load_query_submit_button();return false;">Submit</button>
<button class="btn btn-warning parse-json" data-target="basic" id="btn_load_query_cancel" onClick="query_cancel_button();return false;">Cancel</button>

<script>
$(function() {

	$("#selectpickerQueryToolLoadIndividualJSON").on('loaded.bs.select', function (e){
		$("#selectpickerQueryToolLoadIndividualJSON").selectpicker('ALL');
	});
	
	$("#selectpickerQueryToolLoadPopularJSON").on('loaded.bs.select', function (e){
		$("#selectpickerQueryToolLoadPopularJSON").selectpicker('ALL');
	});
	
	$("#selectpickerQueryToolLoadIndividualJSON").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
		$("#selectpickerQueryToolLoadPopularJSON").val('default');
		$("#selectpickerQueryToolLoadPopularJSON").selectpicker('refresh');
		var target = $(this).find('option').eq(clickedIndex).text();
		var request = searchJSONRequest(target);
		var simplifiedRequest = query_tools_clause_into_SQLRequest(request);
		fill_query_tools_text_area(simplifiedRequest);
	});
	
	$("#selectpickerQueryToolLoadPopularJSON").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
		$("#selectpickerQueryToolLoadIndividualJSON").val('default');
		$("#selectpickerQueryToolLoadIndividualJSON").selectpicker('refresh');
		var target = $(this).find('option').eq(clickedIndex).text();
		var request = searchJSONRequest(target);
		var simplifiedRequest = query_tools_clause_into_SQLRequest(request);
		fill_query_tools_text_area(simplifiedRequest);
	});
	
	$("#selectpickerQueryToolLoadIndividualJSON").selectpicker('render');
	$("#selectpickerQueryToolLoadPopularJSON").selectpicker('render');
	
});

</script>