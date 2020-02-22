<div class="fullwidth-block">
    <div class="container">
        <div class="row">
        	<div class="col-xs-12">
        		<div class="boxed-content">
        			<div class="row">
        				<div class="col-xs-12">
        					<h3>Recherche</h3>
        				</div>
        			</div>
        			<div class="row">
					    <div class="col-xs-7 col-sm-10">
					      <input type="text" class="form-control" id="text_search" placeholder="recherche">
					    </div>

					    <div class="col-xs-2 col-sm-1">
					    	<button id="btn_search" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
					    </div>
					    <div class="col-xs-2 col-sm-1">
					    	<button id="btn_cancel_search" class="btn btn-info"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span></button>
					    </div>
					</div>
					<div class="row">
						<div id="div_keep_filter_var" class="col-xs-5">
							<input id="keep_filter_var" type="checkbox" value="FALSE" name="defaut_value" />
							<label for="keep_filter_var">
								<span class="ui"></span>Garder le filtre
							</label>
						</div>
					</div>

        		</div>
        	</div>
        </div>

        <div class="row">
        	<div class="col-xs-12 col-sm-12 col-md-3">
        		<div class="boxed-content">
        			<div class="row">
        				<div class="col-xs-12 text-center">
        					<h3>Filtre</h3>
        				</div>
        			</div>
        			<div class="row">
	        			<div class="col-xs-12 col-sm-4 col-md-12">
	        				<div class="panel panel-default ">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_class">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Classe</h3>
								</div>
								<div id="div_panel_body_class" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<ul class="ul_filter_var" style="margin-left: 10px">
											<?php foreach ($distinct_class as $key => $class) {
												echo "<li class='li_filter_class' style='word-wrap: break-word;'>".$class['class']."</li>";
											} ?>
										</ul>
									</div>
								</div>
							</div>
	        			</div>

	        			<div class="col-xs-12 col-sm-4 col-md-12">
		        			<div class="panel panel-default">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_subclass">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Sous classe</h3>
								</div>
								<div id="div_panel_body_subclass" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<ul class="ul_filter_var" style="margin-left: 10px">
											<?php foreach ($distinct_subclass as $key => $subclass) {
												echo "<li class='li_filter_subclass disabled' style='word-wrap: break-word;'>".$subclass['subclass']."</li>";
											} ?>
										</ul>
									</div>
								</div>
							</div>
						</div>

	        			<div class="col-xs-12 col-sm-4 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_domain">
									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Domaine</h3>
								</div>
								<div id="div_panel_body_domain" class="panel-collapse collapse div_collapse_body">
									<div class="panel-body">
										<ul class="ul_filter_var" style="margin-left: 10px">
											<?php foreach ($distinct_domain as $key => $domain) {
												echo "<li class='li_filter_domain' style='word-wrap: break-word;'>".$domain['domain']."</li>";
											} ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
        	</div>

        	<div class="col-xs-12 col-md-9">
        		<div class="boxed-content">
        			<div id="res_recherche" class="text-center">
        				<p id="text_recherche" class="text-center">
        					Résultat de la recherche.
        				</p>
        			</div>
        			<p class="text-center indicateur_page"></p>
    				<div id="div_var">
	        			<?php for ($i=0; $i < 5; $i++) {
	        				$vares = $varesult[$i];
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
	    				<div class="divs_var">
	        				<div class="row">
	        					<div class="col-xs-12">
	        						<h3><a class="link_var_href" target="_blank" href="<?php echo site_url($lien_href) ?>"><?php echo $vares["variable_code"] ?></a><button class="btn btn-warning btn_add_cart_var" data-var_name="<?php echo $vares["variable_code"] ?>"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></button></h3>

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
	        			</div>
	        			<hr>
	        			<?php } ?>
        			</div>
        			<p class="text-center indicateur_page"></p>

        			<div class="text-center">
	        			<nav aria-label="Page navigation">
						  <ul class="pagination">
						  	<li id="li_page_deb"><a id="page_deb" class="btn_pagination" data-value="deb">Début</a></li>
						    <li id="li_page_-5"><a id="page_-5" class="btn_pagination" data-value="-5">-5</a></li>
						    <li id="li_page_prec">
						      <a id="page_prec" class="btn_pagination" data-value="-1" aria-label="Previous">
						        <
						      </a>
						    </li>
						    <li id="li_page_1" class="active"><a id="page_1" class="btn_pagination btn_pagination_page" data-value="1">1</a></li>
						    <li id="li_page_2"><a id="page_2" class="btn_pagination btn_pagination_page" data-value="2">2</a></li>
						    <li id="li_page_3"><a id="page_3" class="btn_pagination btn_pagination_page" data-value="3">3</a></li>
						    <li id="li_page_4"><a id="page_4" class="btn_pagination btn_pagination_page" data-value="4">4</a></li>
						    <li id="li_page_5"><a id="page_5" class="btn_pagination btn_pagination_page" data-value="5">5</a></li>
						    <li id="li_page_suiv">
						      <a id="page_suiv" class="btn_pagination" data-value="+1" aria-label="Next">
						        >
						      </a>
						    </li>
						    <li id="li_page_+5"><a id="page_+5" class="btn_pagination" data-value="+5">+5</a></li>
						    <li id="li_page_fin"><a id="page_fin" class="btn_pagination"data-value="fin">Fin</a></li>

						  </ul>
						</nav>
					</div>
        		</div>
        	</div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var varesult = <?php echo(json_encode($varesult)); ?>;
	var class_subclass_domain = <?php echo(json_encode($class_subclass_domain)); ?>;
        console.log(varesult);
</script>
