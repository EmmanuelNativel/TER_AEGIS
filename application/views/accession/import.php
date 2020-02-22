<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Importation</li>
        </ol>

        <?= form_open_multipart('accessions/import', 'class="contact-form"'); ?>
        <div class="col-md-12">
            <div class="col-md-5">
                <div class="boxed-content">
                    <h2 class="section-title"><strong>1.</strong> Télécharger le formulaire</h2>
                    <div class="text-center">
                        <a href="<?= site_url('accessions/download_form'); ?>" class="button btn-lg only-icon"
                           role="button"><span class="glyphicon glyphicon-floppy-disk"></span> .XLS</a>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="boxed-content">
                    <h2 class="section-title"><strong>2.</strong> Remplir le formulaire</h2>
                    <div class="alert alert-info">
                        <p>
                            <span class="glyphicon glyphicon-question-sign"></span>
                            Veuillez verifier, avant l'importation des données, si "taxo_code" existe dans la feuille "Taxo".
                            Si ce n'est pas le cas, il faut procéder à sa création via l'interface web, puis importer les données.<br><br>
                            Pour plus de détails veuillez consulter la feuille "lexicon"
                        </p>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="boxed-content">
                    <h2 class="section-title"><strong>3.</strong> Envoyer le formulaire</h2>

                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group has-feedback">
                                <input type="file" name="access_file" size="20"/>
                                <span class="glyphicon glyphicon-exclamation-sign form-control-feedback text-danger"
                                      aria-hidden="true"></span>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <input type="submit" name="submit" value="Importer"/>
                            </div>
                        </div>
                    </div>
                    <?= (isset($error)) ? $error : NULL; ?>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
