<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
            <li class="active">Accessions</li>
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
            $this->table->set_heading(array('Code accession','Type accession', 'Pays d\'origine'));

          $table_data = array();
          foreach ($accessions as $accession) {
                 array_push($table_data,
                     array(
                         '<a href="' . site_url('accessions/display/' . $accession['accession_code']) . '" >' . $accession['accession_code'] . '</a>',
                     $accession['accession_type'],
                     $accession['seed_origin_country']


                     ));
            }
            echo $this->table->generate($table_data);?>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?php  echo $this->pagination->create_links(); ?>
            </div>
            <div class="col-xs-6 text-right">
                <a href="<?php echo site_url('accessions/create'); ?>" class="button"><span class="glyphicon glyphicon-plus"></span>Nouveau</a>
            </div>
        </div>
    </div>
</div>


