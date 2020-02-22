<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('variables') ?>">Dictionnaire des variables</a></li>
            <li class="active">Saisie</li>
        </ol>
        <h2 class="section-title">Nouvelle variable</h2>
        <?php echo form_open('variables/saisie', 'class="contact-form"'); ?>
        
        <?php if ($this->session->flashdata('msg')!==null) { 
        	$class = "alert-".$this->session->flashdata('msg_state').""?>
        	<div class="alert <?php echo $class; ?>">
		      <?php echo $this->session->flashdata('msg'); ?>
		    </div>
        <?php } ?>
        <?php echo validation_errors() ?>
        <div class="row">
            <div class="col-sm-12">
				<div class="boxed-content">
					<div class="form-group">
						<label class="" for="variable_code">Code Variable</label>
						<input id="variable_code" class="form-control" type="text" name="variable_code" value="<?php echo set_value('variable_code'); ?>" placeholder="Code variable...(plant_height_pht1_cm)">
					</div>

					<div class="form-group">
						<label class="" for="class">Classe</label>
						<input id="class" class="form-control" type="text" name="class" value="<?php echo set_value('class'); ?>" placeholder="Classe (weather, plant)">
					</div>

					<div class="form-group">
						<label class="" for="subclass">Sous-classe</label>
						<input id="subclass" class="form-control" type="text" name="subclass" value="<?php echo set_value('subclass'); ?>" placeholder="Sous-classe (rice, sorghum)">
					</div>
					
					<div class="form-group">
						<label class="" for="domain">Domaine</label>
						<input id="domain" class="form-control" type="text" name="domain" value="<?php echo set_value('domain'); ?>" placeholder="Domaine (weather_traits, biomass_quality_traits)">
					</div>

					<div class="form-group">
						<label class="" for="author">Auteur</label>
						<input id="author" class="form-control" type="text" name="author" value="<?php echo set_value('author'); ?>" placeholder="Auteur">
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div id="div_trait" class="col-xs-12">
				<div class="boxed-content">
					<!-- <h3>Trait</h3> -->
					<div class="form-group">
						<label class="" for="trait_code">Sélectionnez un trait</label>
						<select class="selectpicker" id="trait_code" data-url="traits/searched_options" data-width="100%" name="trait_code" data-live-search="true" data-title="Aucun sélectionné">
							<?php if (set_value('trait_code')) echo '<option value="' . set_value('trait_code') . '" selected>' . set_value('trait_code') . '</option>'; ?>
						</select>
					</div>
					<div class="form-group">
						<button id="btn_new_trait" class="btn-primary" type="button">Nouveau</button>
						<button id="btn_no_trait" class="btn-link float_right" type="button">Aucun</button>
					</div>
					<div id="div_trait_form" class="row">
						<hr>
						<h3 class="text-center">Création d'un trait</h3>
						<div class="col-xs-12">
							<p id="error_add_trait" class="text-danger"></p>
							<div class="form-group">
								<label class="" for="add_trait_code">Code trait</label>
								<input id="add_trait_code" class="form-control" type="text" name="add_trait_code" value="<?php echo set_value('add_trait_code'); ?>" placeholder="Code trait">
							</div>
							<div class="form-group">
								<label class="" for="trait_name">Nom trait</label>
								<input id="trait_name" class="form-control" type="text" name="trait_name" value="<?php echo set_value('trait_name'); ?>" placeholder="Nom trait">
							</div>
							<div class="form-group">
								<label class="" for="trait_description">Description trait</label>
								<textarea id="trait_description" class="form-control" name="trait_description" rows="3" placeholder="Description"><?php echo set_value('trait_description'); ?></textarea>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<label class="" for="trait_entity">Entité</label>
								</div>
								<div class="col-xs-9 col-sm-10 col-md-11">
									<select class="selectpicker" id="entity_code" data-url="entity/searched_options_code_name" data-width="100%" name="entity_code" data-live-search="true" data-title="Entity">
										<?php if (set_value('entity_code')) echo '<option value="' . set_value('entity_code') . '" selected>' . set_value('entity_code') .   '</option>'; ?>
									</select>
								</div>

								<div class="col-xs-3 col-sm-2 col-md-1">
									<button id="btn_add_entity" class="btn btn-primary float_right" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<label class="" for="trait_target">Cible</label>
								</div>
								<div class="col-xs-9 col-sm-10 col-md-11">
									<select class="selectpicker" id="target_name" data-url="targets/searched_options" data-width="100%" name="target_name" data-live-search="true" data-title="Target">
										<?php if (set_value('target_name')) echo '<option value="' . set_value('target_name') . '" selected>' . set_value('target_name') .   '</option>'; ?>
									</select>
								</div>
								<div class="col-xs-3 col-sm-2 col-md-1">
									<button id="btn_add_target" class="btn btn-primary float_right" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
								</div>
							</div>
							<div class="form-group">
								<label class="" for="trait_author">Auteur</label>
								<input id="trait_author" class="form-control" type="text" name="trait_author" value="<?php echo set_value('trait_author'); ?>" placeholder="">
							</div>
							<div class="form-group text-right">
								<button id="btn_create_new_trait" class="btn-primary" type="button">Créer</button>
								<button id="btn_cancel_new_trait" class="btn-danger" type="button">Annuler</button>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div id="div_method" class="col-xs-12 col-sm-6">
				<div class="boxed-content">
					<div class="form-group">
						<label class="" for="trait_code">Sélectionnez une méthode</label>
						<select class="selectpicker" id="method_code" data-url="methods/searched_options" data-width="100%" name="method_code" data-live-search="true" data-title="Aucun sélectionné">
							<?php if (set_value('method_code')) echo '<option value="' . set_value('method_code') . '" selected>' . set_value('method_code') . '</option>'; ?>
						</select>
					</div>
					<div class="form-group">
						<button id="btn_new_method" class="btn-primary" type="button">Nouveau</button>
						<button id="btn_no_method" class="btn-link float_right" type="button">Aucun</button>
					</div>

					<div id="div_method_form" class="row">
						<hr>
						<h3 class="text-center">Création d'une méthode</h3>
						<div class="col-xs-12">
							<p id="error_add_method" class="text-danger"></p>
							<div class="form-group">
								<label class="" for="add_method_code">Code méthode</label>
								<input id="add_method_code" class="form-control" type="text" name="add_method_code" value="<?php echo set_value('add_method_code'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group">
								<label class="" for="add_method_name">Nom méthode</label>
								<input id="add_method_name" class="form-control" type="text" name="add_method_name" value="<?php echo set_value('add_method_name'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="row">
								<div class="col-xs-12">
									<label class="" for="add_method_class">Classe méthode</label>
								</div>
								<div class="col-xs-9 col-md-10">
									<select class="selectpicker" id="add_method_class" data-width="100%" name="add_method_class" data-live-search="true" data-title="Aucun sélectionné">
										<option value="">Aucun sélectionné</option>
										<?php foreach ($method_classes as $key => $value) {
											echo "<option value='".$value['method_class']."'>".$value['method_class']."</option>";
										} ?>
									</select>
								</div>

								<div class="col-xs-3 col-md-2">
									<button id="btn_add_method_class" class="btn btn-primary float_right" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<label class="" for="add_method_subclass">Sous-classe méthode</label>
								</div>
								<div class="col-xs-9 col-md-10">
									<select class="selectpicker" id="add_method_subclass" data-width="100%" name="add_method_subclass" data-live-search="true" data-title="Aucun sélectionné">
										<option value="">Aucun sélectionné</option>
										<?php foreach ($method_subclasses as $key => $value) {
											echo "<option value='".$value['method_subclass']."'>".$value['method_subclass']."</option>";
										} ?>
									</select>
								</div>

								<div class="col-xs-3 col-md-2">
									<button id="btn_add_method_subclass" class="btn btn-primary float_right" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
								</div>
							</div>
							<div class="form-group">
								<label class="" for="add_method_description">Description méthode</label>
								<textarea id="add_method_description" class="form-control" name="add_method_description" rows="3" placeholder="Description"><?php echo set_value('add_method_description'); ?></textarea>
							</div>
							<div class="form-group">
								<label class="" for="add_method_formula">Formule méthode</label>
								<textarea id="add_method_formula" class="form-control" name="add_method_formula" rows="3" placeholder="Description"><?php echo set_value('add_method_formula'); ?></textarea>
							</div>
							<div class="form-group">
								<label class="" for="add_method_reference">Référence méthode</label>
								<textarea id="add_method_reference" class="form-control" name="add_method_reference" rows="3" placeholder="Description"><?php echo set_value('add_method_reference'); ?></textarea>
							</div>
							<div class="form-group">
								<label class="" for="add_method_type">Type méthode</label>
								<input id="add_method_type" class="form-control" type="text" name="add_method_type" value="<?php echo set_value('add_method_type'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group">
								<label class="" for="add_method_content_type">Content type méthode</label>
								<input id="add_method_content_type" class="form-control" type="text" name="add_method_content_type" value="<?php echo set_value('add_method_content_type'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group">
								<label class="" for="add_method_author">Auteur</label>
								<input id="add_method_author" class="form-control" type="text" name="add_method_author" value="<?php echo set_value('add_method_author'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group text-right">
								<button id="btn_create_new_method" class="btn-primary" type="button">Créer</button>
								<button id="btn_cancel_new_method" class="btn-danger" type="button">Annuler</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="div_scale" class="col-xs-12 col-sm-6">
				<div class="boxed-content">
					<div class="form-group">
						<label class="" for="scale_code">Sélectionnez une unité</label>
						<select class="selectpicker" id="scale_code" data-url="scales/searched_options" data-width="100%" name="scale_code" data-live-search="true" data-title="Aucun sélectionné">
							<?php if (set_value('scale_code')) echo '<option value="' . set_value('scale_code') . '" selected>' . set_value('scale_code') . '</option>'; ?>
						</select>
					</div>
					<div class="form-group">
						<button id="btn_new_scale" class="btn-primary" type="button">Nouveau</button>
						<button id="btn_no_scale" class="btn-link float_right" type="button">Aucun</button>
					</div>
					<div id="div_scale_form" class="row">
						<hr>
						<h3 class="text-center">Création d'une unité</h3>
						<div class="col-xs-12">
							<p id="error_add_scale" class="text-danger"></p>
							<div class="form-group">
								<label class="" for="add_scale_code">Code unité</label>
								<input id="add_scale_code" class="form-control" type="text" name="add_scale_code" value="<?php echo set_value('add_scale_code'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group">
								<label class="" for="add_scale_name">Nom unité</label>
								<input id="add_scale_name" class="form-control" type="text" name="add_scale_name" value="<?php echo set_value('add_scale_name'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group">
								<label class="" for="add_scale_type">Type unité</label>
								<input id="add_scale_type" class="form-control" type="text" name="add_scale_type" value="<?php echo set_value('add_scale_type'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group">
								<label class="" for="add_scale_level">Niveau unité</label>
								<input id="add_scale_level" class="form-control" type="text" name="add_scale_level" value="<?php echo set_value('add_scale_level'); ?>" placeholder="Nom entité...(lastleamsh)">
							</div>
							<div class="form-group text-right">
								<button id="btn_create_new_scale" class="btn-primary" type="button">Créer</button>
								<button id="btn_cancel_new_scale" class="btn-danger" type="button">Annuler</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 text-center">
			<input type="submit" name="submit" value="Créer une variable">
		</div>
		<?php echo form_close(); ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addEntityModal" tabindex="-1" role="dialog" aria-labelledby="addEntityModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addEntityModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter une entité</h4>
            </div>
            <div class="modal-body">
				<p id="error_add_entity" class="text-danger"></p>
            	<div class="form-group">
					<label class="" for="add_entity_code">Code entité</label>
					<input id="add_entity_code" class="form-control" type="text" name="add_entity_code" value="<?php echo set_value('add_entity_code'); ?>" placeholder="Nom entité...(lastleamsh)">
				</div>
                <div class="form-group">
					<label class="" for="add_entity_name">Nom entité</label>
					<input id="add_entity_name" class="form-control" type="text" name="add_entity_name" value="<?php echo set_value('add_entity_name'); ?>" placeholder="Nom entité...(lastleamsh)">
				</div>
				<div class="form-group">
					<label class="" for="add_entity_definition">Définition enitité</label>
					<textarea id="add_entity_definition" class="form-control" name="add_entity_definition" rows="3" placeholder="Définition"><?php echo set_value('add_entity_definition'); ?></textarea>
				</div>
			</div>

            <div class="modal-footer">                	
            	<button type="button" id="btn_confirm_add_entity" class="button button-primary" name="button">Ajouter</button>
                <button type="button" id="btn_cancel_add_entity" class="button button-danger" data-dismiss="modal" name="button">Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTargetModal" tabindex="-1" role="dialog" aria-labelledby="addTargetModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addTargetModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter une cible</h4>
            </div>
            <div class="modal-body">
				<p id="error_add_target" class="text-danger"></p>
                <div class="form-group">
					<label class="" for="add_target_name">Nom cible</label>
					<input id="add_target_name" class="form-control" type="text" name="add_target_name" value="<?php echo set_value('add_target_name'); ?>" placeholder="nom cible...(leaf-temperature)">
				</div>
			</div>
            <div class="modal-footer">                	
            	<button type="button" id="btn_confirm_add_target" class="button button-primary" name="button">Ajouter</button>
                <button type="button" id="btn_cancel_add_target" class="button button-danger" data-dismiss="modal" name="button">Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMethodClassModal" tabindex="-1" role="dialog" aria-labelledby="addMethodClassModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addMethodClassModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter une classe pour les méthodes</h4>
            </div>
            <div class="modal-body">
				<p id="error_add_method_class" class="text-danger"></p>
                <div class="form-group">
					<label class="" for="add_method_class_name">Nom classe</label>
					<input id="add_method_class_name" class="form-control" type="text" name="add_method_class_name" value="<?php echo set_value('add_method_class_name'); ?>" placeholder="nom classe...()">
				</div>
			</div>
            <div class="modal-footer">                	
            	<button type="button" id="btn_confirm_add_method_class" class="button button-primary" name="button">Ajouter</button>
                <button type="button" id="btn_cancel_add_method_class" class="button button-danger" data-dismiss="modal" name="button">Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMethodSubclassModal" tabindex="-1" role="dialog" aria-labelledby="addMethodSubclassModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addMethodSubclassModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter une sous-classe pour les méthodes</h4>
            </div>
            <div class="modal-body">
				<p id="error_add_method_subclass" class="text-danger"></p>
                <div class="form-group">
					<label class="" for="add_method_subclass_name">Nom sous-classe</label>
					<input id="add_method_subclass_name" class="form-control" type="text" name="add_method_subclass_name" value="<?php echo set_value('add_method_subclass_name'); ?>" placeholder="nom classe...()">
				</div>
			</div>
            <div class="modal-footer">                	
            	<button type="button" id="btn_confirm_add_method_subclass" class="button button-primary" name="button">Ajouter</button>
                <button type="button" id="btn_cancel_add_method_subclass" class="button button-danger" data-dismiss="modal" name="button">Annuler</button>
            </div>
        </div>
    </div>
</div>