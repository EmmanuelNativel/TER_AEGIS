<div class="fullwidth-block">
	<div class="container">
		 <div class="contact-form">
			<form id='main-form' action="<?php echo current_url() ?>" method='post'>
				<div class="row">

					<div class="col-md-2"></div>
					<div class="col-md-4">
						<div class="boxed-content">
							<legend>Identifiant</legend>
							<p><input type="text" id="pseudo" name="pseudo" oninput="verifie_pseudo()" placeholder="Identifiant..." value="<?php echo set_value('pseudo'); ?>"></p>
							<span id="user-availability-status"></span>
							<?php
								if(form_error('pseudo') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('pseudo'). '</div>';
							?>
							<p><input type="password" name="mdp" placeholder="Mot de passe..."></p>
							<?php
								if(form_error('mdp') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('mdp'). '</div>';
							?>
							<p><input type="password" name="mdp_conf" placeholder="Confirmation du mot de passe..."></p>
							<?php
								if(form_error('mdp_conf') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('mdp_conf'). '</div>';
							?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="boxed-content">
							<legend>Coordonnées</legend>
							<p><input type="text" name="first_name" placeholder="Prénom..." value="<?php echo set_value('first_name'); ?>"></p>
							<?php
								if(form_error('first_name') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('first_name'). '</div>';
							?>
							<p><input type="text" name="last_name" placeholder="Nom..." value="<?php echo set_value('last_name'); ?>"></p>
							<?php
								if(form_error('last_name') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('last_name'). '</div>';
							?>
							<p>
								<select id="select-organization" name="organization" placeholder="Organisation... (facultatif)" data-initial-value=<?php echo '"'.set_value('organization').'"'; ?>>
									<option value=""></option>
									<?php
										foreach ($list_partner as $partner) {
											echo '<option value="'.$partner['partner_code'].'">'.$partner['partner_name']."</option>";
										}
									?>
								</select>
							</p>
							<?php
								if(form_error('organization') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('organization'). '</div>';
							?>
							<p><input type="email" name="email" placeholder="E-mail..." value="<?php echo set_value('email'); ?>"></p>
							<?php
								if(form_error('email') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('email'). '</div>';
							?>
							<?php
								if(form_error('connexion') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('connexion'). '</div>';
							?>
						</div>
					</div>
					<div class="col-md-2"></div>
				</div>

				<div class="row">
					<div class='col-md-10'>
						<p class="text-right">
							<input type="submit" value="S'inscrire">
						</p>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
