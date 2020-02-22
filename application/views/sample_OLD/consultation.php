<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Consultation échantillons</li>
        </ol>
        <h2 class="section-title">Consultation des échantillons</h2>
        <div class="row">
    		
    		<div id="div_datatable_sample" class="col-xs-12">
            	<div class="boxed-content">
            		<table id="table_consultation_sample" class="table table-hover width_table_dataTable">
						<thead>
							<tr>
							<th>Code-barres</th>
							<th>Type</th>
							<th>Nombre d'objet</th>
							<th>Code plante</th>
							<th>Entité</th>
							<th>Référence</th>
							<th>Stade</th>
							<th>Essai</th>
							</tr>
						</thead>						
		        	</table>
				</div>
			</div>
		</div>
		<div class="row">
    		<div class="col-xs-12">
            	<div id="datasetInfo" class="boxed-content">
            		<h3>Selection des échantillons par éssai </h3>
                    <div class="form-group">
                        <label for="select_essai">Sélectionnez un essai <span class="glyphicon glyphicon-info-sign text-info" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Vous pouvez sélectionner et désélectionner tous les échantillons d'un essai"></span> : </label>
                        <div class="form-input">
                            <select class="selectpicker" id="select_essai" data-width="100%" data-size="5" name="select_essai" data-live-search="true" data-title="Essai">
                        	<?php
                            	foreach ($trials as $trial) {
                                    echo '<option value="' . $trial['trial_code'] . '" ' . set_select('select_essai', $trial['trial_code']) . '>' . $trial['trial_code'] . "</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-xs-12">
                    		<div class="visible-xs">
					    		<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle btn-block" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<!-- <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> -->
										Action
								    	<span class="caret"></span>

									</button>
									<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
										<li><a id="btn_select_trial">Selectionner les échantillons</a></li>
									    <li><a id="btn_deselect_trial">Désélectionner les échantillons</a></li>
									</ul>
								</div>
							</div>
							<div class="hidden-xs">
		                    	<button id="btn_select_trial" class="btn btn-primary">Selectionner les échantillons</button>
					    		<button id="btn_deselect_trial" class="btn btn-primary">Désélectionner les échantillons</button>
							</div>
		                    
		                </div>
	                </div>

            	</div>
        	</div>
        </div>
		<div class="row">
    		<div class="col-xs-12 col-sm-8">
            	<div class="boxed-content">
            		<h3>Liste des échantillons séléctionnés</h3>
                	<div id="div_table_selected_sample" class=" table-responsive">
                		<table id="selected_sample_table" class="table table-hover">
							<thead>
								<tr>
								<th>Code-barres</th>
								<th>Type</th>
								<th></th>
								</tr>
							</thead>
							<tbody id="body_selected_sample_table">
								
							</tbody>
			        	</table>
                	</div>
            	</div>
            </div>
            <div class="col-xs-12 col-sm-4 position_sticky">
            	<div class="boxed-content">
            		<div class="row">
            			<div class="col-xs-12">
							<p id="p_btn_extract_error" class="text-danger"></p>
            				<button id="btn_extract_code" class="btn btn-primary btn-block">Extraction code-barres</button>
            				<!-- <button id="btn_extract_code" class="btn btn-primary btn-block">Extraction données</button> -->
            			</div>
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ExtractCodeModal" tabindex="-1" role="dialog" aria-labelledby="ExtractCodeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ExtractCodeModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Extraction des code-barres</h4>
            </div>
        	<?php echo form_open('samples/extract_bar_code', 'id="form_extract_code" class="contact-form"'); ?>
	            <div class="modal-body">
	                <div class="form-group">
	            		<p id="error_extract_code" class="text-danger"></p>
	                    <label class="" for="add_field_extract_code">Selection des champs pour extraction :</label>
	            		<select class="selectpicker" id="add_field_extract_code" data-width="100%" data-size="5" name="add_field_extract_code[]" data-live-search="true" data-title="Champs" multiple  data-selected-text-format="count > 5" data-count-selected-text="{0} champs séléctionnés">
	                    	<option value="sample_type">Type</option>
	                    	<option value="sample_nb_objects">Nombre</option>
	                    	<option value="sample_plant_code">Code plante</option>
	                    	<option value="sample_entity">Entity</option>
	                    	<option value="sample_entity_ref">Entity ref</option>
	                    	<option value="sample_entity_level">Entity level</option>
	                    	<option value="st_name">Sample stage</option>
	                    	<option value="trial_code">Code de l'éssai</option>
	                    </select>
	                </div>
	                <div class="row">
	                	<div class="col-xs-12">
	                		<button id="btn_deselect_field" type="button" class="button button-default float_right" name="button">Aucun</button>
	                	</div>
                	</div>
	                
	            </div>
	            <div class="modal-footer">
                	<p id="p_btn_extract_submit_error" class="text-danger text-right"></p>
        			<input type="submit" name="Extraire" class="" value="Extraire">

	                <button type="button" class="button button-danger" data-dismiss="modal" name="button">Annuler</button>
	            </div>
	        </form>
        </div>
    </div>
</div>