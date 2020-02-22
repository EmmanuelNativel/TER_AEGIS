<div class="fullwidth-block">
  <div class="container">

      <form action="#" method="post" class="contact-form">

        <div class="col-md-8 col-md-offset-2">
          <?= validation_errors(); ?>
          <h2 class="section-title"><span class="glyphicon flaticon-group"></span>Nouveau groupe de partage de données</h2>

          <p class="text-danger">
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            Champs obligatoires
          </p>

          <div class="form-group has-feedback">
            <label class="sr-only" for="name">Nom</label>
            <input id="name" class="form-control" type="text" name="name" value="<?= set_value('name'); ?>" placeholder="Nom...">
            <span class="glyphicon glyphicon-exclamation-sign form-control-feedback text-danger" aria-hidden="true"></span>
          </div>

          <div class="form-group">
            <label class="sr-only" for="description">Description</label>
            <textarea id="description" class="form-control" name="description" rows="5" cols="80" placeholder="Description... (max : 255)"><?= set_value('description'); ?></textarea>
          </div>

          <div class="row">
            <div class="col-xs-6 text-left">
              <a href="<?=site_url('groups') ?>" class="button"><span class="glyphicon glyphicon-arrow-left"></span>Retour</a>
            </div>
            <div class="col-xs-6 text-right">
              <input type="submit" name="submit" value="Ajouter">
            </div>
          </div>

        </div>

      </form>

  </div>
</div>
