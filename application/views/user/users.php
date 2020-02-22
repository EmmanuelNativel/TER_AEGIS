<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
            <li class="active">Membres</li>
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
            $this->table->set_heading(array('Identifiant','Prenom', 'Nom'));

            $table_data = array();
            foreach ($users as $pseudo) {
                array_push($table_data,
                    array(
                        '<a href="' . site_url('users/display/' . $pseudo['login']) . '" >' . $pseudo['login'] . '</a>',
                    $pseudo['first_name'],
                    $pseudo['last_name'],
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

