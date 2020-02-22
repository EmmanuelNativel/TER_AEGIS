<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Saisie</li>
        </ol>
        <h2 class="section-title">Nouvel itinéraire technique</h2>
        <?php echo form_open('itks/create', 'class="contact-form"'); ?>
        <?php echo validation_errors() ?>
		
			<div class="col-md-12">
				<p class="text-danger">
					<span class="glyphicon glyphicon-exclamation-sign"></span>
					Champs obligatoires
				</p>
				
				<div class="boxed-content">
					<h2 class="section-title"><strong>1.</strong> De quel essai s'agit-il?</h2>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="project_code">Projet</label>
								<select class="selectpicker" id="project_code" data-url="projects/searched_options_user_projects" data-width="100%" name="project_code" data-live-search="true" data-title="Projet (PROJECT_CODE)">
									<?php if(set_value('project_code')) echo '<option value="'.set_value('project_code').'" selected>'.set_value('project_code').'</option>'; ?>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group has-feedback">
								<label class="sr-only" for="trial_code">Essai</label>
								<select class="selectpicker" id="trial_code" data-url="" data-width="100%" name="trial_code" data-live-search="true" data-title="Essai (TRIAL_CODE)">
									<?php if(set_value('trial_code')) echo '<option value="'.set_value('trial_code').'" selected>'.set_value('trial_code').'</option>'; ?>
								</select>
								<span class="glyphicon glyphicon-exclamation-sign form-control-feedback text-danger" aria-hidden="true"></span>
							</div>
						</div>

					</div>
					<?= form_error('trial_code') ?>
				</div>
				
				<script type="text/javascript">
					// Script de mise à jour des essais en fonction du projet
					$(document).ready(function() {
						var project_selector = $('#project_code');
						var trial_selector = $('#trial_code');
						var dataset_selector = $('#dataset_id');
		
						if (!project_selector.val()) {
						  trial_selector.prop('disabled', 'disabled');
						  dataset_selector.prop('disabled', 'disabled');
						}

						project_selector.on('change', function() {
						  trial_selector.prop('disabled', false);
						  trial_selector.html(null).selectpicker('refresh');
						  
						  dataset_selector.prop('disabled', false);
						  dataset_selector.html(null).selectpicker('refresh');
						  
						  if (project_selector.val() != "") {
							trial_selector.data('url', 'trials/searched_options/' + project_selector.val());
							
							dataset_selector.data('url', 'datasets/searched_user_datasets_project/' + project_selector.val());
						  }
						  else {
							trial_selector.prop('disabled', 'disabled');
							
							dataset_selector.prop('disabled', 'disabled');
							}
						});
					});
				</script>
			</div>
		
			<div class="col-md-12">
				<div class="boxed-content">
					<h2 class="section-title"><strong>2.</strong> Dans quel jeu de données voulez vous partager ces itinéraires techniques?</h2>
					<div class="form-group has-feedback">
						<label class="sr-only" for="dataset_name">Jeu de données</label>
						<select class="selectpicker" id="dataset_name" data-url="datasets/searched_options" data-width="100%" name="dataset_name" data-live-search="true" data-title="Dataset...">
							<?php if(set_value('dataset_name')) echo '<option value="'.set_value('dataset_name').'" selected>'.set_value('dataset_name').'</option>'; ?>
						</select>
						<span class="glyphicon glyphicon-exclamation-sign form-control-feedback text-danger" aria-hidden="true"></span>
					</div>
					<?= form_error('dataset_name') ?>
					<div class="alert alert-warning">
						<p>
							<span class="glyphicon glyphicon-warning-sign"></span>
							<strong>Attention</strong>, la visibilité des informations renseignées est définit par le jeu de données selectionné.
							Si vous n'êtes pas sûr de la visibilité d'un jeu de données vous pouvez toujours en créer un nouveau.
						</p>
					</div>
				</div>
			</div>
		
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="boxed-content">
							
                            <div class="form-group">
                                <label class="sr-only" for="variable_code">Variable</label><select class="selectpicker" id="variable_code" data-url="Treeview/searched_options" data-width="100%" name="variable_code" data-live-search="true" data-title="Code Variable...(flag_fw_fre2_g, grn_mat_mat1_d)">
                                    <?php if (set_value('variable_code')) echo '<option value="' . set_value('variable_code') . '" selected>' . set_value('variable_code') . '</option>'; ?>
                                </select>
                            </div>
                            <?php if (form_error('variable_code')) echo form_error('variable_code'); ?>

                            <div class="form-group">
                                <label class="sr-only" for="unit_code">Unité expérimentale</label><select class="selectpicker" id="unit_code" data-url="Exp_unit/searched_options" data-width="100%" name="unit_code" data-live-search="true" data-title="Code unité expérimentale...(R1_C, P01)">
                                    <?php if (set_value('unit_code')) echo '<option value="' . set_value('unit_code') . '" selected>' . set_value('unit_code') . '</option>'; ?>
                                </select>
                            </div>
                            <?php if (form_error('unit_code')) echo form_error('unit_code'); ?>
							
							<div class="form-group">
                                <label class="sr-only" for="itk_date">Date</label>
                                <input id="itk_date" class="form-control" type="text" name="itk_date" value="<?php echo set_value('itk_date'); ?>" placeholder="Date (yyyy-mm-dd)">
                            </div>
							
                            <div class="form-group">
                                <label class="sr-only" for="itk_duration">Durée</label>
                                <input id="itk_duration" class="form-control" type="text" name="itk_duration" value="<?php echo set_value('itk_duration'); ?>" placeholder="Durée">
                            </div>

							<div class="form-group">
                                <label class="sr-only" for="itk_value">Valeur</label>
                                <input id="itk_value" class="form-control" type="text" name="itk_value" value="<?php echo set_value('itk_value'); ?>" placeholder="Valeur">
                            </div>
							
                        </div>
                    </div>
                </div>
				
                <div class="text-center">
                    <input type="submit" name="submit" value="Créer">
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

