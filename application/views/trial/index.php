<div class="fullwidth-block">
    <div class="container">
        <?php
        $attributes = array(
            'class' => 'form-contact'
        );
        form_open('#', $attributes); ?>

        <div class="row">
            <div class="col-xs-6 col-md-6 text-left">
                <?php echo $limit ?> élément(s) par page
            </div>
            <div class="col-xs-6 col-md-6 text-right">
                Nombre total d'élément(s) <?php echo $total_rows ?>
            </div>
        </div>

        <?php form_close(); ?>

        <div class="table-responsive">
            <?php

            $template = array('table_open' => '<table class="table table-hover">');
            $this->table->set_template($template);
            $this->table->set_heading(array('Essai', 'Date de début', 'Date de fin', 'Projet', 'Géolocalisation'));

            $table_data = array();

            foreach ($trials as $trial) {
                // if ($trial["trial_lat"] != null && $trial["trial_long"] != null) {
                //   $geo_link = '<a href="https://www.google.fr/maps/place/'. $trial["trial_lat"] .','.$trial["trial_long"].'" target="_blank"><span class="glyphicon glyphicon-map-marker"></span></a>'.$trial['site_code'];
                // } else {
                //   $geo_link = '<span class="glyphicon glyphicon-map-marker"></span>'.$trial['site_code'];
                // }
                array_push($table_data, array(  $trial['trial_code'],
                    nice_date($trial['starting_date'], 'd/m/Y'),
                    nice_date($trial['ending_date'], 'd/m/Y'),
                    $trial['project_code'],
                    $trial['site_code']
                ));

            }

            echo $this->table->generate($table_data); //génere le tableau html
            ?>
        </div>
        <p>
            <?php  echo $this->pagination->create_links(); ?>
        </p>
    </div>
</div>
