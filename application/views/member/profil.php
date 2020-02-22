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
              Les changements n'ont pas été effectués!
            </div>
            <div class="alert alert-success alert-dismissible fade in" <?php if($update_status == FALSE) echo "hidden"; ?>>
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <span class="glyphicon glyphicon-ok"></span>
              Les changements demandés ont été effectués avec succés!
            </div>

            <div class="boxed-content">
              <h2 class="section-title"><span class="glyphicon glyphicon-user"></span><?php echo $user_infos['username']; ?></h2>
              <p>
                <label for="first_name">Prénom</label>
                <input id="first_name" type="text" name="first_name" value="<?php echo $user_infos['first_name']; ?>">
              </p>
              <?php
								if(form_error('first_name') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('first_name'). '</div>';
							?>
              <p>
                <label for="last_name">Nom</label>
                <input id="last_name" type="text" name="last_name" value="<?php echo $user_infos['last_name']; ?>">
              </p>
              <?php
								if(form_error('last_name') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('last_name'). '</div>';
							?>
              <p>
                <label for="email">E-mail</label>
                <input id="email" type="text" name="email" value="<?php echo $user_infos['email']; ?>">
              </p>
              <?php
								if(form_error('email') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('email'). '</div>';
							?>
              <hr>
              <p>
                <a href="<?php echo site_url("member/password"); ?>"><span class="glyphicon glyphicon-lock"></span>Changer de mot de passe</a>
              </p>
            </div>
          </div>
        </div>

        <div class="row">
					<div class='col-md-10'>
						<p class="text-right">
							<input type="submit" value="Sauvegarder les changements">
						</p>
					</div>
				</div>

      </form>
    </div>
  </div>
</div>
