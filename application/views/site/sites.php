<div class="fullwidth-block">
  <div class="container">
      <ol class="breadcrumb">
          <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
          <li class="active">Localisation</li>
      </ol>
    <?php
    $attributes = array(
      'class' => 'form-contact'
    );
    form_open('#', $attributes); ?>

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
      $this->table->set_heading(array('Goole map','Code site', 'Nom', 'Pays'));

      $table_data = array();

      foreach ($sites as $site) {
        if ($site['site_lat'] != null && $site["site_long"] != null) {
          $geo_link = '<a href="https://www.google.fr/maps/place/'. $site['site_lat'] .','.$site["site_long"].'" target="_blank"><span class="glyphicon glyphicon-map-marker"></span>Géolocalisé</a>';
        } else {
          $geo_link = '<span class="glyphicon glyphicon-map-marker"></span>';
        }
        array_push($table_data,
        array(
          $geo_link,
          $site['site_code'],
          $site['site_name'],
          $site['country'] // TODO: Transform country code to country name
          )
        );
      }
  echo $this->table->generate($table_data); //génere le tableau html
  ?>
</div>
      <div class="row">
          <div class="col-xs-6">
              <?php  echo $this->pagination->create_links(); ?>
          </div>
          <div class="col-xs-6 text-right">
              <a href="<?php echo site_url('sites/create'); ?>" class="button"><span class="glyphicon glyphicon-plus"></span>Nouveau</a>
          </div>
      </div>
</div>
</div>
