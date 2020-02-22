<div class="fullwidth-block">
  <div class="container">
    <div class="contact-form">
      <form action="#" method="post">

        <div class="col-md-2"></div>
        <div class="col-md-8">

          <div class="row">
            <h2 class="section-title"><span class="glyphicon flaticon-group"></span><?php echo $group_name; ?></h2>
          </div>

          <div class="row">
            <div class="boxed-content">
          <?php
          switch ($user_status) {
            case 'guest':
            echo '<p class="text-center">Vous avez reçu une invitation à rejoindre ce groupe.</p>';
            echo '<form action="#" method="post">
                    <p class="text-center">
                      <button type="submit" value="yes" name="invit-answer"><span class="glyphicon glyphicon-ok"></span>Accepter</button>
                      <button type="submit" value="no" name="invit-answer"><span class="glyphicon glyphicon-remove"></span>Décliner</button>
                    </p>
                  </form>';
              break;

            case 'member':
              echo '<p class="text-center">Vous faites parti de ce groupe.</p>';
              break;
            case 'candidate':
              echo '<p class="text-center">Votre demande de participation à ce groupe est en cours de validation.</p>';
              break;

            default:
            echo '<p class="text-center">Vous pouvez demander à rejoindre ce groupe.</p>';
            echo '<form action="#" method="post">
                    <p class="text-center">
                      <button type="submit" value="asked" name="team-request"><span class="glyphicon glyphicon-comment"></span>Demander</button>
                    </p>
                  </form>';
              break;
          }
          ?>
          </div></div>

          <div class="row">
            <div class="boxed-content">
              <p>
                <label for="name">Nom du groupe</label><p id='name'><?php echo $group_name; ?></p>
              </p>
              <p>
                <label for="description">Description du groupe</label><p id='description'><?php echo $group_description; ?></p>
              </p>
            </div>
          </div>

          <div class="row">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Liste des membres du groupe</h3>
              </div>
              <div class="panel-body">
                <div class="db_table">
                  <?php echo $members_table_html; ?>
                </div>
                <?php
                  if ($is_group_admin) {
                    echo '<p class="text-right">
                            <button type="button" class="btn btn-icon" data-backdrop="static" data-toggle="modal" data-target="#myModal">
                              <span class="glyphicon glyphicon-plus"></span>
                            </button>
                          </p>';
                  }
                 ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

    <?php
    if ($is_group_admin) {
      $this->load->view('member/modal_invit_members', $_POST);
    }
    ?>

  </div>
</div>
