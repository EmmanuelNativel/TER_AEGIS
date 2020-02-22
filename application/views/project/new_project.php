<div class="fullwidth-block">
  <div class="container">
    <h2 class="section-title">Nouveau projet</h2>

    <?php echo form_open('projects/create', 'class="contact-form" id="newProjectForm"'); ?>
    <?php echo validation_errors() ?>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="sr-only" for="project_code">Code projet</label>
          <input id="project_code" class="form-control" type="text" name="project_code" value="<?php echo set_value('project_code'); ?>" placeholder="Code projet...(ex: acronyme, nom simplifié)">
        </div>

        <div class="form-group">
          <label class="sr-only" for="project_name">Nom</label>
          <input id="project_name" class="form-control" type="text" name="project_name" value="<?php echo set_value('project_name'); ?>" placeholder="Nom projet...">
        </div>

        <div class="form-group">
          <label class="sr-only" for="coordinator">Responsable</label>
          <input id="coordinator" class="form-control" type="text" name="coordinator" value="<?php echo set_value('coordinator'); ?>" placeholder="Responsable...">
        </div>
        <div class="form-group">
          <label class="sr-only" for="coord_company">Organisme</label>
          <input id="coord_company" class="form-control" type="text" name="coord_company" value="<?php echo set_value('coord_company'); ?>" placeholder="Organisme...">

        </div>

        <div class="row">
          <div class="col-xs-10 col-md-9">
            <div class="form-group">
              <label class="sr-only" for="selected_partners">Liste des partenaires</label>
              <select class="selectpicker" id="selected_partners" multiple data-width="100%" data-size="5" name="selected_partners[]" data-live-search="true" data-title="Partenaires...">
                <?php foreach ($partners as $partner): ?>
                  <option <?php if(in_array($partner['partner_code'], $selected_partners)) echo 'selected'; ?> value="<?= $partner['partner_code'] ?>"><?= $partner['partner_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php if(form_error('selected_partners')) echo form_error('selected_partners'); ?>
          </div>
          <div class="col-xs-2 col-md-3">
            <a href="<?= site_url('partners/create') ?>" title="Nouveau partenaire" class="button"><span class="glyphicon glyphicon-plus no-padding"></span></a>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <label class="sr-only" for="project_description">Description</label>
          <textarea class="form-control" name="project_description" id="project_description" rows=10 placeholder="Description..."><?php echo set_value('project_description'); ?></textarea>
        </div>
      </div>
    </div>

    <div class="text-right">
      <input id="newProjectFormBtn" type="submit" name="submit" value="Envoyer">
    </div>

    <?php echo form_close(); ?>

  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="alertModalLabel">Message</h4>
      </div>
      <form class="contact-form">
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="button" data-dismiss="modal">OK</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
$(window).load(function() {
  // Loading
  $('#newProjectForm').submit(function(event) {
    $('input[type=submit]').val("Chargement...");
    $(document).off("click");
  });
});
</script>
