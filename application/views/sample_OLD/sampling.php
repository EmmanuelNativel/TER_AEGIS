<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Prélèvement des échantillons</li>
        </ol>
        <h2 class="section-title">Prélèvement des échantillons</h2>        
        <?php if (isset($message_ok_update) && $message_ok_update== TRUE) {
        	echo '<div class="alert alert-success">Les échantillons qui ne sont plus présents dans la liste ont bien été modifiés</div>';
        }
        if (isset($array_error) && empty($array_error) == FALSE) {
    		echo '<div class="alert alert-danger">Des echantillons n\'ont pas été enregistrés, les champs NB et Date sont obligatoires</div>';
    	}
        ?>
        <?php echo form_open('samples/sampling', 'class="contact-form"'); ?>
       		<div class="row">
                <div class="col-md-8">
                    <div class="boxed-content">
                    	<div class="row">
                        	<div class="col-xs-12">
                        		<h3>Valeurs par défaut</h3>
                        	</div>
                        	<div class="col-xs-12 col-sm-6">
                        		<div class="col-xs-12">
                        			<label for="exampleInputName2">Nombre d'objet :</label>
                        		</div>
                        		<div class="col-xs-10">
                        			<input id='defaut_nb' class='form-control' type='number' name='defaut_nb' value=''>
                        		</div>
                        		<div class="col-xs-2">
	                        		<button id="btn_defaut_nb" type="button" class="btn btn-secondary">
	                        			<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
	                        	</div>
                        	</div>
                        	<div class="col-xs-12 col-sm-6">
                        		<div class="col-xs-12">
                        			<label for="exampleInputName2">Date :</label>
                        		</div>
                        		<div class="col-xs-10">
                        			<input id='defaut_date' class='form-control' type='date' name='defaut_date' value=''>
                        		</div>
                        		<div class="col-xs-2">
	                        		<button id="btn_defaut_date" type="button" class="btn btn-secondary">
	                        			<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
	                        	</div>
                        	</div>
                    	</div>
                    </div>
                </div>
                <div class="col-md-4">
                	<div id="datasetInfo" class="boxed-content">
                        <div class="form-group">
                            <div class="row">
	                        	<div class="col-xs-12">
                            		<label for="essai_name">Sélectionnez plusieurs échantillons à prélever</label>
                            	</div>
	                        </div>
	                        <div class="row">
	                        	<div class="col-xs-offset-1">
									<button id="add" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addSampleStageModal">Ajouter</button>
								</div>
	                    	</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
            		<div class="boxed-content">
            			<h3>Liste des échantillons à prélever</h3>
                    	<div id="div_table_sample" class=" table-responsive">
                    		<table id="sample_table" class="table table-hover">
								<thead>
									<tr>
									<th>Code-barres</th>
									<th>Nombre d'objet</th>
									<th>Date de récolte</th>
									<th></th>
									</tr>
								</thead>
								<tbody id="body_sample_table">
									<?php
										foreach ($array_error as $error) {
											$row = "<tr id='".$error[0]."' class='row_sample_table'>";
											$row .= "<td>".$error[0]."</td>";
											$row .="<td> <input id='nb_".$error[0]."' class='form-control' type='number' min='0' name='nb_".$error[0]."' value='".$error[1]."' ></td>";
											$row .="<td> <input id='date_".$error[0]."' class='form-control' type='date' name='date_".$error[0]."' value='".$error[2]."'></td>";
											$row .= "<td><div> <span title='Retirer' class='close span_remove_sample' data-code_sample='".$error[0]."'><span>×</span></span></div></td>";

											$row .="</tr>";

											echo ($row);
										}
									?>
								</tbody>
				        	</table>
                    	</div>
                    	<div id="div_btn_sampling" class="col-sm-offset-10 col-xs-offset-7">
                   			<input type='submit' name='submit' value='Prélever'>
                   		</div>
               		</div>
           		</div>
       		</div>

    	</form>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addSampleStageModal" tabindex="-1" role="dialog" aria-labelledby="addSampleStageModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addSampleStageModalLabel"><span class="glyphicon glyphicon-plus-sign"></span>Ajouter des échantillons</h4>
            </div>
            <div class="modal-body">
            	<p>Cliquez sur une ligne du tableau pour choisir un échantillon</p>
            	<div class="row">
            		<div class="col-xs-12">
	            		<div id="div_table_sample" class=" table-responsive">
	                	<?php
				          if ($not_collected_sample != null) {
				            $template = array('table_open' => '<table id="table_add_sample" class="table table-hover">');
				            $this->table->set_template($template);
				            $this->table->set_heading(array('Code', 'Type', 'NB', 'Plant code', 'Entity', 'Entity ref', 'Entity level', 'Sample stage'));

				            foreach ($not_collected_sample as $sample) {

				              $this->table->add_row(array($sample['sample_code'], $sample['sample_type'], $sample['sample_nb_objects'], $sample['sample_plant_code'], $sample['sample_entity'], $sample['sample_entity_ref'], $sample['sample_entity_level'], $sample['st_name']));
				            }
				            echo $this->table->generate();
				          }
				          else {
				            echo "Tous les échantillons ont déjà été prélevés.";
				          }
				          ?>
				      	</div>
			      	</div>
			    </div>
			</div>
            <div class="modal-footer" style="text-align: left !important;">
            	<div class="col-xs-4">
					<input id="checkbox_defaut_value" type="checkbox" checked value="TRUE" name="defaut_value" />
					<label for="checkbox_defaut_value">
						<span class="ui"></span>Garder les valeurs par défaut
					</label>
				</div>

				<div class="col-xs-3 col-xs-offset-6">
            	<button type="button" id="btn_selectionner" class="button button-secondary" data-dismiss="modal" name="button"></span>Selectionner</button>
            	</div>
            </div>
        </div>
    </div>
</div>