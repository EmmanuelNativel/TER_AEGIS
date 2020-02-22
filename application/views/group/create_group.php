<div class="fullwidth-block">
  <div class="container">
    <div class="contact-form">
      <form action="#" method="post">

        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">

            <div class="boxed-content">
              <h2 class="section-title"><span class="glyphicon flaticon-group"></span>Créer une équipe</h2>
              <p>
                <label for="name">Nom</label>
                <input id="name" type="text" name="name" value="<?php echo $name; ?>">
              </p>
              <?php
								if(form_error('name') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('name'). '</div>';
							?>
              <p>
                <label for="description">Description</label>
                <input id="description" type="text" name="description" value="<?php echo $description; ?>">
              </p>
              <?php
								if(form_error('description') != NULL)
								echo '<div class="alert alert-danger" role="alert">'.form_error('description'). '</div>';
							?>
              <p>
                <label for="select_users">Membres de l'équipe</label>
                <select multiple id="select_users" name="select_users[]" placeholder="Membres...">
                  <option value=""></option>
                  <?php
                    foreach ($list_users as $user) {
                      if (in_array($user['login'], $team_members)) $status = 'selected';
                      else $status = '';
                      echo '<option '.$status.' value='.$user['login'].'>
                              '.$user['login'].' | '.$user['first_name'].' '.$user['last_name'].' | '.$user['organization'].'
                            </option>';
                    }
                  ?>
                </select>
              </p>
              <?php
                if(form_error('select_users') != NULL)
                echo '<div class="alert alert-danger" role="alert">'.form_error('select_users'). '</div>';
              ?>
            </div>
          </div>
        </div>

        <div class="row">
					<div class='col-md-10'>
            <p class="text-right">
							  <button type="submit" name="submit-btn"><span class="glyphicon glyphicon-plus"></span>Ajouter l'équipe</button>
						</p>
					</div>
				</div>

      </form>
    </div>
  </div>
</div>
