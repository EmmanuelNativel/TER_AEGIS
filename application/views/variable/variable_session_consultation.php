<div class="fullwidth-block">
    <div class="container">
    	<div class="row">
        	<div class="col-xs-12">
        		<div class="boxed-content">
        			<div class="row">
        				<div class="col-xs-12">
        					<h3 class="text-center">Pour ajouter des variables dans votre panier rendez-vous sur le <a href="<?php echo site_url('variables/consultation') ?>">catalogue des varaiables</a></h3>
        				</div>
        			</div>
        		</div>
        	</div>
        </div>
        <div class="row">
        	<div class="col-xs-12 col-sm-9">
        		<div class="boxed-content">
        			<div class="row">
        				<div id="div_variables_session" class="col-xs-12">
    					<?php if($this->session->userdata('array_variable') == null || count($this->session->userdata('array_variable')) == 0){
    						echo "<h3 class='text-center'>Aucune variable dans votre panier</h3>";
    					}else{ ?>
	        			<?php foreach ($data_var as $key => $vares) { 
	        				$var_code = $vares['variable_code'];
	        				$without_slash = explode("/", $var_code);
	        				$encode_var_url = '';
	        				if (!empty($without_slash)) {
	        					foreach ($without_slash as $key => $value) {
	        						$encode_var_url .= "/".urlencode($value);
	        					}	
	        				}else{
	        					$encode_var_url = urlencode($var_code);
	        				}
	        				
	        				$lien_href = "variables/consultation".$encode_var_url;
	        				?>
	        				<div id="<?php echo $vares['variable_code']; ?>">
		        				<div class="row">
			        				<div class="col-xs-12">
			        					<h3><a class="link_var_href" target="_blank" href="<?php echo site_url($lien_href) ?>"><?php echo $vares["variable_code"] ?></a><button class="btn btn-info btn_remove_var_session" data-var_name="<?php echo $vares["variable_code"] ?>"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span></button></h3>
			        				</div>
			        				<div class="col-xs-12 col-md-6">
		        						<dl class="dl-horizontal">
											<dt>Classe</dt>
											<dd><?php echo $vares["class"] ?></dd>
											<dt>Sous classe</dt>
											<dd><?php echo $vares["subclass"] ?></dd>
											<dt>Domaine</dt>
											<dd><?php echo $vares["domain"] ?></dd>
											<dt>Nom trait</dt>
											<dd><?php echo $vares["trait_name"] ?></dd>
											<dt>Entité trait</dt>
											<dd><?php echo $vares["trait_entity"] ?></dd>
											<dt>Trait cible</dt>
											<dd><?php echo $vares["trait_target"] ?></dd>
										</dl>
		        					</div>
		        					<div class="col-xs-12 col-md-6">
		        						<dl class="dl-horizontal">
											<dt>Classe méthode</dt>
											<dd><?php echo $vares["method_class"] ?></dd>
											<dt>Nom méthode</dt>
											<dd><?php echo $vares["method_name"] ?></dd>
											<dt>Formule</dt>
											<dd><?php echo $vares["method_formula"] ?></dd>
											<dt>Unité</dt>
											<dd><?php echo $vares["scale_name"] ?></dd>
											<dt>Type d'unité</dt>
											<dd><?php echo $vares["scale_type"] ?></dd>
											<dt>Auteur</dt>
											<dd><?php echo $vares["author"] ?></dd>
										</dl>
		        					</div>
		        				</div>
		        				<hr>
		        			</div>
	        			<?php }
	        			} ?>
	        			</div>
        			</div>
        		</div>
    		</div>
    		<div class="col-xs-12 col-sm-3 position_sticky">
            	<div class="boxed-content">
            		<div class="row">
            			<div class="col-xs-12">
            				<?php echo form_open('variables/create_excel_file', 'class="contact-form"'); ?>
								<p id="p_btn_extract_error" class="text-danger"></p>
	            				<button type="submit" id="btn_generate_excel_file" class="btn btn-primary btn-block">Ficher Excel</button>        						
            				</form>
            			</div>
            		</div>
            	</div>
            </div>
		</div>
	</div>
</div>