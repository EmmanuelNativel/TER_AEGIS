<div class="fullwidth-block">
  <div class="container">
    <h2 class="section-title">Modification du Projet <?= $project_code ?></h2>

      <?= form_open('projects/update/'.$project_code, 'class="contact-form" id="updateProjectForm"'); ?>
      <?= validation_errors(); ?>

      <div class="boxed-content">
      <div class="row">
        <div class="col-md-4">

          <div class="form-group">
            <label class="" for="project_name">Nom</label>
            <input id="project_name" class="form-control" type="text" name="project_name" value="<?= html_escape(set_value('project_name')? set_value('project_name'): $project_name); ?>" placeholder="Nom...">
          </div>

          <div class="form-group">
            <label class="" for="coordinator">Responsable</label>
            <input id="coordinator" class="form-control" type="text" name="coordinator" value="<?= html_escape(set_value('coordinator')? set_value('coordinator'): $coordinator); ?>" placeholder="Responsable...">
          </div>
          <div class="form-group">
            <label class="" for="coord_company">Société affiliée</label>
            <input id="coord_company" class="form-control" type="text" name="coord_company" value="<?= html_escape(set_value('coord_company')? set_value('coord_company'): $coord_company); ?>" placeholder="Société affiliée...">
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <label class="" for="project_description">Description</label>
            <textarea class="form-control" name="project_description" id="project_description" rows=8 placeholder="Description..."><?= set_value('project_description')? set_value('project_description'): $project_resume; ?></textarea>
          </div>
        </div>

      </div>
    </div>

    <div class="text-right">
      <button type="button" onclick="location.href='<?= site_url('projects/display/'.$project_code) ?>';" class="button button-danger"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
      <button type="submit" class="button"><span class="glyphicon glyphicon-ok"></span>Modifier</button>
    </div>

    <?= form_close(); ?>
  </div>
</div>
