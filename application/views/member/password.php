<div class="fullwidth-block">
  <div class="container">
    <div class="contact-form">
      <form class="" action="#" method="post">


        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="alert alert-danger alert-dismissible fade in" <?php if($update_status == TRUE or $update_status == NULL) echo "hidden"; ?>>
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <span class="glyphicon glyphicon-alert"></span>
              Une erreur s'est produite pendant la transmission des données du formulaire.
              Le changement de mot de passe n'a pas été effectué!
            </div>
            <div class="alert alert-success alert-dismissible fade in" <?php if($update_status == FALSE) echo "hidden"; ?>>
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <span class="glyphicon glyphicon-ok"></span>
              Le changement de mot de passe a été effectué avec succés!
            </div>

            <div class="boxed-content">
              <h2 class="section-title"><span class="glyphicon glyphicon-lock"></span>Mot de passe</h2>
              <p>
                <label for="mdp">Mot de passe actuel</label>
                <input id="mdp" type="password" name="mdp" placeholder="Mot de passe actuel...">
              </p>
              <?php
                if(form_error('mdp') != NULL)
                echo '<div class="alert alert-danger" role="alert">'.form_error('mdp'). '</div>';
              ?>
              <p>
                <label for="new_mdp">Nouveau mot de passe</label>
                <input id="new_mdp" type="password" name="new_mdp" placeholder="Nouveau mot de passe...">
              </p>
              <?php
                if(form_error('new_mdp') != NULL)
                echo '<div class="alert alert-danger" role="alert">'.form_error('new_mdp'). '</div>';
              ?>
              <p>
                <label for="mdp_conf">Confirmation du nouveau mot de passe</label>
                <input id="mdp_conf" type="password" name="mdp_conf" placeholder="Confirmation du nouveau mot de passe...">
              </p>
              <?php
                if(form_error('mdp_conf') != NULL)
                echo '<div class="alert alert-danger" role="alert">'.form_error('mdp_conf'). '</div>';
              ?>
            </div>
          </div>
        </div>

        <div class="row">
					<div class='col-md-10'>
						<p class="text-right">
              <button type="button" class="back" onclick="window.location.href = '<?php echo site_url("member"); ?>';" name="button">Retour</button>
							<input type="submit" value="Sauvegarder le mot de passe">
						</p>
					</div>
				</div>

      </form>
    </div>
  </div>
</div>
