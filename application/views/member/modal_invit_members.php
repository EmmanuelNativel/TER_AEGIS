<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Nouveau membre(s) de l'équipe</h4>
      </div>
      <div class="modal-body">
        <?php echo form_open(current_url()); ?>
        <p>
          <select class="selectpicker" multiple id="select_users" data-width="100%" data-actions-box="true" data-live-search="true" data-size="5" name="select_users[]" title="Membres...">
            <?php
              foreach ($list_users as $user) {
                var_dump($user);
                echo '<option data-subtext="'.$user['first_name'].' '.$user['last_name'].' '.$user['organization'].'" value='.$user['login'].'>
                        '.$user['login'].'
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
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="submit" value='add_members' class="btn btn-primary">Inviter</button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
