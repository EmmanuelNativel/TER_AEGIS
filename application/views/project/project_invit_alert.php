<div class="alert alert-info">
  <h3><span class="glyphicon flaticon-group"></span>Invitation</h3>
  <p>
    Désirez-vous rejoindre les membres du projet <?php echo $project_code ?>.
  </p><br>
  <a href="<?php echo site_url('projects/accept/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Rejoindre</a>
  <a href="<?php echo site_url('projects/decline/'.$project_code); ?>" class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Refuser</a>
</div>
