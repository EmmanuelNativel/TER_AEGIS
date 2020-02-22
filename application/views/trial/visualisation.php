<div class="fullwidth-block">
	<div class="container">
		<div class="row">
			<div id="dataviz-container" class="col-lg-6 col-xs-12, highcharts-container" height="600px">
				<svg id="dataviz" class="center-block" height="600px">
				</svg>
			</div>

			<div class="col-lg-6 col-xs-12">
				<form action="" id="var_form">
					<div class="form-group">
						<!-- <label class="sr-only" for="choosed_level">Niveau</label>
						<select name="choosed_level" id="obs_variable">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
						</select> -->
					</div>
					<div class="form-group">
						<label class="sr-only" for="choosed_var">Variable</label>
						<select name="choosed_var" id="obs_variable">
							<?php
							foreach ($variables as $var) {
								echo '<option value="'.$var['obs_variable'].'">'.$var['obs_variable'].'</option>';
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<input type="submit">
					</div>
				</form>

				<div id="info">
				</div>
			</div>
		</div>
	</div>

</div>
<script src="https://d3js.org/d3.v5.min.js"></script>

