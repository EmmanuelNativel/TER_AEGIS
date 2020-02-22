<div class="fullwidth-block">
  <div class="container">
    <ol class="breadcrumb">
      <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
      <li class="active">Projets</li>
    </ol>
    <?php
      $attributes = array('class' => 'form-contact');
      form_open('#', $attributes);
    ?>

    <div class="row">
      <div class="col-xs-4 text-left">
        <?= $limit ?> élément(s) par page
      </div>
      <div class="col-xs-4 text-center">
        <?= $this->pagination->create_links(); ?>
      </div>
      <div class="col-xs-4 text-right">
        Nombre total d'élément(s) <?= $total_rows ?>
      </div>
    </div>

    <?php form_close(); ?>

    <div class="table-responsive">
      <?php
      $template = array('table_open' => '<table class="table table-hover">');
      $this->table->set_template($template);
      $this->table->set_heading(array('Code projet', 'Nom', 'Description', 'Responsable(s)', 'Société affiliée'));

      $table_data = array();

      foreach ($projects as $project) {
        //$status_label = '';
        //if ($project['is_validated'] == PGSQL_FALSE) {
        //  $status_label = '<span class="label label-warning">Non vérifié</span>';
        //}
        array_push($table_data, array(
          //$status_label.
          '<a href="'.site_url('projects/display/'.$project['project_code']).'">'.$project['project_code'].'</a>',
          $project['project_name'],
          $project['project_resume'],
          $project['coordinator'],
          $project['coord_company']
        ));
      }
      echo $this->table->generate($table_data); //génere le tableau html
      ?>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <?php  echo $this->pagination->create_links(); ?>
      </div>
      <div class="col-xs-6 text-right">
        <a href="<?php echo site_url('projects/create'); ?>" class="button"><span class="glyphicon glyphicon-plus"></span>Demande de création</a>
      </div>
    </div>
  </div>
</div>
