<div class="alert alert-warning">
  <h3><span class="glyphicon glyphicon-warning-sign"></span>Projet non verifié</h3>
  <p>
    Ce projet est en attente de validation par un administrateur.
  </p><br>
  <a href="<?php echo site_url('projects/valid/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Valider</a>
  <a href="<?php echo site_url('projects/reject/'.$project_code); ?>" class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Refuser</a>
</div>
