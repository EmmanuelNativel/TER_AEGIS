<div class="fullwidth-block">
    <div class="container">
        <h2 class="section-title">Modification d'une accession <?= $accession_code ?></h2>

        <?= form_open('accessions/update/' . $accession_code, 'class="contact-form"'); ?>
        <?= validation_errors(); ?>

        <div class="boxed-content">
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="" for="accession_mother">Code accession</label>
                        <input id="accession_code" class="form-control" type="text" name="accession_code" value="<?= html_escape(set_value('accession_code') ? set_value('accession_mother') : $accession_code); ?>" placeholder="Accession mère...">
                    </div>

                    <div class="form-group">
                        <label class="" for="accession_mother">Accession mère</label>
                        <input id="accession_mother" class="form-control" type="text" name="accession_mother" value="<?= html_escape(set_value('accession_mother') ? set_value('accession_mother') : $accession_mother); ?>" placeholder="Accession mère...">
                    </div>

                    <div class="form-group">
                        <label class="" for="accession_father">accession père</label>
                        <input id="accession_father" class="form-control" type="text" name="accession_father" value="<?= html_escape(set_value('accession_father') ? set_value('accession_father') : $accession_father); ?>" placeholder="Accession père...">
                    </div>

                    <div class="form-group">
                        <label class="" for="accession_type">Type accession</label>
                        <input id="accession_type" class="form-control" type="text" name="accession_type" value="<?= html_escape(set_value('accession_type') ? set_value('accession_type') : $accession_type); ?>" placeholder="Type accession...">
                    </div>

                    <div class="form-group">
                        <label class="" for="seed_production_date">Date de production</label>
                        <input id="seed_production_date" class="form-control" type="text" name="seed_production_date"
                               value="<?= html_escape(set_value('seed_production_date') ? set_value('seed_production_date') : $seed_production_date); ?>" placeholder="Date de production...">
                    </div>

                    <div class="form-group">
                        <label class="" for="genetic_pool">Pool génétique</label>
                        <input id="genetic_pool" class="form-control" type="text" name="genetic_pool" value="<?= html_escape(set_value('genetic_pool') ? set_value('genetic_pool') : $genetic_pool); ?>" placeholder="Pool génétique...">
                    </div>

                    <div class="form-group">
                        <label class="" for="donor_code">Code du donateur</label>
                        <input id="donor_code" class="form-control" type="text" name="donor_code" value="<?= html_escape(set_value('donor_code') ? set_value('donor_code') : $donor_code); ?>" placeholder="Code du donateur...">
                    </div>

                    <div class="form-group">
                        <label class="" for="seed_production_date">Site de production</label>
                        <input id="site_code" class="form-control" type="text" name="site_code" value="<?= html_escape(set_value('site_code') ? set_value('site_code') : $seed_production_site); ?>" placeholder="Site de production...">
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group">
                            <label class="" for="ecotype">Ecotype</label>
                            <input id="ecotype" class="form-control" type="text" name="ecotype" value="<?= html_escape(set_value('ecotype') ? set_value('ecotype') : $ecotype); ?>" placeholder="Ecotype...">
                        </div>

                        <div class="form-group">
                            <label class="" for="agrosystem">Agrosystème</label>
                            <input id="agrosystem" class="form-control" type="text" name="agrosystem" value="<?= html_escape(set_value('agrosystem') ? set_value('agrosystem') : $agrosystem); ?>" placeholder="Agrosystème...">
                        </div>

                        <div class="form-group">
                            <label class="" for="vernacular_name">Nom vernaculaire</label>
                            <input id="vernacular_name" class="form-control" type="text" name="vernacular_name" value="<?= html_escape(set_value('vernacular_name') ? set_value('vernacular_name') : $vernacular_name); ?>" placeholder="Nom vernaculaire...">
                        </div>

                        <div class="form-group">
                            <label class="" for="biological_status">Statut biologique</label>
                            <input id="biological_status" class="form-control" type="text" name="biological_status" value="<?= html_escape(set_value('biological_status') ? set_value('biological_status') : $biological_status); ?>" placeholder="Statut biologique...">
                        </div>

                        <div class="form-group">
                            <label class="" for="lat_origin">Latitude</label>
                            <input id="lat_origin" class="form-control" type="text" name="lat_origin" value="<?= html_escape(set_value('lat_origin') ? set_value('lat_origin') : $lat_origin); ?>" placeholder="Latitude...">
                        </div>

                        <div class="form-group">
                            <label class="" for="long_origin">Longitude</label>
                            <input id="long_origin" class="form-control" type="text" name="long_origin" value="<?= html_escape(set_value('long_origin') ? set_value('long_origin') : $long_origin); ?>" placeholder="Longitude...">
                        </div>

                        <div class="form-group">
                            <label class="" for="region">Région d'origine</label>
                            <input id="region" class="form-control" type="text" name="region" value="<?= html_escape(set_value('region') ? set_value('region') : $region); ?>" placeholder="Région d'origine...">
                        </div>

                        <div class="form-group">
                            <label class="" for="city">Ville d'origine</label>
                            <input id="city" class="form-control" type="text" name="city" value="<?= html_escape(set_value('city') ? set_value('city') : $city); ?>" placeholder="Ville d'origine...">
                        </div>

                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="button" onclick="location.href='<?= site_url('accessions/display/' . $accession_code) ?>';"
                        class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Annuler
                </button>
                <button type="submit" class="button"><span class="glyphicon glyphicon-ok"></span>Modifier</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>




