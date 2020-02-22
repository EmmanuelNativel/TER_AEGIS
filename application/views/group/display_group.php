<div class="fullwidth-block">
  <div class="container">

    <div class="col-md-8 col-md-offset-2">
      <h2 class="section-title"><span class="glyphicon flaticon-group"></span><?= $group_name ?></h2>
      <div class="boxed-content">
        <dl class="dl-horizontal">
          <dt>Nom</dt>
          <dd><?= $group_name ?></dd>
          <dt>Description</dt>
          <dd><?= $group_description ?></dd>
        </dl>
      </div>
    </div>

    <div class="col-md-8 col-md-offset-2">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
          <a href="#members" aria-controls="members" role="tab" data-toggle="tab">
             Utilisateurs associés <span class="badge badge-inverse"><?= count($members) ?></span>
          </a>
        </li>

      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active fade in active" id="members">
          <div class="table-responsive">
            <?php

              $template = array('table_open' => '<table class="table table-hover">');
              $this->table->set_template($template);
              $this->table->set_heading(array('Identifiant', 'Nom'));

              $table_data = array();

              foreach ($members as $member) {

                ob_start();
                if ($member['is_leader'] == PGSQL_TRUE): ?>
                <span data-toggle="tooltip" title="Gestionnaire du groupe" class="glyphicon flaticon-fashion"></span>
              <?php else: ?>
                <span data-toggle="tooltip" title="Membre" class="glyphicon glyphicon-user"></span>
              <?php endif;
              $status_icon = ob_get_clean();

                array_push($table_data, array(
                  $member['login'],
                  $member['first_name'].' '.$member['last_name']
                  )
                );

              }

              echo $this->table->generate($table_data); //génere le tableau html
            ?>
          </div>
          <div class="text-right">
            <a href="#" class="button" data-toggle="modal" data-target="#addMemberModal"><span class="glyphicon glyphicon-plus"></span>Ajouter</a>
          </div>
        </div>
      </div>

      <!-- buttons -->
      <hr>
      <div class="text-left">
        <a href="<?= site_url('groups/'); ?>" class="button"><span class="glyphicon glyphicon-menu-left"></span>Mes groupes</a>
      </div>
      <!-- end buttons -->
    </div>

  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="addMemberModalLabel"><span class="glyphicon glyphicon-user"></span>Lier un utilisateur au groupe <?= $group_name ?></h4>
      </div>
      <?= form_open('groups/add_member/'.$group_id, 'class="contact-form"'); ?>
      <div class="modal-body">
        <div class="form-group">
          <p>
            Quel utilisateur voulez-vous lier à ce groupe?
          </p>
          <label class="sr-only" for="user">Utilisateur</label>
          <select class="selectpicker" id="user" data-url="groups/searched_users/<?= $group_id ?>" data-width="100%" name="user" data-live-search="true" data-title="Utilisateur">
            <?php if(set_value('user')) echo '<option value="'.set_value('user').'" selected>'.set_value('user').'</option>'; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
        <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Ajouter</button>
      </div>
      <?= form_close(); ?>
    </div>
  </div>
</div>
