<div class="fullwidth-block">
  <div class="container">
    <h2 class="section-title"><?php echo ucfirst($project_name); ?></h2>

    <div class="boxed-content">
      <div class="pull-right">
        <span title="Projet en attente de validation" class="label label-warning">Non vérifié</span>
      </div>
      <dl class="dl-horizontal">
        <dt>Code projet</dt>
        <dd><a href="#"><?php echo $project_code; ?></a></dd>
        <dt>Nom</dt>
        <dd><?php echo ucfirst($project_name); ?></dd>
        <dt>Description</dt>
        <dd><?php echo ucfirst($project_resume); ?></dd>
        <dt>Responsable(s)</dt>
        <dd><?php echo ucfirst($coordinator); ?></dd>
        <dt>Organisation</dt>
        <dd><a href="<?php echo site_url('partners/display/'.$coord_company); ?>"><?php echo $coord_company; ?></a></dd>
      </dl>
    </div>

    <div class="alert alert-warning">
      <h3><span class="glyphicon glyphicon-warning-sign"></span>Projet non verifié</h3>
      <p>
        Ce projet est en attente de validation par un administrateur.
      </p><br>
      <a href="<?php echo site_url('projects/valid/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Valider</a>
      <a href="<?php echo site_url('projects/reject/'.$project_code); ?>" class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Refuser</a>
    </div>

    <div class="alert alert-info">
      <h3><span class="glyphicon flaticon-group"></span>Invitation</h3>
      <p>
        @member vous invite à rejoindre les membres du projet <?php echo $project_code ?>.
      </p><br>
      <a href="<?php echo site_url('projects/valid/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Valider</a>
      <a href="<?php echo site_url('projects/reject/'.$project_code); ?>" class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Refuser</a>
    </div>

    <!-- buttons -->
    <div class="row">
      <div class="col-xs-6 text-left">
        <a href="<?php echo site_url('projects/'); ?>" class="button"><span class="glyphicon glyphicon-menu-left"></span>Tous les projets</a>
      </div>
      <div class="col-xs-6 text-right">
        <a href="<?php echo site_url('projects/update/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-pencil"></span>Modifier</a>
        <a href="<?php echo site_url('projects/delete/'.$project_code); ?>" class="button button-danger"><span class="glyphicon glyphicon-trash"></span>Supprimer</a>
      </div>
    </div>
    <!-- end buttons -->

  </div>
</div>
