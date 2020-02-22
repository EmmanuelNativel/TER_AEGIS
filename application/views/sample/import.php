<div class="fullwidth-block">
	<div class="container">
		<ol class="breadcrumb">
			<li><a href="<?= site_url('samples') ?>">Gestion des échantillons</a></li>
			<li class="active">Importation</li>
		</ol>
		<?= form_open_multipart('samples/import', 'class="contact-form"');?>

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
		<div class="col-md-5">
			<div class="boxed-content">
				<h2 class="section-title"><strong>3.</strong> Télécharger le formulaire</h2>
				<div class="text-center">
					<a href="<?= site_url('samples/download_form'); ?>" class="button btn-lg only-icon" role="button"><span class="glyphicon glyphicon-floppy-disk"></span> .XLS</a>
				</div>
			</div>
		</div>

		<div class="col-md-7">
			<div class="boxed-content">
				<h2 class="section-title"><strong>4.</strong> Remplir le formulaire</h2>
				<div class="alert alert-info">
					<p>
						<span class="glyphicon glyphicon-question-sign"></span>
						La date doit prendre le format suivant (dd/mm/yyyy) ou (yyyy-mm-dd).<br>
						Toutes Les variables dans l'en-tête du formulaire d'échantillon (bleu ciel) doivent être définies dans le dictionnaire des variables de DAPHNE.
						L'en-tête fixe (bleu marine) ne doit pas être modifié.
					</p>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="boxed-content">
				<h2 class="section-title"><strong>5.</strong> Envoyer le formulaire</h2>

				<div class="row">
					<div class="col-md-10">
						<div class="form-group has-feedback">
							<input type="file" name="sample_file" size="20" />
							<span class="glyphicon glyphicon-exclamation-sign form-control-feedback text-danger" aria-hidden="true"></span>
						</div>

					</div>
					<div class="col-md-2">
						<div class="text-center">
							<input type="submit" name="submit" value="Importer" />
						</div>
					</div>
				</div>

				<?= (isset($error))? $error : NULL; ?>

			</div>
		</div>

		<?= form_close(); ?>
	</div>
</div>
