<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
            <li class="active">Nouveau site</li>
        </ol>
        <h2 class="section-title">Nouveau site</h2>
        <?php echo form_open('sites/create', 'class="contact-form"'); ?>
        <?php echo validation_errors() ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="sr-only" for="site_code">Code site</label>
                    <input id="site_code" class="form-control" type="text" name="site_code" value="<?php echo set_value('site_code'); ?>" placeholder="Code site...(maug_dia)">
                </div>

                <div class="form-group">
                    <label class="sr-only" for="site_name">Nom site</label>
                    <input id="site_name" class="form-control" type="text" name="site_name" value="<?php echo set_value('site_name'); ?>" placeholder="Nom de site...(mauguio_diaphen)">
                </div>

                <div class="form-group">
                    <label class="sr-only" for="select_country">Pays</label>
                    <select class="selectpicker" id="select_country" data-width="100%" data-size="5" name="select_country" data-live-search="true" data-title="Pays...(sélectionner un pays)">
                        <?php foreach ($countrys as $country): ?>
                            <option <?php if($select_country == $country['country_code'])
                                echo 'selected'; ?> value="<?= $country['country_code'] ?>">
                                <?= $country['country'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="form-group">
                        <label class="sr-only" for="site_lat">Latitude de site</label>
                        <input id="site_lat" class="form-control" type="text" name="site_lat" value="<?php echo set_value('site_lat'); ?>" placeholder="Latitude de site...(43.650124)">
                    </div>

                    <div class="form-group">
                        <label class="sr-only" for="site_long">Longitude de site</label>
                        <input id="site_long" class="form-control" type="text" name="site_long" value="<?php echo set_value('site_long'); ?>" placeholder="Longitude de site...(3.869162)">
                    </div>

                    <div class="form-group">
                        <label class="sr-only" for="site_alt">Altitude de site</label>
                        <input id="site_alt" class="form-control" type="text" name="site_alt" value="<?php echo set_value('site_alt'); ?>" placeholder="Altitude de site...">
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right">
            <input type="submit" name="submit" value="Créer">
        </div>
        <?php echo form_close(); ?>
    </div>
</div>


