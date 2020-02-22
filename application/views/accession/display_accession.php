<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li class="active"><a href="<?= site_url('accessions/display/') ?>"><?= 'Code accession : ', $accession_code ?></a></li>
        </ol>
        <div class="boxed-content">
            <dl class="dl-horizontal">
                <dt>Code accession:</dt>
                <dd><?= $accession_code?></dd>
                <dt>Type accession:</dt>
                <dd><?= $accession_type?></dd>
                <dt>Site de production:</dt>
                <dd><?= $seed_production_site?></dd>
                <dt>Date de production:</dt>
                <dd><?= $seed_production_date?></dd>
               <!-- <dt>Institut de production:</dt>
                <dd><?/*= $seed_institut_producer*/?></dd>-->
                <dt>Pays de production:</dt>
                <dd><?= $seed_origin_country?></dd>
                <dt>Accession pere :</dt>
                <dd><?= $accession_father?></dd>
                <dt>Accession mère :</dt>
                <dd><?= $accession_mother?></dd>
                <dt>Genetic pool:</dt>
                <dd><?= $genetic_pool?></dd>
                <dt>Code de donneur:</dt>
                <dd><?= $donor_code?></dd>
                <dt>Isogenic_of:</dt>
                <dd><?= $isogenic_of?></dd>
                <dt>Ecotype:</dt>
                <dd><?= $ecotype?></dd>
                <dt>Agrosystème:</dt>
                <dd><?= $agrosystem?></dd>
                <dt>Nom vernaculaire:</dt>
                <dd><?= $vernacular_name?></dd>
                <dt>Statut biologique:</dt>
                <dd><?= $biological_status?></dd>
                <dt>latitude:</dt>
                <dd><?= $lat_origin?></dd>
                <dt>Longitude:</dt>
                <dd><?= $long_origin?></dd>
                <dt>Région d'origine:</dt>
                <dd><?= $region?></dd>
                <dt>Ville d'origine:</dt>
                <dd><?= $city?></dd>
            </dl>

            <div class="text-right">
                    <a href="<?= site_url('accessions/update/'.$accession_code); ?>" class="button"><span class="glyphicon glyphicon-pencil"></span>Modifier</a>
                <?php if ($this->session->userdata('admin')|| $is_leader): ?>
                    <a href="#" class="button button-danger" data-toggle="modal" data-target="#deleteModal"><span class="glyphicon glyphicon-trash"></span>Supprimer</a>
                <?php endif; ?>
            </div>
        </div>

        <hr>
        <div class="text-left">
          <a href="<?= site_url('accessions/index/') ?>" class="button"><span class="glyphicon glyphicon-chevron-left"></span>Toutes les accessions</a>
        </div>
    </div>
</div>
<?php if ($this->session->userdata('admin')|| $is_leader): ?>

<!-- DELETE Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="deleteModalLabel"><span class="glyphicon glyphicon-trash"></span>Supprimer
            </h4>
        </div>
        <?= form_open('accessions/delete', 'class="contact-form"'); ?>
        <input type="text" name="accession_code" value="<?= $accession_code ?>" hidden="true">
        <div class="modal-body">
            <p>
                Voulez-vous vraiment supprimer cette accession ?
            </p>
            <div class="modal-footer">
                <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler
                </button>
                <button type="submit" class="button button" name="button"><span
                            class="glyphicon glyphicon-ok"></span>Je supprime cette accession</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>







