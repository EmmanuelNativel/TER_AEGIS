<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Operations</li>
        </ol>
        <h2 class="section-title">Nouvelle opération</h2>
        <!-- <?php print_r($list_sample_input_post); ?> -->
        <?php 
	        if ($this->session->flashdata('error_input')) {
        		echo '<div class="alert alert-danger">Problème avec les échantillons en entrée. </br>
        		Au moins un échantillon doit être séléctionné et le nombre d\'éléments retirés ne peut dépasser le nombre actuel</div>';
        	}
        	if ($this->session->flashdata('is_okay_operation')) {
        		echo '<div class="alert alert-success">L\'opération à bien été effectuée</div>';
        	}
        ?>
        <?php echo form_open('operations/create', 'class="contact-form"'); ?>
	        <div class="row">
	        	<div class="col-md-4 col-md-push-8">
                	<div id="datasetInfo" class="boxed-content">
                        <div class="form-group">
                            <div class="row">
	                        	<div class="col-xs-12">
                            		<label>Sélectionnez les échantillons en entrée</label>
                            	</div>
	                        </div>
	                        <div class="row">
	                        	<div class="col-xs-offset-1">
									<button id="btn_addSampleInput" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addSampleInput">Ajouter</button>
								</div>
	                    	</div>
                        </div>
                    </div>
            	</div>
                <div class="col-md-8 col-md-pull-4">
            		<div class="boxed-content">
            			<h3>Liste des échantillons en entrée</h3>
                    	<div id="div_table_sample" class=" table-responsive">
                    		<table id="sample_table" class="table table-hover">
								<thead>
									<tr>
									<th>Code échantillon</th>
									<th>Laboratoire</th>
									<th>Nombre d'objet</th>
									<th>Nombre retiré</th>
									<th></th>
									</tr>
								</thead>
								<tbody id="body_sample_input_table">
									<?php
										foreach ($list_sample_input_post as $input) {
											$code = $input[0];
											$val_labo = $input[1];
											$nb_input = $input[2];
											$nb_remove = $input[3];
											$row = "<tr id='".$code."' class='row_sample_input_table'>";
											$row .= "<td>".$code."</td>";
											$row .= "<td> <select class='selectpicker' id='lab_select-".$code."' data-size='5' name='lab_select-".$code."' data-live-search='true' data-title='Labo'>";
                            	
		                                	foreach ($list_lab as $lab) {
		                                		if ($lab['lab_id'] == $val_labo) {
			                                        $row .= "<option value='".$lab['lab_id'] . "' selected>" . $lab['lab_code'] . "</option>";
			                                	}else{
			                                        $row .= "<option value='".$lab['lab_id'] . "' >" . $lab['lab_code'] . "</option>";
			                                	}
		                                    }
                            				$row .= "</select> </td>";
											$row .= "<td> <input id='nb_input-".$code."' class='form-control' type='number' min='0' name='nb_input-".$code."' value='".$nb_input."' readonly></td>";
											$row .= "<td> <input id='nb_remove_input-".$code."' class='form-control' type='number' min='0' name='nb_remove_input-".$code."' value='".$nb_remove."'> </td>"; 

											$row .= "<td><div> <span title='Retirer' class='close span_remove_sample' data-code_sample='".$code."'><span>×</span></span></div></td>" ;
											$row .="</tr>";

											echo ($row);
										}
									?>
								</tbody>
				        	</table>
                    	</div>
               		</div>
           		</div>
       		</div>

       		<div class="row">
       			<div class="col-xs-12">
            		<div class="boxed-content">
            			<h3>Opération à effectuer</h3>
            			<div class="row">
                        	<div class="col-xs-12">
                            	<label for="operation_type">Type d'opération :</label>
                            </div>
                        	<div class="col-xs-9 col-sm-7 col-md-9">
                            	<?php echo form_error('operation_type', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                                <select class="selectpicker selectpicker_operation" id="operation_type" data-width="100%" data-size="5" name="operation_type" data-live-search="true" data-title="Opération">
                            	<?php
                                	foreach ($list_operation as $operation) {
                                        echo '<option value="' . $operation['id'] . '" ' . set_select('operation_type', $operation['id']) . '>' . $operation['operation_name'] . "</option>";
                                    }
                                ?>
                                </select>
                            </div>
	                        <div class="col-xs-3 col-sm-5 col-md-3 text-right">
	                            <div class="dropdown">
									<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<div class="visible-xs">
								    		<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
										</div>
										<div class="hidden-xs">
								    		Gestion des opérations
								    		<span class="caret"></span>
										</div>

									</button>
									<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
										<li><a data-toggle="modal" data-target="#addOperationModal">Ajouter</a></li>
									    <li><a data-toggle="modal" data-target="#updateOperationModal">Modifier</a></li>
									    <li><a data-toggle="modal" data-target="#deleteOperationModal">Supprimer</a></li>
									</ul>
								</div>
							</div>
                        </div>
            			<div class="form-group">
                            <label class="" for="operation_date">Date :</label>
                            <?php echo form_error('operation_date', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <input id="operation_date" class="form-control" type="date" name="operation_date" value="<?php echo set_value('operation_date'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="" for="operation_loca">Localisation :</label>
                            <?php echo form_error('operation_loca', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <input id="operation_loca" class="form-control" type="text" name="operation_loca" value="<?php echo set_value('operation_loca'); ?>" placeholder="Localisation">
                        </div>
                        <div class="form-group">
                            <label class="" for="operation_info">Informations supplémentaires :</label>
                            <?php echo form_error('operation_info', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <textarea id="operation_info" class="form-control" name="operation_info" rows="5" placeholder="Informations supplémentaires" ><?php echo set_value('operation_info'); ?></textarea>
                        </div>
            		</div>
            	</div>
       		</div>

       		<?php 
		        if ($this->session->flashdata('error_output')) {
	        		echo '<div class="row"><div class="col-xs-12"> <div class="alert alert-danger">Problème avec les échantillons en sortie. Au moins un échantillon doit être créé.</div></div></div>';
	        	}
	        ?>
       		<div class="row">
       			<div class="col-md-4 col-md-push-8">
            		<div class="boxed-content">
            			<div class="form-group">
                            <div class="row">
	                        	<div class="col-xs-12">
                            		<label>Création d'échantillons en sortie</label>
		                        	<div class="form-group">
			                            <label class="" for="sample_type">Type d'échantillon :</label>
			                            <input id="sample_type" class="form-control" type="text" name="sample_type" placeholder="Type d'échantillon">
			                        </div>
			                        <div class="form-group">
			                            <label class="" for="sample_nb">Nombre d'objet :</label>
			                            <input id="sample_nb" class="form-control" type="number" name="sample_nb" placeholder="Nombre d'objet">
			                        </div>

									<button id="btn_addSampleOutput" type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="right" title="Les deux champs sont obligatoires. Le nombre doit être supérieur a 0">Ajouter</button>
								</div>
	                    	</div>
                        </div>
            		</div>
            	</div>
            	<div class="col-md-8 col-md-pull-4">
            		<div class="boxed-content">
            			<h3>Liste des échantillons en sortie</h3>
            			<div id="div_table_sample_output" class=" table-responsive">
                    		<table id="sample_table_output" class="table table-hover">
								<thead>
									<tr>
									<th>Type d'échantillon</th>
									<th>Nombre d'objet</th>
									<th>Laboratoire</th>
									<th></th>
									</tr>
								</thead>
								<tbody id="body_sample_output_table">
									<?php
										foreach ($list_sample_output_post as $input) {
											$code = $input[0];
											$sample_type = $input[1];
											$nb_output = $input[2];
											$val_labo = $input[3];
											$row = "<tr id='nb_".$code."'>";

											$row .="<td><input id='sample_output_type-".$code."' class='form-control' type='text' name='sample_output_type-".$code."' value='".$sample_type."' readonly></td>";
											$row .= "<td><input id='sample_output_nb-".$code."' class='form-control' type='number' min='0' name='sample_output_nb-".$code."' value='".$nb_output."' readonly></td>";
											$row .= "<td> <select class='selectpicker' id='lab_select_output-nb_".$code."' data-size='5' name='lab_select_output-nb_".$code."' data-live-search='true' data-title='Labo'>";
                            	
		                                	foreach ($list_lab as $lab) {
		                                		if ($lab['lab_id'] == $val_labo) {
			                                        $row .= "<option value='".$lab['lab_id'] . "' selected>" . $lab['lab_code'] . "</option>";
			                                	}else{
			                                        $row .= "<option value='".$lab['lab_id'] . "' >" . $lab['lab_code'] . "</option>";
			                                	}
		                                    }
                            				$row .= "</select> </td>";
											$row .= "<td><div> <span title='Retirer' class='close span_remove_sample_output' data-nb_output='nb_".$code."'><span>×</span></span></div></td>" ;
											$row .="</tr>";

											echo ($row);
										}
									?>
								</tbody>
							</table>
						</div>
            		</div>
            	</div>
       		</div>
       		<div class="row">
       			<div class="col-xs-12">
            		<div class="boxed-content">
                        <label>Effectuer l'opération</label>
       					<div class="row">
       						<div class="col-xs-12">
                        		<button type="submit" class="btn btn-default">Effectuer l'opération</button>
                    		</div>
                    	</div>
            		</div>
       			</div>
       		</div>
   		</form>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addSampleInput" tabindex="-1" role="dialog" aria-labelledby="addSampleInputLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addSampleInputLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter des échantillons</h4>
            </div>
            <div class="modal-body">
            	<p>Cliquez sur une ligne du tableau pour choisir un échantillon</p>
            	<div class="row">
            		<div class="col-xs-12">
	            		<div id="div_table_add_sample_input_list" class=" table-responsive">
	                	<?php
				          if ($list_sample_input != null) {
				            $template = array('table_open' => '<table id="table_add_sample_input_list" class="table table-hover width_table_dataTable">');
				            $this->table->set_template($template);
				            $this->table->set_heading(array('Code-barres', 'Type', 'Nombre', 'Code plante', 'Entity', 'Entity ref', 'Entity level', 'Sample stage'));

				            foreach ($list_sample_input as $sample) {

				              $this->table->add_row(array($sample['sample_code'], $sample['sample_type'], $sample['sample_nb_objects'], $sample['sample_plant_code'], $sample['sample_entity'], $sample['sample_entity_ref'], $sample['sample_entity_level'], $sample['st_name']));
				            }
				            echo $this->table->generate();
				          }
				          else {
				            echo "Aucun échantillon n'est disponible.";
				          }
				          ?>
				      	</div>
			      	</div>
			    </div>
			</div>
            <div class="modal-footer">
            	<button type="button" id="btn_selectionner" class="button button-secondary" data-dismiss="modal" name="button"></span>Selectionner</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addOperationModal" tabindex="-1" role="dialog" aria-labelledby="addOperationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addOperationModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter une opération</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
            		<p id="error_add_operation_type" class="text-danger"></p>
                    <label class="" for="add_operation_name">Nom de l'opération :</label>
                    <input id="add_operation_name" class="form-control" type="text" name="add_operation_name" value="<?php if (isset($this->session->flashdata('add_sample_stage_values')['add_operation_name'])) { echo($this->session->flashdata('add_sample_stage_values')['add_operation_name']); } ?>" placeholder="Operation">
                </div>
            </div>
            <div class="modal-footer">
            	<button id="btn_add_operation" type="button" class="button button-secondary" name="btn_add_operation">Ajouter</button>
                <button type="button" class="button button-danger" data-dismiss="modal" name="button"></span>Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateOperationModal" tabindex="-1" role="dialog" aria-labelledby="updateOperationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="updateOperationModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Modifier une opération</h4>
            </div>
            <div class="modal-body">
            	<div class="form-group">
            		<p id="error_update_operation_type" class="text-danger"></p>
                    <label class="" for="trial">Opération : </label>
                    <select class="selectpicker selectpicker_operation" id="update_operation_type" data-width="100%" data-size="5" name="update_operation_type" data-live-search="true" data-title="Opération">
                    	<?php
                        	foreach ($list_operation as $operation) {
                                echo '<option value="' . $operation['id'] . '" ' . set_select('operation_type', $operation['id']) . '>' . $operation['operation_name'] . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
            		<p id="error_update_operation_name" class="text-danger"></p>
                    <label class="" for="update_operation_name">Nouveau nom de l'opération :</label>
                    <input id="update_operation_name" class="form-control" type="text" name="update_operation_name" value="<?php if (isset($this->session->flashdata('add_sample_stage_values')['update_operation_name'])) { echo($this->session->flashdata('add_sample_stage_values')['update_operation_name']); } ?>" placeholder="Operation">
                </div>
            </div>
            <div class="modal-footer">
            	<button id="btn_update_operation" type="button" class="button button-secondary" name="btn_update_operation">Modifier</button>
                <button type="button" class="button button-danger" data-dismiss="modal" name="button"></span>Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteOperationModal" tabindex="-1" role="dialog" aria-labelledby="deleteOperationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteOperationModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Supprimer une opération</h4>
            </div>
            <div class="modal-body">
            	<div class="form-group">
            		<p id="error_delete_operation_type" class="text-danger"></p>
                    <label class="" for="trial">Opération : </label>
                    <select class="selectpicker selectpicker_operation" id="delete_operation_type" data-width="100%" data-size="5" name="delete_operation_type" data-live-search="true" data-title="Opération">
                    	<?php
                        	foreach ($list_operation as $operation) {
                                echo '<option value="' . $operation['id'] . '" ' . set_select('operation_type', $operation['id']) . '>' . $operation['operation_name'] . "</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
            	<button id="btn_delete_operation" type="button" class="button button-secondary" name="btn_delete_operation">Supprimer</button>
                <button type="button" class="button button-danger" data-dismiss="modal" name="button"></span>Annuler</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
	var list_lab = <?php echo(json_encode($list_lab)); ?>;
</script>