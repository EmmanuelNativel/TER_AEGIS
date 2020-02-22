<div class="fullwidth-block">
    <div class="container">
        <div class="row">
        	<div class="col-xs-12">
        		<div class="boxed-content">
        			<div class="row">
        				<div class="col-xs-12">
        					<h3 class="text-center"><?php echo $real_variable_code; ?></h3>
        				</div>
        			</div>
        			<div class="row">
        				<div class="col-xs-12">
        					<div class="panel panel-default">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_variable">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Variable</h3>
								</div>
								<div id="div_panel_body_variable" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<div class="row">
											<div class="col-xs-12 col-sm-3">
												<label>Classe : </label> <?php echo $info_without_onto["class"] ?>
											</div>
											<div class="col-xs-12 col-sm-3">
												<label>Sous classe : </label> <?php echo $info_without_onto["subclass"] ?>
											</div>
											<div class="col-xs-12 col-sm-3">
												<label>Domaine : </label> <?php echo $info_without_onto["domain"] ?>
											</div>
											<div class="col-xs-12 col-sm-3">
												<label>Auteur : </label> <?php echo $info_without_onto["author"] ?>
											</div>
										</div>
										<hr>

										<div class="row">
											<div id="div_var_onto">
												<?php if ($var_onto == null) { ?>
												<div class="col-xs-12 text-center">
													Aucune ontologie n'est présente dans la base de données
												</div>
										<?php } else { 
													foreach ($var_onto as $key => $value) { ?>
														<div class="div_var_onto">
															<div class="col-xs-12 col-sm-6">
																<label>Code ontologie : </label> <?php echo $value["ontology_code"] ?>
															</div>
															<div class="col-xs-12 col-sm-6">
																<label>Nom ontologie : </label > <?php echo $value["ontology_name"] ?>
															</div>
														</div>
											<?php }
												}
												if (count($var_onto) > 1) {?>
													<div class="col-xs-12 text-center">
														<a id="show_more_less_var_onto" class="cursor_hover">Afficher plus</a>
													</div>
										<?php }
												?>
											</div>
										</div>						
									</div>
								</div>
							</div>
        				</div>

        				<div class="col-xs-12 col-sm-6">
        					<div class="panel panel-default ">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_trait_onto">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Trait</h3>
								</div>
								<div id="div_panel_body_trait_onto" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<div class="row">
											<div class="col-xs-12">
												<label>Code : </label> <?php echo $info_without_onto["trait_code"] ?>
											</div>
											<div class="col-xs-12">
												<label>Nom : </label> <?php echo $info_without_onto["trait_name"] ?>
											</div>
											<div class="col-xs-12">
												<label>Description : </label> <?php echo $info_without_onto["trait_description"] ?>
											</div>
											<div class="col-xs-12">
												<label>Trait cible : </label> <?php echo $info_without_onto["trait_target"] ?>
											</div>
											<div class="col-xs-12">
												<label>Auteur: </label> <?php echo $info_without_onto["trait_author"] ?>
											</div>
										</div>
										<hr>

										<div class="row">
											<div id="div_trait_onto">
												<?php if ($trait_onto == null) { ?>
												<div class="col-xs-12 text-center">
													Aucune ontologie n'est présente dans la base de données
												</div>
										<?php } else {
													foreach ($var_onto as $key => $value) { ?>
														<div class="div_trait_onto">
															<div class="col-xs-12 col-sm-6">
																<label>Code ontologie : </label> <?php echo $trait_onto["ontology_code"] ?>
															</div>
															<div class="col-xs-12 col-sm-6">
																<label>Nom ontologie : </label > <?php echo $trait_onto["ontology_name"] ?>
															</div>
														</div>
											<?php }
												}
												if (count($trait_onto) > 1) {?>
													<div class="col-xs-12 text-center">
														<a id="show_more_less_trait_onto" class="cursor_hover">Afficher plus</a>
													</div>
										<?php } ?>
											</div>
										</div>	
									</div>
								</div>
							</div>
        				</div>

        				<div class="col-xs-12 col-sm-6">
        					<div class="panel panel-default ">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_entity_onto">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Entité</h3>
								</div>
								<div id="div_panel_body_entity_onto" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<div class="row">
											<div class="col-xs-12">
												<label>Code : </label> <?php echo $info_without_onto["entity_code"] ?>
											</div>
											<div class="col-xs-12">
												<label>Nom : </label> <?php echo $info_without_onto["entity_name"] ?>
											</div>
											<div class="col-xs-12">
												<label>Définition : </label> <?php echo $info_without_onto["entity_definition"] ?>
											</div>
										</div>
										<hr>

										<div class="row">
											<div id="div_entity_onto">
												<?php if ($entity_onto == null) { ?>
												<div class="col-xs-12 text-center">
													Aucune ontologie n'est présente dans la base de données
												</div>
										<?php } else { 
													foreach ($entity_onto as $key => $value) { ?>
														<div class="div_entity_onto">
															<div class="col-xs-12 col-sm-6">
																<label>Code ontologie : </label> <?php echo $value["ontology_code"] ?>
															</div>
															<div class="col-xs-12 col-sm-6">
																<label>Nom ontologie : </label > <?php echo $value["ontology_name"] ?>
															</div>
														</div>
											<?php }
												}
												if (count($entity_onto) > 1) {?>
													<div class="col-xs-12 text-center">
														<a id="show_more_less_entity_onto" class="cursor_hover">Afficher plus</a>
													</div>
										<?php }
												?>
											</div>
										</div>

									</div>
								</div>
							</div>
        				</div>
        			</div>

        			<div class="row">
        				<div class="col-xs-12 col-sm-6">
        					<div class="panel panel-default">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_methode_onto">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Méthode</h3>
								</div>
								<div id="div_panel_body_methode_onto" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<div class="row">
											<div class="col-xs-12">
												<label>Code : </label> <?php echo $info_without_onto["method_code"] ?>
											</div>
											<div class="col-xs-12">
												<label>Nom : </label> <?php echo $info_without_onto["method_name"] ?>
											</div>
											<div class="col-xs-12">
												<label>Classe : </label> <?php echo $info_without_onto["method_class"] ?>
											</div>
											<div class="col-xs-12">
												<label>Sous class : </label> <?php echo $info_without_onto["method_subclass"] ?>
											</div>
											<div class="col-xs-12">
												<label>Description : </label> <?php echo $info_without_onto["method_description"] ?>
											</div>
											<div class="col-xs-12">
												<label>Formule : </label> <?php echo $info_without_onto["method_formula"] ?>
											</div>
											<div class="col-xs-12">
												<label>Référence : </label> <?php echo $info_without_onto["method_reference"] ?>
											</div>
											<div class="col-xs-12">
												<label>Type : </label> <?php echo $info_without_onto["method_type"] ?>
											</div>
											<div class="col-xs-12">
												<label>Content type : </label> <?php echo $info_without_onto["content_type"] ?>
											</div>
											<div class="col-xs-12">
												<label>Auteur: </label> <!-- <?php echo $info_without_onto["content_type"] ?>  Manque l'auteur-->
											</div>
										</div>
										<hr>
										<div class="row">
											<div id="div_method_onto">
												<?php if ($method_onto == null) { ?>
												<div class="col-xs-12 text-center">
													Aucune ontologie n'est présente dans la base de données
												</div>
										<?php } else { 
													foreach ($method_onto as $key => $value) { ?>
														<div class="div_method_onto">
															<div class="col-xs-12 col-sm-6">
																<label>Code ontologie : </label> <?php echo $value["ontology_code"] ?>
															</div>
															<div class="col-xs-12 col-sm-6">
																<label>Nom ontologie : </label > <?php echo $value["ontology_name"] ?>
															</div>
														</div>
											<?php }
												}
												if (count($method_onto) > 1) {?>
													<div class="col-xs-12 text-center">
														<a id="show_more_less_method_onto" class="cursor_hover">Afficher plus</a>
													</div>
										<?php }
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
        				</div>

        				<div class="col-xs-12 col-sm-6">
        					<div class="panel panel-default">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_scale_onto">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Scale</h3>
								</div>
								<div id="div_panel_body_scale_onto" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<div class="row">
											<div class="col-xs-12">
												<label>Code : </label> <?php echo $info_without_onto["method_code"] ?>
											</div>
											<div class="col-xs-12">
												<label>Nom : </label> <?php echo $info_without_onto["method_name"] ?>
											</div>
											<div class="col-xs-12">
												<label>Type : </label> <?php echo $info_without_onto["method_class"] ?>
											</div>
											<div class="col-xs-12">
												<label>Niveau : </label> <?php echo $info_without_onto["method_class"] ?>
											</div>
										</div>
										<hr>
										<div class="row">
											<div id="div_scale_onto">
												<?php if ($scale_onto == null) { ?>
												<div class="col-xs-12 text-center">
													Aucune ontologie n'est présente dans la base de données
												</div>
										<?php } else { 
													foreach ($scale_onto as $key => $value) { ?>
														<div class="div_scale_onto">
															<div class="col-xs-12 col-sm-6">
																<label>Code ontologie : </label> <?php echo $value["ontology_code"] ?>
															</div>
															<div class="col-xs-12 col-sm-6">
																<label>Nom ontologie : </label > <?php echo $value["ontology_name"] ?>
															</div>
														</div>
											<?php }
												}
												if (count($scale_onto) > 1) {?>
													<div class="col-xs-12 text-center">
														<a id="show_more_less_scale_onto" class="cursor_hover">Afficher plus</a>
													</div>
										<?php }
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
        				</div>
        			</div>
    			</div>
			</div>
		</div>
	</div>
</div>