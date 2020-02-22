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
        $this->table->set_heading(array('Groupe', 'Description'));

        $table_data = array();

        foreach ($groups as $group) {
					$link_name = '<a href="'.site_url('groups/display/'.$group['group_name']).'">'.$group['group_name'].'</a>';
          array_push($table_data, array(
						$link_name,
						$group['group_description']
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
				<a href="<?php echo site_url('groups/create'); ?>" class="button"><span class="glyphicon glyphicon-plus"></span>Nouveau</a>
			</div>
		</div>
  </div>
</div>
