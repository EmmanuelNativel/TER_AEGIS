<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
            <li class="active">Essais</li>
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
            $this->table->set_heading(array('Essai', 'Date de début', 'Date de fin', 'Géolocalisation'));

            $table_data = array();

            foreach ($trials as $trial) {

                array_push($table_data,
                    array(
                        '<a href="' . site_url('trials/display/' . $trial['trial_code']) . '" >' . $trial['trial_code'] . '</a>',
                        nice_date($trial['starting_date'], 'd/m/Y'),
                        nice_date($trial['ending_date'], 'd/m/Y'),
                        $trial['site_code']

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
                <a href="<?php echo site_url('trials/create'); ?>" class="button"><span class="glyphicon glyphicon-plus"></span>Nouveau</a>
            </div>
        </div>
    </div>
</div>
