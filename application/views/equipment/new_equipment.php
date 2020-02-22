<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Saisie</li>
        </ol>
        <h2 class="section-title">Nouvel équipement</h2>
        <?php echo form_open('equipments/create', 'class="contact-form"'); ?>
        <?php echo validation_errors() ?>
		
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="boxed-content">
								
								<div class="form-group">
									<label class="sr-only" for="equipment_name">Nom Equipement</label>
									<input id="equipment_name" class="form-control" type="text" name="equipment_name" value="<?php echo set_value('equipment_name'); ?>" placeholder="Nom Equipement...">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="equipment_class">Classe d'equipement</label>
									<input id="equipment_class" class="form-control" type="text" name="equipment_class" value="<?php echo set_value('equipment_class'); ?>" placeholder="Classe d'équipement">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="equipment_features">Caractéristiques</label>
									<input id="equipment_features" class="form-control" type="text" name="equipment_features" value="<?php echo set_value('equipment_features'); ?>" placeholder="Caractéristiques équipement">
								</div>
								
							</div>
						</div>
					</div>
					
					<div class="text-center">
						<input type="submit" name="submit" value="Créer">
					</div>
				</div>
			</div>
        </form>
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

