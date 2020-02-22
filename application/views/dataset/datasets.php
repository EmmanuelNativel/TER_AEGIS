<div class="fullwidth-block">
	<div class="container">
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
        $this->table->set_heading(array('Jeu de données', 'Type', 'Visibilité', 'Accés', 'Crée par'));

        $table_data = array();

        foreach ($datasets as $dataset) {
					$link_name = '<a href="'.site_url('datasets/display/'.$dataset['dataset_id']).'">'.$dataset['dataset_name'].'</a>';

          switch ($dataset['visibility']) {
            case VISIBILITY_PRIVATE:
              $visibility = '<span class="label label-danger"><span class="glyphicon glyphicon-lock"></span> Privé</span>';
              break;

            case VISIBILITY_CUSTOM:
              $visibility = '<span class="label label-warning"><span class="glyphicon glyphicon-cog"></span> Personnalisé</span>';
              break;

            case VISIBILITY_PUBLIC:
              $visibility = '<span class="label label-info"><span class="glyphicon flaticon-world"></span> Publique</span>';
              break;
          }

          $access = 'Écriture';

          if (isset($dataset['permissions'])) {
            if ($dataset['permissions'] == ACCESS_WRITE) $access = 'Écriture';
            else $access = 'Lecture';
          }

          array_push($table_data, array(
						$link_name,
						$dataset['dataset_type'],
            $visibility,
            $access,
            $dataset['dataset_owner_login']
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
				<a href="<?php echo site_url('datasets/create'); ?>" class="button"><span class="glyphicon glyphicon-plus"></span>Nouveau</a>
			</div>
		</div>
