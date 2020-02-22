<div class="fullwidth-block">
	<div class="container">

		<div class="row contact-form">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="boxed-content">
					<form action="<?php echo current_url() ?>" method='post'>
						<p>
							<input type="text" name="partner_name" placeholder="Nom..." value="<?php echo set_value('partner_name'); ?>">
						</p>
						<?php
						if(form_error('partner_name') != NULL)
						echo '<div class="alert alert-danger" role="alert">'.form_error('partner_name'). '</div>';
						?>
						<p>
							<input type="text" name="adress" placeholder="Adresse..." value="<?php echo set_value('adress'); ?>">
						</p>
						<?php
						if(form_error('adress') != NULL)
						echo '<div class="alert alert-danger" role="alert">'.form_error('adress'). '</div>';
						?>
						<p>
							<input type="text" name="zip_code" placeholder="Code Postal..." value="<?php echo set_value('zip_code'); ?>">
						</p>
						<?php
						if(form_error('zip_code') != NULL)
						echo '<div class="alert alert-danger" role="alert">'.form_error('zip_code'). '</div>';
						?>
						<p>
							<input type="text" name="city" placeholder="Ville..." value="<?php echo set_value('city'); ?>">
						</p>
						<?php
						if(form_error('city') != NULL)
						echo '<div class="alert alert-danger" role="alert">'.form_error('city'). '</div>';
						?>
						<p>
							<select id="select_country" name="select_country" placeholder="Pays...">
								<option value=""></option>
								<?php
									foreach ($countrys as $country) {
										echo '<option value='.$country['country_code'].'>'.$country['country']."</option>";
									}
								?>
							</select>
						</p>
						<?php
							if(form_error('select_country') != NULL)
							echo '<div class="alert alert-danger" role="alert">'.form_error('select_country'). '</div>';
						?>

						<p class="text-right">
							  <button type="submit" name="submit-btn"><span class="glyphicon glyphicon-plus"></span>Ajouter l'Organisme</button>
						</p>
					</form>
				</div>
			</div>
			<div class="col-md-4"></div>

		</div>
	</div>
</div>
