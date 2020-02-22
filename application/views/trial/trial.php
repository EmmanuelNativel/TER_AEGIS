<div class="fullwidth-block">
  <div class="container">
    <h2 class="section-title">Création d'un nouvel essai</h2>

    <?php echo form_open('trials/create', 'class="contact-form"'); ?>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="sr-only" for="trial_code">Trial code</label>
          <input id="trial_code" class="form-control" type="text" name="trial_code" value="<?php echo set_value('trial_code'); ?>" placeholder="Code essai... (trial_code: 2014_WP3_Diaphen)">
        </div>
        <?php if(form_error('trial_code')) echo form_error('trial_code'); ?>

        <div class="form-group">
          <label class="sr-only" for="description">Description</label>
          <input id="description" class="form-control" type="text" name="description" placeholder="Description... (max: 255 carcatères)">
        </div>
        <?php if(form_error('description')) echo form_error('description'); ?>

        <div class="form-group">
          <div id="datepicker" class="input-group input-daterange">
            <label class="sr-only" for="starting_date">Date de début de l'essai</label>
            <input type="text" id="starting_date" name="starting_date" class="form-control" value="<?php echo mdate('%Y-%m-%d', time()); ?>">
            <span class="input-group-addon">à</span>
            <label class="sr-only" for="ending_date">Date de fin de l'essai</label>
            <input type="text" id="ending_date" name="ending_date" class="form-control" value="<?php echo mdate('%Y-%m-%d', strtotime('+1 week')); ?>">
          </div>
        </div>
        <?php if(form_error('starting_date')) echo form_error('starting_date'); ?>
        <?php if(form_error('ending_date')) echo form_error('ending_date'); ?>

      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-xs-10 col-md-10">
            <div class="form-group">
              <label class="sr-only" for="project_code">Projet</label>
              <select class="selectpicker" id="project_code" data-url="data_import/existing_project" data-width="100%" name="project_code" data-live-search="true" data-title="Projet... (project_code)">
              </select>
            </div>
            <?php if(form_error('project_code')) echo form_error('project_code'); ?>
          </div>
          <div class="col-xs-2 col-md-1">
            <a href="#" title="Nouveau projet" class="button"><span class="glyphicon glyphicon-plus no-padding"></span></a>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-10 col-md-10">
            <div class="form-group">
              <label class="sr-only" for="site_code">Site code</label>
              <select class="selectpicker" id="site_code" data-url="data_import/existing_site" data-width="100%" name="site_code" data-live-search="true" data-title="Lieu... (site_code)">
              </select>
            </div>
            <?php if(form_error('site_code')) echo form_error('site_code'); ?>
          </div>
          <div class="col-xs-2 col-md-1">
            <a href="#" title="Nouveau lieu" class="button"><span class="glyphicon glyphicon-plus no-padding"></span></a>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-10 col-md-10">
            <div class="form-group">
              <label class="sr-only" for="soil_code">Soil code</label>
              <select class="selectpicker" id="soil_code" data-url="data_import/existing_soil" data-width="100%" name="soil_code" data-live-search="true" data-title="Sol... (soil_code)">
              </select>
            </div>
            <?php if(form_error('soil_code')) echo form_error('soil_code'); ?>
          </div>
          <div class="col-xs-2 col-md-1">
            <a href="#" title="Nouveau sol" class="button"><span class="glyphicon glyphicon-plus no-padding"></span></a>
          </div>
        </div>
      </div>

    </div>

    <div class="row">
      <div class="col-md-6">

        <div class="form-group">
          <label for="checkbox_env">Evironnement controllé : </label>
          <input id="checkbox_env" type="checkbox" value="TRUE" name="checkbox_env"/><label for="checkbox_env"><span class="ui"></span></label>
        </div>
        <?php if(form_error('checkbox_env')) echo form_error('checkbox_env'); ?>

        <div class="form-group">
          <label class="sr-only" for="root_depth">Profondeur des racines</label>
          <input id="root_depth" class="form-control" type="text" name="root_depth" placeholder="Profondeur des racines... (en cm)">
        </div>
        <?php if(form_error('root_depth')) echo form_error('root_depth'); ?>

        <div class="form-group">
          <label class="sr-only" for="nb_unit_levels">Nombre de niveaux hiérarchiques</label>
          <input id="nb_unit_levels" class="form-control" type="text" name="nb_unit_levels" placeholder="Nombre de niveaux hiérarchiques...">
        </div>
        <?php if(form_error('nb_unit_levels')) echo form_error('nb_unit_levels'); ?>

      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="sr-only" for="commentary">Commentaire</label>
          <textarea id="commentary" name="commentary" class="form-control" rows="5" placeholder="Commentaire... (max: 2000 caractères)"></textarea>
        </div>
        <?php if(form_error('commentary')) echo form_error('commentary'); ?>
      </div>
    </div>

    <div class="text-right">
      <input type="submit" name="submit" value="Créer">
    </div>

    <?php echo form_close(); ?>

  </div>
</div>

<script type="text/javascript">
$(window).load(function() {
  // UI Calendar
  $('#datepicker').datepicker({
    format: "yyyy-mm-dd",
    language: "fr"
  });

  // Loading
  $('form').submit(function(event) {
    $('input[type=submit]').val("Chargement...");
    $(document).off("click");
  });

});
</script>
