<div class="fullwidth-block">
	<div class="container">

		 <div class="row contact-form">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="boxed-content">
					<form action="<?php echo current_url() ?>" method='post'>
							<p><input type="text" name="pseudo" placeholder="Identifiant..." value="<?php echo set_value('pseudo'); ?>"></p>
							<?php
								if(form_error('pseudo') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('pseudo'). '</div>';
							?>
							<p><input type="password" name="mdp" placeholder="Mot de passe..." value="<?php echo set_value('mdp'); ?>"></p>
							<?php
								if(form_error('mdp') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('mdp'). '</div>';
							?>

							<p class="text-right">
								<input type="submit" value="Connexion">
							</p>
					</form>
				</div>
				<div class="well text-center">
					Nouveau sur AEGIS? <a href="<?php echo site_url('welcome/signin') ?>">Créer un compte</a>.
				</div>
			</div>
			<div class="col-md-4"></div>

		 </div>
	</div>
</div>
