<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('samples') ?>">Gestion des échantillons</a></li>
            <li class="active">Saisie Echantillons</li>
        </ol>
        <h2 class="section-title">Nouveaux échantillons</h2>
        <?php echo form_open('samples/create', 'class="contact-form"'); ?>
        <?php 
        	if ($this->session->flashdata('insert_sample_ok') == TRUE) {
        		$str = '';
        		foreach ($tab_sample_create as $key => $sample_id) {
        			$str .=  $sample_id.' ';
        		}
        		echo '<div class="alert alert-success">La création de(s) échantillon(s) a bien été effectuée. Voici l\'ensemble de(s) échantillon(s) : <br>'.$str.'</div>';

        	}
        	if ($this->session->flashdata('sample_stage_operation') == 1) {
        		echo '<div class="alert alert-success">Le sample stage à bien été créé </div>';
        	}
        	if ($this->session->flashdata('sample_stage_operation') == 2) {
        		echo '<div class="alert alert-success">Le sample stage à bien été modifié </div>';
        	}
        	if ($this->session->flashdata('sample_stage_operation') == 3) {
        		echo '<div class="alert alert-success">Le sample stage à bien été supprimé </div>';
        	}
        ?>
        	<div class="row">
        		<div class="col-md-4 col-md-push-8">
                	<div id="datasetInfo" class="boxed-content">
                        <div class="form-group">
                            <label for="essai_name">Sélectionnez un essai :</label>
                            <div class="form-input">
                            	<?php echo form_error('essai_name', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                                <select class="selectpicker" id="essai_name" data-width="100%" data-size="5" name="essai_name" data-live-search="true" data-title="Essai">
                            	<?php
                                	foreach ($trials as $trial) {
                                        echo '<option value="' . $trial['trial_code'] . '" ' . set_select('essai_name', $trial['trial_code']) . '>' . $trial['trial_code'] . "</option>";
                                    }
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <span class="glyphicon glyphicon-question-sign"></span>Sélectionnez un essai pour actualiser les menus déroulant Sample stage et Exp_unit.
                        </div>

                        <div class="text-right">
		                    <span>
		                    	<a href="<?php echo site_url('trials/create') ?>" class="button btn-success">Créer</a>
		                    </span>
                    	</div>
                	</div>
            	</div>
                <div class="col-md-8 col-md-pull-4">
                    <div class="boxed-content">
                    	<div class="form-group">
                            <label class="" for="sample_type">Type d'échantillon :</label>
                            <?php echo form_error('sample_type', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <input id="sample_type" class="form-control" type="text" name="sample_type" value="<?php echo set_value('sample_type'); ?>" placeholder="NIRS">
                        </div>
                        <div class="form-group">
                            <label class="" for="sample_plant_code">Code plante :</label>
                            <?php echo form_error('sample_plant_code', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <input id="sample_plant_code" class="form-control" type="text" name="sample_plant_code" value="<?php echo set_value('sample_plant_code'); ?>" placeholder="G00001">
                        </div>
                        <div class="form-group">
                            <label class="" for="sample_entity">Entité :</label>
                            <?php echo form_error('sample_entity', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <select class="selectpicker" id="sample_entity" data-width="100%" data-size="5" name="sample_entity" data-live-search="true" data-title="Entité">
                        	<?php
                            	foreach ($entities as $entity) {
                                    echo '<option value="' . $entity['entity_code'] . '" ' . set_select('sample_entity', $entity['entity_code']) . '>' .  $entity['entity_code'] . ' : ' . $entity['entity_name'] . "</option>";
                                }
                            ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="" for="sample_entity_ref">Référence entité :</label>
                            <?php echo form_error('sample_entity_ref', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <input id="sample_entity_ref" class="form-control" type="text" name="sample_entity_ref" value="<?php echo set_value('sample_entity_ref'); ?>" placeholder="Leaf_number_Bottom_Up">
                        </div>
                        <div class="form-group">
                            <label class="" for="sample_entity_level">Niveau entité :</label>
                            <?php echo form_error('sample_entity_level', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            <input id="sample_entity_level" class="form-control" type="text" name="sample_entity_level" value="<?php echo set_value('sample_entity_level'); ?>" placeholder="10">
                        </div>
                        <div class="row">
                        	<div class="col-xs-12">
                            	<label class="" for="sample_stage">Stade :</label>
                            	<?php echo form_error('sample_stage', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                            </div>
                        	<div class="col-xs-9 col-sm-7 col-md-7">
	                            <div class="form-group">
	                                <select class="selectpicker selectpicker_sample_stage" id="sample_stage" data-width="100%" data-size="5" name="sample_stage" data-live-search="true" data-title="Stade">
	                                	<option value="-2" disabled>Aucun sample_stage</option>
	                            	<?php
	                                	foreach ($samples_stage as $sample_stage) {
	                                        echo '<option value="' . $sample_stage['code_st'] . '" data-id_trial="' . $sample_stage['trial_code']. '" ' . set_select('sample_stage', $sample_stage['code_st']) . '>' .  $sample_stage['st_name'] . ' : ' . $sample_stage['st_physio_stage'] . "</option>";
	                                    }
	                                ?>
	                                </select>
	                            </div>
	                        </div>
	                        <div class="col-xs-3 col-sm-5 col-md-5 text-right">
                        		<div class="dropdown">
									<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<div class="visible-xs">
								    		<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
										</div>
										<div class="hidden-xs">
								    		Gestion des stades
								    		<span class="caret"></span>
										</div>

									</button>
									<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
										<li><a data-toggle="modal" data-target="#addSampleStageModal">Ajouter</a></li>
									    <li><a data-toggle="modal" data-target="#updateSampleStageModal">Modifier</a></li>
									    <li><a data-toggle="modal" data-target="#deleteSampleStageModal">Supprimer</a></li>
									</ul>
								</div>
                        	</div>
	                    </div>

                        <div class="row">
                        	<div class="col-xs-12">
                            	<label class="" for="sample_exp_unit">Unité expérimentale :</label>
                            	<?php echo form_error('exp_unit[]', '<div class="text-danger"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ', '</div>'); ?>
                        	</div>
                        	<div class="col-xs-9 col-sm-9 col-md-9">
	                            <div class="form-group">
	                                <select class="selectpicker" id="exp_unit" data-width="100%" data-size="5" name="exp_unit[]" data-live-search="true" data-title="Unité expérimentale" multiple data-selected-text-format="count > 3">
	                                	<option value="-2" disabled>Aucun exp_unit</option>
	                            	<?php
	                                	foreach ($exps_unit as $exp_unit) {
	                                        echo '<option value="' . $exp_unit['exp_unit_id'] . '" data-id_trial="' . $exp_unit['trial_code']. '" ' . set_select('exp_unit[]', $exp_unit['exp_unit_id']) . '>'  . $exp_unit['unit_code'] . "</option>";
	                                    }
	                                ?>
	                                </select>
	                            </div>
	                        </div>
	                        <div class="col-xs-3 col-sm-3 col-md-3 text-right">
		                        <div class="dropdown">
									<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<div class="visible-xs">
								    		<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
										</div>
										<div class="hidden-xs">
								    		Action
								    		<span class="caret"></span>
										</div>
									</button>
									<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
										<li><a id="select_all_exp_unit">Tout sélectionner</a></li>
									    <li><a id="deselect_exp_unit">Tout désélectionner</a></li>
									</ul>
								</div>
							</div>
	                    </div>
                        <div class="text-right">
		                    <input type="submit" name="submit" value="Créer">
		                </div>
                    </div>
                    
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addSampleStageModal" tabindex="-1" role="dialog" aria-labelledby="addSampleStageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addSampleStageModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter un sample stage</h4>
            </div>
            <form class="contact-form" action="<?= site_url("samples/create_sample_stage") ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_add_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_add_sample_stage')['essai_name_add_sample_stage'])) {
        						echo ('<div class="text-danger error_add_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_add_sample_stage')['essai_name_add_sample_stage'].'</div>');
        					}	
        				}
        				?>
                        <label class="" for="trial">Essai :</label>
                        <select class="selectpicker selectpicker_essai" id="essai_name_add_sample_stage" data-width="100%" data-size="5" name="essai_name_add_sample_stage" data-live-search="true" data-title="Essai" data-essai_name="<?php if(isset($this->session->flashdata('add_sample_stage_values')['essai_name_sample_stage'])) {echo $this->session->flashdata('add_sample_stage_values')['essai_name_sample_stage'];} ?>">
                        	<?php
                            	foreach ($trials as $trial) {
                                    echo '<option value="' . $trial['trial_code'] . '" ' . set_select('essai_name_add_sample_stage', $trial['trial_code']) . '>' . $trial['trial_code'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
        				<?php if (count($this->session->flashdata('errors_add_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_add_sample_stage')['add_sample_stage_name'])) {
        						echo ('<div class="text-danger error_add_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_add_sample_stage')['add_sample_stage_name'].'</div>');
        					}	
        				}
        				?>

                        <label class="" for="add_sample_stage_name">Sample stage name :</label>
                        <input id="add_sample_stage_name" class="form-control" type="text" name="add_sample_stage_name" value="<?php if (isset($this->session->flashdata('add_sample_stage_values')['add_sample_stage_name'])) { echo($this->session->flashdata('add_sample_stage_values')['add_sample_stage_name']); } ?>" placeholder="D02">
                    </div>
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_add_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_add_sample_stage')['add_sample_stage_physio_stage'])) {
        						echo ('<div class="text-danger error_add_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_add_sample_stage')['add_sample_stage_physio_stage'].'</div>');
        					}	
        				}
        				?>
                        <label class="" for="add_sample_stage_physio_stage">Sample stage physio stage :</label>
                        <input id="add_sample_stage_physio_stage" class="form-control" type="text" name="add_sample_stage_physio_stage" value="<?php if (isset($this->session->flashdata('add_sample_stage_values')['add_sample_stage_physio_stage'])) { echo($this->session->flashdata('add_sample_stage_values')['add_sample_stage_physio_stage']); } ?>" placeholder="Sample stage physio stage">
                    </div>
                </div>
                <div class="modal-footer">
                	<input type="submit" name="submit" value="Créer">
                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateSampleStageModal" tabindex="-1" role="dialog" aria-labelledby="updateSampleStageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="updateSampleStageModalLabel"><span class="glyphicon glyphicon-briefcase"></span>Modifier un sample stage</h4>
            </div>
            <form class="contact-form" action="<?= site_url("samples/update_sample_stage") ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_update_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_update_sample_stage')['essai_name_update_sample_stage'])) {
        						echo ('<div class="text-danger error_update_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_update_sample_stage')['essai_name_update_sample_stage'].'</div>');
        					}	
        				}
        				?>
                        <label class="" for="trial">Essai : </label>
                        <select class="selectpicker selectpicker_essai" id="essai_name_update_sample_stage" data-width="100%" data-size="5" name="essai_name_update_sample_stage" data-live-search="true" data-title="Essai" data-essai_name="<?php if(isset($this->session->flashdata('update_sample_stage_values')['essai_name_sample_stage'])) {echo $this->session->flashdata('update_sample_stage_values')['essai_name_sample_stage'];} ?>">
                        	<?php
                            	foreach ($trials as $trial) {
                                    echo '<option value="' . $trial['trial_code'] . '" ' . set_select('essai_name_update_sample_stage', $trial['trial_code']) . '>' . $trial['trial_code'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_update_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_update_sample_stage')['update_sample_stage'])) {
        						echo ('<div class="text-danger error_update_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_update_sample_stage')['update_sample_stage'].'</div>');
        					}	
        				}
        				?>
                    	<label class="" for="trial">Sample stage : </label>
                        <select class="selectpicker selectpicker_sample_stage" id="update_sample_stage" data-width="100%" data-size="5" name="update_sample_stage" data-live-search="true" data-title="Sample stage" data-sample_stage="<?php if(isset($this->session->flashdata('update_sample_stage_values')['update_sample_stage_id'])) {echo $this->session->flashdata('update_sample_stage_values')['update_sample_stage_id'];} ?>">
                        	<option value="-2" disabled>Aucun sample_stage</option>
                    	<?php
                        	foreach ($samples_stage as $sample_stage) {
                                echo '<option value="' . $sample_stage['code_st'] . '" data-id_trial="' . $sample_stage['trial_code']. '" data-st_name="' . $sample_stage['st_name'] . '" data-st_physio_stage="' . $sample_stage['st_physio_stage'] . '"' . set_select('sample_stage', $sample_stage['code_st']) . '>' .  $sample_stage['st_name'] . ' : ' . $sample_stage['st_physio_stage'] . "</option>";
                            }
                        ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_update_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_update_sample_stage')['update_sample_stage_name'])) {
        						echo ('<div class="text-danger error_update_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_update_sample_stage')['update_sample_stage_name'].'</div>');
        					}	
        				}
        				?>
                        <label class="" for="update_sample_stage_name">Sample stage name :</label>
                        <input id="update_sample_stage_name" class="form-control" type="text" name="update_sample_stage_name" value="<?php if (isset($this->session->flashdata('update_sample_stage_values')['update_sample_stage_name'])) { echo($this->session->flashdata('update_sample_stage_values')['update_sample_stage_name']); } ?>" placeholder="D02">
                    </div>
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_update_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_update_sample_stage')['update_sample_stage_physio_stage'])) {
        						echo ('<div class="text-danger error_update_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_update_sample_stage')['update_sample_stage_physio_stage'].'</div>');
        					}	
        				}
        				?>
                        <label class="" for="update_sample_stage_physio_stage">Sample stage physio stage :</label>
                        <input id="update_sample_stage_physio_stage" class="form-control" type="text" name="update_sample_stage_physio_stage" value="<?php if (isset($this->session->flashdata('update_sample_stage_values')['update_sample_stage_physio_stage'])) { echo($this->session->flashdata('update_sample_stage_values')['update_sample_stage_physio_stage']); } ?>" placeholder="Sample stage physio stage">
                    </div>
                </div>
                <div class="modal-footer">
                	<input type="submit" name="submit" value="modifier">
                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSampleStageModal" tabindex="-1" role="dialog" aria-labelledby="deleteSampleStageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteSampleStageModalLabel"><span class="glyphicon glyphicon-remove-sign"></span>Supprimer un sample stage</h4>
            </div>
            <form class="contact-form" action="<?= site_url("samples/delete_sample_stage") ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_delete_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_delete_sample_stage')['essai_name_delete_sample_stage'])) {
        						echo ('<div class="text-danger error_delete_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_delete_sample_stage')['essai_name_delete_sample_stage'].'</div>');
        					}	
        				}
        				?>
                        <label class="" for="trial">Essai : </label>
                        <select class="selectpicker selectpicker_essai" id="essai_name_delete_sample_stage" data-width="100%" data-size="5" name="essai_name_delete_sample_stage" data-live-search="true" data-title="Essai" data-essai_name="<?php if(isset($this->session->flashdata('delete_sample_stage_values')['essai_name_sample_stage'])) {echo $this->session->flashdata('delete_sample_stage_values')['essai_name_sample_stage'];} ?>">
                        	<?php
                            	foreach ($trials as $trial) {
                                    echo '<option value="' . $trial['trial_code'] . '" ' . set_select('essai_name_delete_sample_stage', $trial['trial_code']) . '>' . $trial['trial_code'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                    	<?php if (count($this->session->flashdata('errors_delete_sample_stage'))>0) {
        					if (isset($this->session->flashdata('errors_delete_sample_stage')['delete_sample_stage'])) {
        						echo ('<div class="text-danger error_delete_sample_stage"> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> '. $this->session->flashdata('errors_delete_sample_stage')['delete_sample_stage'].'</div>');
        					}	
        				}
        				?>
                    	<label class="" for="trial">Sample stage : </label>
                        <select class="selectpicker selectpicker_sample_stage" id="delete_sample_stage" data-width="100%" data-size="5" name="delete_sample_stage" data-live-search="true" data-title="Sample stage" data-sample_stage="<?php if(isset($this->session->flashdata('delete_sample_stage_values')['delete_sample_stage_id'])) {echo $this->session->flashdata('delete_sample_stage_values')['delete_sample_stage_id'];} ?>">
                        	<option value="-2" disabled>Aucun sample_stage</option>
                    	<?php
                        	foreach ($samples_stage as $sample_stage) {
                                echo '<option value="' . $sample_stage['code_st'] . '" data-id_trial="' . $sample_stage['trial_code']. '" ' . set_select('sample_stage', $sample_stage['code_st']) . '>' .  $sample_stage['st_name'] . ' : ' . $sample_stage['st_physio_stage'] . "</option>";
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                	<input type="submit" name="submit" value="Supprimer">
                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>