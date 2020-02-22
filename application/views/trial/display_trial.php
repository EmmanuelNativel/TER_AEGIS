<script src="https://d3js.org/d3.v5.min.js"></script>
<script type="text/javascript" src="<?php echo js_url('display_trial/dataviz/utils') ?>"></script>
<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>

            <?php if ($project_code) {?>

                <li><a href="<?= site_url('projects') ?>">Projets</a></li>
                <li><a href="<?= site_url('projects/display/' . $project_code) ?>"> <?= $project_code ?></a></li>

            <?php } else { ?>

                <li><a href="<?= site_url('trials') ?>">Essais</a></li>

            <?php } ?>
           <!-- <li><a href="<?/*= site_url('projects/display/' . $project_code) */?>"><?/*= $project_code */?></a></li>-->
            <!--<li><a href="<?/*= site_url('projects/display/' . $project_code) */?>">Essais</a></li>-->
            <li class="active"><?= $trial_code ?></li>
        </ol>
        <h2 class="section-title">Essai <?= $trial_code ?></h2>
        <div class="boxed-content">
            <div class="row">
              <div class='col-xs-12 col-md-6'>
                <dl class="dl-horizontal">
                    <dt>Essai</dt>
                    <dd><?= $trial_code ?></dd>
                    <dt>Lieu</dt>
                    <dd><?= $site_code ?></dd>
                    <dt>Description</dt>
                    <dd><?= $trial_description ?></dd>
                    <dt>Date de debut</dt>
                    <dd><?= $starting_date ?></dd>
                    <dt>Date de la fin</dt>
                    <dd><?= $ending_date ?></dd>
                    <dt>Commentaire</dt>
                    <dd><?= $commentary ?></dd>
                    <!--<dt>Projet</dt>
                    <dd><a href="<?/*= site_url('projects/display/' . $project_code) */?>"><?/*= $project_code */?></a></dd>-->
                </dl>
              </div>
              <div class='col-xs-12 col-md-6'>
                <dl class="dl-horizontal">
                    <dt>Essai irrigué</dt>
                    <dd><?= ($irrigated == PGSQL_TRUE ? "oui" : "non") ?></dd>
                    <dt>Essai fertilisé</dt>
                    <dd><?= ($fertilization == PGSQL_TRUE ? "oui" : "non") ?></dd>
                    <!--<dt>Projet</dt>
                    <dd><a href="<?/*= site_url('projects/display/' . $project_code) */?>"><?/*= $project_code */?></a></dd>-->
                </dl>
              </div>
            </div><!-- end row -->

            <div class="text-right">
                <?php if ($this->session->userdata('admin')|| $is_leader): ?>
                    <a href="<?= site_url('trials/update/'.$trial_code); ?>" class="button"><span class="glyphicon glyphicon-pencil"></span>Modifier</a>
                    <a href="#" class="button button-danger" data-toggle="modal" data-target="#deleteModal"><span class="glyphicon glyphicon-trash"></span>Supprimer</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#dataviz" aria-controls="dataviz" role="tab" data-toggle="tab">Data Visualization</a>
            </li>
            <li role="presentation">
                <a href="#dis_exp" aria-controls="dis_exp" role="tab" data-toggle="tab">Dispositif expérimental</a>
            </li>
            <li role="presentation">
                <a href="#observations" aria-controls="observations" role="tab" data-toggle="tab">Observations</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <!--========================== new TAB ============================-->

            <div role="tabpanel" class="tab-pane fade in active" id="dataviz" trial_code=<?=$trial_code?> >
                <div class="boxed-content">
                  <div class="row">
                    <div class="col col-xs-4">
                      <h3>Type de visualisation : </h3>
                      <select class="selectpicker" id="datavizSelect" data-width="100%"
                              data-size="5" data-title="Visualisation...">
                          <option value='expUnitGraph'>Graphique comparatif d'unités expérimentales</option>
                          <option value='expUnit2D'>Unités expérimentales 2D</option>
                          <option value='animatedMap'>Carte animée</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div id="datavizDiv"><!-- contiendra le dataviz selectionné --></div>
            </div>

            <!--========================== new TAB ============================-->
            <!-- Onglet Dispositif Expérimental -->
            <div role="tabpanel" class="tab-pane fade" id="dis_exp">
              <div class="boxed-content">
                <table id="table_disp_exp" class="table table-hover width_table_dataTable">
                  <thead>
                         <tr>
                            <?php foreach($dispExp_tableHeaders as $key=>$column_name): ?>
                             <th><?=$column_name?></th>
                            <?php endforeach; ?>
                         </tr>
                     </thead>
                     <tbody>
                          <?php
                          foreach($dispExp_tableRows as $key=>$row) {
                             echo "<tr>";
                              foreach($dispExp_tableHeaders as $colKey=>$colName) {
                                  echo "<td>";
                                  if (array_key_exists($colKey, $row)) {
                                      foreach($row[$colKey] as $val) {
                                        $factor_level_description = isset($dispExp_FLDescription[$val]) ? $dispExp_FLDescription[$val] : "";
                                        echo "<span data-toggle='tooltip' data-container='body' title=\"". $factor_level_description ."\">" . $val . '</span><br>';
                                      }
                                  }
                                  echo "</td>";
                              };
                              echo "</tr>";
                          }
                          ?>
                     </tbody>
                </table>
              </div>
            </div>

            <!--========================== new TAB ============================-->

            <div role="tabpanel" class="tab-pane fade" id="observations">
              <div class="boxed-content">
                <table id="table_observations" class="table table-hover width_table_dataTable">
                </table>
              </div>
            </div>

            <!--=============================================================-->
        </div>

        <hr>  <!--separator line-->
        <div class="text-left">
            <?php
                if ($project_code) {
                  $backButtonUrl = $previousUrl;
                  $backButtonLabel = "Retour au projet " . $project_code;
                } else {
                  $backButtonUrl = site_url('trials/index');
                  $backButtonLabel = "Tous les essais";
                }
            ?>
            <a href="<?= $backButtonUrl ?>" class="button"><span class="glyphicon glyphicon-chevron-left"></span><?= $backButtonLabel ?></a>
        </div>
    </div>
</div>

<?php if ($this->session->userdata('admin')|| $is_leader): ?>
<!-- DELETE Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteModalLabel"><span class="glyphicon glyphicon-trash"></span>Supprimer
                </h4>
            </div>
            <?= form_open('trials/delete', 'class="contact-form"'); ?>
            <input type="text" name="trial_code" value="<?= $trial_code ?>" hidden="true">
            <div class="modal-body">
                <p>
                    Voulez-vous vraiment supprimer cet essai ?
                </p>
                <div class="modal-footer">
                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler
                    </button>
                    <button type="submit" class="button button" name="button"><span
                                class="glyphicon glyphicon-ok"></span>Je supprime cet essai
                    </button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
