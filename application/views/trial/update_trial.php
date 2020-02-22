<div class="fullwidth-block">
    <div class="container">
        <h2 class="section-title">Modification d'un essai <?= $trial_code ?></h2>

        <?= form_open('trials/update/' . $trial_code, 'class="contact-form"'); ?>
        <?= validation_errors(); ?>

        <div class="boxed-content">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="" for="site_code">Lieu</label>
                        <input id="site_code" class="form-control" type="text" name="site_code"
                               value="<?= html_escape(set_value('site_code') ? set_value('site_code') : $site_code); ?>"
                               placeholder="Lieu...">
                    </div>

                    <div class="form-group">
                        <label class="" for="trial_description">Description</label>
                        <input id="description" class="form-control" type="text" name="description"
                               value="<?= html_escape(set_value('description') ? set_value('description') : $trial_description); ?>"
                               placeholder="Description...">
                    </div>

                    <div class="form-group">
                        <div id="datepicker" class="input-group input-daterane">
                            <label class="sr-only" for="starting_date">Date de début de l'essai</label>
                            <input type="text" id="starting_date" name="starting_date" class="form-control" value="<?= html_escape(set_value('starting_date') ? set_value('starting_date') : $starting_date); ?>">
                            <span class="input-group-addon">à</span>
                            <label class="sr-only" for="ending_date">Date de fin de l'essai</label>
                            <input type="text" id="ending_date" name="ending_date" class="form-control" value="<?= html_escape(set_value('ending_date') ? set_value('ending_date') : $ending_date); ?>">
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <p>
                            <span class="glyphicon glyphicon-warning-sign"></span>
                            <strong>Attention</strong>, la date doit prendre ce format (yyyy-mm-dd).
                        </p>
                    </div>
                </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="" for="commentary">Commentaires</label>
                            <textarea class="form-control" name="commentary" id="commentary" rows=2
                                      placeholder="Commentaires..."><?= set_value('commentary') ? set_value('commentary') : $commentary; ?></textarea>
                        </div>
                    </div>
                </div>


            </div>

            <div class="text-right">
                <button type="button" onclick="location.href='<?= site_url('trials/display/' . $trial_code) ?>';"
                        class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Annuler
                </button>
                <button type="submit" class="button"><span class="glyphicon glyphicon-ok"></span>Modifier</button>
            </div>

            <?= form_close(); ?>
        </div>
    </div>
