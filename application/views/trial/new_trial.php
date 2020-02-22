<div class="fullwidth-block">
  <div class="container">
    <h2 class="section-title">Création d'un nouvel essai</h2>


    <?php echo form_open('trials/create', 'class="contact-form"'); ?>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="sr-only" for="trial_code">Trial code</label>
          <input id="trial_code" class="form-control" type="text" name="trial_code" value="<?= set_value('trial_code'); ?>" placeholder="Code essai... (trial_code: 2014_WP3_Diaphen)">
        </div>
        <?php if(form_error('trial_code')) echo form_error('trial_code'); ?>

        <div class="form-group">
          <label class="sr-only" for="description">Description</label>
          <input id="description" class="form-control" type="text" name="description" value="<?= set_value('description'); ?>" placeholder="Description... (max: 255 carcatères)">
        </div>
        <?php if(form_error('description')) echo form_error('description'); ?>

      </div>

      <div class="col-md-6">
        <div class="row">
          <div class="col-xs-10 col-md-12">
              <div class="form-group">
                  <div id="datepicker" class="input-group input-daterange">
                      <label class="sr-only" for="starting_date">Date de début de l'essai</label>
                      <input type="text" id="starting_date" name="starting_date" class="form-control" value="<?php if(set_value('starting_date')) echo set_value('starting_date'); else echo mdate('%Y-%m-%d', time()); ?>">
                      <span class="input-group-addon">à</span>
                      <label class="sr-only" for="ending_date">Date de fin de l'essai</label>
                      <input type="text" id="ending_date" name="ending_date" class="form-control" value="<?php if(set_value('ending_date')) echo set_value('ending_date'); else echo mdate('%Y-%m-%d', strtotime('+1 week')); ?>">
                  </div>
              </div>
              <?php if(form_error('starting_date')) echo form_error('starting_date'); ?>
              <?php if(form_error('ending_date')) echo form_error('ending_date'); ?>
          </div>

        </div>

        <div class="row">
          <div class="col-xs-10 col-md-9">
              <div class="form-group">
                  <label class="sr-only" for="site_code">Site code</label>
                  <select class="selectpicker" id="site_code" data-url="sites/searched_options" data-width="100%" name="site_code" data-live-search="true" data-title="Lieu... (site_code)">
                      <?php if(set_value('site_code')) echo '<option value="'.set_value('site_code').'" selected>'.set_value('site_code').'</option>'; ?>
                  </select>
              </div>
              <?php if(form_error('site_code')) echo form_error('site_code'); ?>
          </div>
          <div class="col-xs-2 col-md-1">
              <a href="<?php echo site_url('sites/create') ?>" class="button bg-info">Nouveau</a>
          </div>
        </div>
      </div>
    </div>
      <div class="fullwidth-block">
          <div class="form-group">
              <label class="sr-only" for="commentary">Commentaire</label>
              <textarea id="commentary" name="commentary" value="<?php echo set_value('commentary'); ?>" class="form-control" rows="5" placeholder="Commentaire... (max: 2000 caractères)"></textarea>
          </div>
          <?php if(form_error('commentary')) echo form_error('commentary'); ?>

          <div class="form-group">
              <label for="checkbox_env">Environnement contrôlé <span class="small">(serre ou phytotron)</span> : </label>
              <input id="checkbox_env" type="checkbox" <?php if(set_value('checkbox_env')) echo "checked"; ?> value=TRUE name="checkbox_env"/><label for="checkbox_env"><span class="ui"></span></label>
          </div>
          <?php if(form_error('checkbox_env')) echo form_error('checkbox_env'); ?>
      </div>
      <div class="text-right">
          <input type="submit" name="submit" value="Créer">
      </div>
      <?= form_close(); ?>
    </div>
</div>

<script type="text/javascript">
$(window).load(function() {
  $root_depth_range = $('#root_depth_range');
  $root_depth_txt = $('#root_depth_txt');

  synch_title_root_depth();

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

  // Synchronize root_depth fields
  $root_depth_range.on('input', function(event) {
    synch_title_root_depth();
    $root_depth_txt.val($(this).val());
  });

  // Synchronize root_depth fields (bis)
  $root_depth_txt.change(function(event) {
    if ($(this).val() > 200) {
      $(this).val(200.00);
    }
    else if ($(this).val() < 0 || isNaN($(this).val()) || !$(this).val()) {
      $(this).val(0.00);
    }
    synch_title_root_depth();
    $root_depth_range.val($(this).val());
  });

  // Synchronize title of root_depth range
  function synch_title_root_depth() {
    $root_depth_range.prop('title', $root_depth_range.val());
  }
});
</script>
