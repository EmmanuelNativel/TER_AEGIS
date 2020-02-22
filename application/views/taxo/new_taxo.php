<div class="fullwidth-block">
    <div class="container">

        <?= form_open('taxos/create/', 'class="contact-form"'); ?>

        <div class="col-md-8 col-md-offset-2">
            <?= validation_errors(); ?>
            <h2 class="section-title">Nouveau taxo</h2>

            <div class="form-group">
                <label class="sr-only" for="taxo_code">Nom</label>
                <input id="code" class="form-control" type="text" name="code" value="<?= set_value('code') ?>" required="true" placeholder="Code taxo...">
            </div>

            <div class="form-group">
                <label class="sr-only" for="taxo_name">Nom</label>
                <input id="name" class="form-control" type="text" name="name" value="<?= set_value('name') ?>" placeholder="Nom taxonomique...">
            </div>

            <div class="form-group">
                <label class="sr-only" for="level_taxo">Rang taxonomique</label>
                <input id="level_taxo" class="form-control" type="text" name="level_taxo" value="<?= set_value('level_taxo') ?>" placeholder="Rang taxonomique...">
            </div>

            <div class="form-group">
                <label class="sr-only" for="id_parent">Taxo parent</label>
                <input id="parent" class="form-control" type="text" name="parent" value="<?= set_value('parent') ?>" placeholder="Taxo parent...">
            </div>

            <div class="row">
                <div class="col-xs-6 text-left">
                    <a href="<?= site_url('accessions/create') ?>" class="button"><span class="glyphicon glyphicon-chevron-left"></span>Retour</a>
                </div>
                <div class="col-xs-6 text-right">
                    <input type="submit" class="button" value="Créer">
                </div>
            </div>

        </div>

        <?= form_close(); ?>

    </div>
</div>
