<div class="fullwidth-block">
  <div class="container">
    <div class="col-md-6 col-md-offset-3">
      <h2 class="section-title">Nouveau partenaire</h2>
      <?php echo form_open('partners/create', 'class="contact-form"'); ?>
      <?php echo validation_errors() ?>

      <div class="form-group">
        <label class="sr-only" for="partner_name">Nom</label>
        <input id="partner_name" class="form-control" type="text" name="partner_name" value="<?php echo set_value('partner_name'); ?>" placeholder="Nom...">
      </div>

      <div class="form-group">
        <label class="sr-only" for="adress">Adresse</label>
        <input id="adress" class="form-control" type="text" name="adress" value="<?php echo set_value('adress'); ?>" placeholder="Adresse...">
      </div>

      <div class="form-group">
        <label class="sr-only" for="zip_code">Code postal</label>
        <input id="zip_code" class="form-control" type="text" name="zip_code" value="<?php echo set_value('zip_code'); ?>" placeholder="Code postal...">
      </div>

      <div class="form-group">
        <label class="sr-only" for="city">Ville</label>
        <input id="city" class="form-control" type="text" name="city" value="<?php echo set_value('city'); ?>" placeholder="Ville...">
      </div>

      <div class="form-group">
        <label class="sr-only" for="select_country">Pays</label>
        <select class="selectpicker" id="select_country" data-width="100%" data-size="5" name="select_country" data-live-search="true" data-title="Pays...">
          <?php foreach ($countrys as $country): ?>
            <option <?php if($select_country == $country['country_code'])
                echo 'selected'; ?> value="<?= $country['country_code'] ?>">
                <?= $country['country'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="text-right">
        <input type="submit" name="button" value="Créer"></input>
      </div>


      <?php form_close(); ?>
    </div>

  </div>
</div>
