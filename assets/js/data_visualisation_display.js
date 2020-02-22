$(document).ready(function() {

	var form = $('#var_form');

	$(form).submit(function(event) {
		// Stop the browser from submitting the form.
		// console.log($('select[name=choosed_level]').val());
		// console.log($('select[name=choosed_var]').val());
		event.preventDefault();
		var formData = {
			'choosed_level': $('select[name=choosed_level]').val(),
			'choosed_var': $('select[name=choosed_var]').val()
		};
		//console.log(formData);

		// Submit the form using AJAX.
		$.ajax({
		  dataType: "json",
		  //url: 'http://localhost/daphne-master/index.php/trials/get_trial_obs/2016_WP4_Parents_G2MARS_BCNAM_Diaphen',
		  url: window.location.origin + '/index.php/trials/get_trial_obs/2016_WP4_Parents_G2MARS_BCNAM_Diaphen',
		  type : 'POST',
		  data : formData,
		  success: function(data){
		  	observations = data;
		  	make_drawable_map();
		  	draw_map();

			infoBox = $("#info");
			infoBox.html("");
			infoBox.append('<div><strong>Variable observée : </strong>' + formData.choosed_var + '</div>');
			infoBox.append("Cliquez sur une parcelle pour avoir le détail");
		  	d3.selectAll("rect")

		  }
		});
	});
})

function draw_map(){

	var data = drawable_data;
	var values = data.map(box=>box.average);
  	var domain = [Math.min(...values), Math.max(...values)];
	
	var ordon = data.map(box=>box.y);
	var min_ord = Math.min(...ordon);
	var co_ordon = ordon.map(co=>co-min_ord);
	var corrected = 500/Math.max(...co_ordon);	


	var svgContainer = d3.select("#dataviz");

	svgContainer.selectAll("rect")
		.remove()

	svgContainer.append("rect")
		.attr("x", 0)
		.attr("y", 0);

	svgContainer.selectAll("rect")
	  .data(data)
	  .enter()
	  	.append("g")
	  	.attr("id", function(d){return d.x + "," + d.y})
			.append("rect")
		    .attr("x", function(d){return parseInt((d.x*50))})
		    .attr("y", function(d){return parseInt((d.y-min_ord)*corrected)})
		    .attr("width", corrected-(corrected/10))
			.attr("height", corrected-(corrected/10))
			.style("stroke", "black")
			.style("fill", function(d){return choose_color(d, domain)})
		.on("click", function(d) {
			svgContainer.selectAll("rect").attr("stroke-width", 1);
			d3.select(this).attr("stroke-width", 4);
	        infoBox.append('<div id="filles"></div>');
	        var fillesBox = $("#filles");
			fillesBox.html("");
	        fillesBox.append("<strong>observations filles :</strong>");
	        d.obs_group.forEach(function(obs){
	        	fillesBox.append("<p>observation : " + obs.id +
	        		"<br>valeur : " + obs.value + "</p>");
	        });
		})
		.on("mouseover", handleMouseOver)
		.on("mouseout", handleMouseOut);
}

function get_mouse_coords(event) {

	var coordinates = [0, 0];
	coordinates = d3.mouse(d3.select("#dataviz").node());
	var x = coordinates[0];
	var y = coordinates[1];

	return {x, y};
}

function handleMouseOver(d, i) {  // Add interactivity

	// Use D3 to select element, change color and size
	var coords = get_mouse_coords(event);
	console.log(coords);
	var values = d.obs_group;
	d3.select(this)
		.attr('fill-opacity', .4);
		console.log(d);
	d3.select("#dataviz-container")
		.append("div")
		.attr("class", "MyChartTooltip")
		.attr("id", "t" + d.x + "-" + d.y + "-" + i)
		.attr("style", 'top:' + coords.y + 'px ; left:' + coords.x + 'px ;')
		.html(function() {
			var info = "";
			values.forEach(function(obs){
				info = info + "observation : " + obs.id + " || valeur : " + obs.value + "<br>"
			});
			return info;
		});
}

function handleMouseOut(d, i, event) {
	// Use D3 to select element, change color back to normal
	
	d3.select(this).attr('fill-opacity', 1);

	// Select text by id and then remove
	d3.selectAll("#t" + d.x + "-" + d.y + "-" + i).remove();  // Remove text location
}