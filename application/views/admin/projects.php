<?php $this->load->view('admin/nav_menu'); ?>

<div class="container"> <!-- Start Admin_page -->

	<div class="row">

		<div class="col-lg-4">
			<div class="filter-options btn-group">
				<button class="btn btn--warning" data-group="project">Projets</button>
				<button class="btn btn--warning" data-group="request">Demandes de Projet</button>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="input-group">
			 <input type="text" class="form-control js-shuffle-search" placeholder="Recherche...">
			 <span class="input-group-btn">
				 <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
			 </span>
			</div>
		</div>

		<div class="col-lg-4">
					<span>Ordre : </span>
					<select class="sort-options selectpicker">
						<option value="">Defaut</option>
					  <option value="alphanumeric">Alphanumerique</option>
					</select>
		</div>

	</div>

	<div class="fullwidth-block">
		<div id="grid" class="row-fluid">
			<?php
				foreach ($figure_list as $figure) {
					echo $figure;
				}
			?>
		</div>
	</div>

</div> <!-- End Admin_page -->
