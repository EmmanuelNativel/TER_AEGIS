<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Saisie</li>
        </ol>
        <h2 class="section-title">Nouvelle variable</h2>
        <?php echo form_open('variables/create', 'class="contact-form"'); ?>
        <?php echo validation_errors() ?>
        <div class="row">
            <div class="col-md-8">
				<div class="row">
					<div class="boxed-content">
						<div class="form-group">
							<label class="sr-only" for="variable_code">Variable</label>
							<input id="variable_code" class="form-control" type="text" name="variable_code" value="<?php echo set_value('variable_code'); ?>" placeholder="Code variable...(plant_height_pht1_cm)">
						</div>
						
						<div class="form-group">
							<label class="sr-only" for="trait_code">Trait</label>
							<select class="selectpicker" id="trait_code" data-url="traits/searched_options" data-width="100%" name="trait_code" data-live-search="true" data-title="Code trait...(weath_temp)">
								<?php if (set_value('trait_code')) echo '<option value="' . set_value('trait_code') . '" selected>' . set_value('trait_code') . '</option>'; ?>
							</select>
						</div>
						<?php if (form_error('trait_code')) echo form_error('trait_code'); ?>

						<div class="form-group">
							<label class="sr-only" for="method_code">Méthode</label>
							<select class="selectpicker" id="method_code" data-url="methods/searched_options" data-width="100%" name="method_code" data-live-search="true" data-title="Code méthode...(wea2)">
								<?php if (set_value('method_code')) echo '<option value="' . set_value('method_code') . '" selected>' . set_value('method_code') . '</option>'; ?>
							</select>
						</div>
						<?php if (form_error('method_code')) echo form_error('method_code'); ?>

						<div class="form-group">
							<label class="sr-only" for="scale_code">Unité / Echelle</label>
							<select class="selectpicker" id="scale_code" data-url="scales/searched_options" data-width="100%" name="scale_code" data-live-search="true" data-title="Code échelle / unité...(kg, m/s)">
								<?php if (set_value('scale_code')) echo '<option value="' . set_value('scale_code') . '" selected>' . set_value('scale_code') . '</option>'; ?>
							</select>
						</div>
						<?php if (form_error('scale_code')) echo form_error('scale_code'); ?>

						<div class="form-group">
							<label class="sr-only" for="author">Auteur</label>
							<input id="author" class="form-control" type="text" name="author" value="<?php echo set_value('author'); ?>" placeholder="Auteur">
						</div>

						<div class="form-group">
							<label class="sr-only" for="class">Classe</label>
							<input id="class" class="form-control" type="text" name="class" value="<?php echo set_value('class'); ?>" placeholder="Classe (weather, plant)">
						</div>

						<div class="form-group">
							<label class="sr-only" for="subclass">Sous classe</label>
							<input id="subclass" class="form-control" type="text" name="subclass" value="<?php echo set_value('subclass'); ?>" placeholder="Sous classe (rice, sorghum)">
						</div>
						
						<div class="form-group">
							<label class="sr-only" for="domain">Domaine</label>
							<input id="domain" class="form-control" type="text" name="domain" value="<?php echo set_value('domain'); ?>" placeholder="Domaine (weather_traits, biomass_quality_traits)">
						</div>
					</div>
				</div>
			</div>
			<div class="text-center">
				<input type="submit" name="submit" value="Créer">
			</div>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(window).load(function () {
        $root_depth_range = $('#root_depth_range');
        $root_depth_txt = $('#root_depth_txt');

        synch_title_root_depth();

// UI Calendar
        $('#datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "fr"
        });

// Loading
        $('form').submit(function (event) {
            $('input[type=submit]').val("Chargement...");
            $(document).off("click");
        });

// Synchronize root_depth fields
        $root_depth_range.on('input', function (event) {
            synch_title_root_depth();
            $root_depth_txt.val($(this).val());
        });

// Synchronize root_depth fields (bis)
        $root_depth_txt.change(function (event) {
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

