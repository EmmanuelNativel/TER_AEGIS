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
              <!--======================== new Filtre =======================-->
	        			<div class="col-xs-12 col-sm-4 col-md-12">
	        				<div class="panel panel-default ">
    								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_project">
    									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Projet</h3>
    								</div>
    								<div id="div_panel_body_project" class="panel-collapse collapse div_collapse_body">
    									<div class="panel-body fixed-panel">
    										<ul class="ul_filter_var" style="margin-left: 10px">
                          <?php foreach ($projects as $key => $project) {
                            echo "<li class='li_filter_project' style='word-wrap: break-word;'>".$project['project_code']."</li>";
                          } ?>
    										</ul>
    									</div>
    								</div>
    							</div>
	        			</div>
                <!--======================== new Filtre =======================-->
	        			<div class="col-xs-12 col-sm-4 col-md-12">
    							<div class="panel panel-default">
    								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_year">
    									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Année</h3>
    								</div>
    								<div id="div_panel_body_year" class="panel-collapse collapse div_collapse_body">
    									<div class="panel-body fixed-panel">
    										<ul class="ul_filter_var" style="margin-left: 10px">
    											<?php foreach ($years as $key => $year) {
    												echo "<li class='li_filter_year' style='word-wrap: break-word;'>".$year."</li>";
    											} ?>
    										</ul>
    									</div>
    								</div>
    							</div>
    						</div>
                <!--======================== new Filtre =======================-->
	        			<div class="col-xs-12 col-sm-4 col-md-12">
		        			<div class="panel panel-default">
    								<div class="panel-heading" data-toggle="collapse" href="#div_panel_body_factor">
    									<h3 class="panel-title"><i class="float_right glyphicon glyphicon-plus"></i>Facteurs étudiés</h3>
    								</div>
    								<div id="div_panel_body_factor" class="panel-collapse collapse div_collapse_body">
    									<div class="panel-body">
    										<ul class="factorUL">

                            <div id="selectedFactors">
                              <!-- div qui va contenir la liste des facteurs et les levels -->
                              <!-- (sera mis à jour dynamiquement via jQuery) -->
                            </div>

                            <li class="col-xs-12 factorLi factorSelect">
                              <select class="selectpicker" id="factorSelectpicker" data-live-search="true" data-width="100%" data-size="5" name="datasetType" data-title="Facteur...">
                              </select>
                            </li>


    										</ul>
    									</div>
    								</div>
    							</div>
						    </div>
              <!--======================= fin filtres =======================-->

					</div>

				</div>
        	</div>

        	<div class="col-xs-12 col-md-9">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active">
                <a href="#listResult" aria-controls="listResult" role="tab" data-toggle="tab">
                  Liste
                </a>
              </li>
              <li role="presentation">
                <a href="#geolocalisation" aria-controls="geolocalisation" role="tab" data-toggle="tab">
                  Géolocalisation
                </a>
              </li>
            </ul>

            <div class="boxed-content">
            <!-- Tab panes -->
            <div class="tab-content">

              <!--========================= new TAB ===========================-->

              <div role="tabpanel" class="tab-pane fade in active" id="listResult">
                <div id="res_recherche" class="text-center">
                  <p id="text_recherche" class="text-center">
                    Résultat de la recherche.
                  </p>
                </div>
                <p class="text-center indicateur_page"></p>
                <div id="div_trials">
                  <!-- Contiendra la vue des essais (Mis à jour grâce à jQuery) -->
                </div>
                <p id="noResultMessage" class="text-center" style="display:none">Aucun résultat...</p>

                <!--====================== Pagination ======================== -->
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
                <!--==================== Fin Pagination =======================-->
              </div>

              <!--========================= new TAB ===========================-->
              <div role="tabpanel" class="tab-pane" id="geolocalisation">

                            <div id="trialsMap"></div>


              </div>
        		</div>
        	</div>
        </div>
    </div>

    <!-- Modal pour les messages d'erreurs -->
    <div id="myModal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Alerte</h4>
          </div>
          <div class="modal-body">
            <!-- Va contenir le message d'erreur -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>
<script type="text/javascript">
	var trials = <?php echo(json_encode($trials)); ?>;
</script>
