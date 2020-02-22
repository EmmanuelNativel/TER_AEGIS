<div class="fullwidth-block">
  <div class="container">
    <div class="contact-form">

      <?php echo form_open_multipart('data_import/#','class="contact-form" id="main-form"'); ?>
      <h2 class="section-title"><span class="glyphicon glyphicon-import"></span>Importation et/ou saisie des données</h2>

      <div class="row">
        <div class="col-md-8">
          <div class="row">
            <div class="boxed-content">
              <div class="form-group">
                <label for="select-form">Formulaire de données</label>
                <div class="form-input">
                  <select class="selectpicker form-control" id="select-form" data-width="100%" name="form" required>
                    <?php
                    echo '<option value="" data-hidden="true">Aucun formulaire sélectionné</option>';
                    echo '<optgroup data-icon="flaticon-world" label="Public uniquement">';
                    foreach ($public_forms as $key => $form) {
                      echo '<option value="'.$key.'" '.set_select('form', $key).'>'.$form."</option>";
                    }
                    echo '</optgroup>';
                    echo '<optgroup data-icon="glyphicon-eye-open" label="Accés controlés">';
                    foreach ($private_forms as $key => $form) {
                      echo '<option value="'.$key.'" '.set_select('form', $key).'>'.$form."</option>";
                    }
                    echo '</optgroup>';
                    ?>
                  </select>
                </div>
                <?php
                if(form_error('form') != NULL)
                echo '<div class="alert alert-danger" role="alert">'.form_error('form'). '</div>';
                ?>
              </div>
            </div>
          </div>

          <div id="infoBlock" class="row" >
            <div class="alert alert-info" role="alert">
              ...
            </div>
          </div>

          <div class="row">
            <div id="addTrial" class="boxed-content" hidden>
              <h4>Ajouter un Essai</h4>

              <div class="alert alert-warning">
                <span class="glyphicon glyphicon-warning-sign"></span> Attention, cette section est en développement! (instable)
              </div>

              <div class="form-group">
                <label class="sr-only" for="trial_code">Trial code</label>
                <input id="trial_code" class="form-control" type="text" name="trial_code" placeholder="Trial code... (ex: 2014_WP3_Diaphen)">
                <?php
                  if(form_error('trial_code') != NULL)
                  echo '<div class="alert alert-danger" role="alert">'.form_error('trial_code'). '</div>';
                ?>
              </div>


              <div class="form-group">
                <label class="sr-only" for="trial_description">Description</label>
                <input id="trial_description" class="form-control" type="text" name="trial_description" placeholder="Description... (max: 255 carcatères)">
              </div>

              <div class="form-group">
                <div id="trial_datepicker" class="input-group input-daterange">
                  <label class="sr-only" for="trial_starting_date">Date de début de l'essai</label>
                  <input type="text" id="trial_starting_date" name="trial_starting_date" class="form-control" value="<?php echo mdate('%Y-%m-%d', time()); ?>">
                  <span class="input-group-addon">à</span>
                  <label class="sr-only" for="trial_ending_date">Date de fin de l'essai</label>
                  <input type="text" id="trial_ending_date" name="trial_ending_date" class="form-control" value="<?php echo mdate('%Y-%m-%d', strtotime('+1 week')); ?>">
                </div>
              </div>

              <div class="row">
                <div class="col-xs-10 col-md-11">
                  <div class="form-group">
                    <label class="sr-only" for="trial_project_code">Projet</label>
                    <select class="selectpicker" id="trial_project_code" data-url="data_import/existing_project" data-width="100%" name="trial_project_code" data-live-search="true" data-title="Projet... (project_code)">
                    </select>
                  </div>
                </div>
                <div class="col-xs-2 col-md-1">
                  <button title="Nouveau projet" type="button" name="add_site" class="btn-sm btn-add"><span class="glyphicon glyphicon-plus no-padding"></span></button>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-10 col-md-11">
                  <div class="form-group">
                    <label class="sr-only" for="site_code">Site code</label>
                    <select class="selectpicker" id="site_code" data-url="data_import/existing_site" data-width="100%" name="site_code" data-live-search="true" data-title="Lieu... (site_code)">
                    </select>
                  </div>
                </div>
                <div class="col-xs-2 col-md-1">
                  <button title="Nouveau lieu" type="button" name="add_site" class="btn-sm btn-add"><span class="glyphicon glyphicon-plus no-padding"></span></button>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-10 col-md-11">
                  <div class="form-group">
                    <label class="sr-only" for="soil_code">Soil code</label>
                    <select class="selectpicker" id="soil_code" data-url="data_import/existing_soil" data-width="100%" name="soil_code" data-live-search="true" data-title="Sol... (soil_code)">
                    </select>
                  </div>
                </div>
                <div class="col-xs-2 col-md-1">
                  <button title="Nouveau sol" type="button" name="add_soil" class="btn-sm btn-add"><span class="glyphicon glyphicon-plus no-padding"></span></button>
                </div>
              </div>

              <div class="form-group">
                <label class="sr-only" for="root_depth">Profondeur des racines</label>
                <input id="root_depth" class="form-control" type="text" name="root_depth" placeholder="Profondeur des racines... (en cm)">
              </div>


                  <div class="form-group">
                    <label class="sr-only" for="nb_unit_levels">Nombre de niveaux hiérarchiques</label>
                    <input id="nb_unit_levels" class="form-control" type="text" name="nb_unit_levels" placeholder="Nombre de niveaux hiérarchiques...">
                  </div>


                  <div class="form-group">
                    <label for="checkbox_env">Evironnement controllé : </label>
                    <input id="checkbox_env" type="checkbox" value="TRUE" name="checkbox_env"/><label for="checkbox_env"><span class="ui"></span></label>
                  </div>



              <div class="form-group">
                <label class="sr-only" for="trial_comment">Commentaire</label>
                <textarea id="trial_comment" class="form-control" rows="5" placeholder="Commentaire... (max: 2000 caractères)"></textarea>
              </div>

            </div>
          </div>

          <div class="row">
            <div id="addCrop" class="boxed-content" >
              <h4>Ajouter une culture</h4>

              <div class="form-group">
                <label class="sr-only" for="cropCode">Crop code</label>
                <input id="cropCode" class="form-control" type="text" name="cropCode" placeholder="Crop code... (ex:MG)">
              </div>

              <div class="form-group">
                <label class="sr-only" for="cropName">Nom de la culture</label>
                <input id="cropName" class="form-control" type="text" name="cropName" placeholder="Nom de la culture... (ex: Miscanthus Giganteus)">
              </div>

              <div class="form-group">
                <label class="sr-only" for="levelTaxo">Rang taxonomique</label>
                <select class="selectpicker form-control" id="levelTaxo" data-width="100%" name="level_taxo" title="Rang taxonomique">
                  <option value="order">Ordre</option>
                  <option value="family">Famille</option>
                  <option value="subfamily">Sous-Famille</option>
                  <option value="genus">Genre</option>
                  <option value="species">Espèce</option>
                  <option value="subspecies">Sous-Espèce</option>
                </select>
              </div>

              <div class="form-group">
                <label class="sr-only" for="parentTaxo">Taxon parent</label>
                <select class="selectpicker form-control" id="parentTaxo" data-width="100%" name="id_parent" data-live-search="true" title="Taxon parent">
                </select>
              </div>

              <div id="cropAlert" class="alert alert-danger" role="alert" hidden>
                <span id="cropAlertMsg"></span>
              </div>

              <div class="text-right">
                <span>
                  <button id="addCropBtn" type="button" name="addCropBtn"><span class="glyphicon glyphicon-plus"></span>Ajouter</button>
                </span>
              </div>

            </div>

            <div id="addSampleStage" class="boxed-content" >
              <h4>Ajouter un stade de développement</h4>

              <div class="form-group">
                <label class="sr-only" for="stageName">Nom du stade</label>
                <input id="stageName" class="form-control" type="text" name="stageName" placeholder="Nom du stade... (ex: D1, D2)">
              </div>

              <div class="form-group">
                <div id="datepicker" class="input-group input-daterange">
                  <label class="sr-only" for="stageStartingDate">Date de début du stade</label>
                  <input type="text" id="stageStartingDate" name="stageStartingDate" class="form-control" value="<?php echo mdate('%Y-%m-%d', time()); ?>">
                  <span class="input-group-addon">à</span>
                  <label class="sr-only" for="stageEndingDate">Date de fin du stade</label>
                  <input type="text" id="stageEndingDate" name="stageEndingDate" class="form-control" value="<?php echo mdate('%Y-%m-%d', strtotime('+1 week')); ?>">
                </div>
              </div>

              <div class="form-group">
                <label class="sr-only" for="physioStage">Stade physiologique</label>
                <input id="physioStage" class="form-control" type="text" name="physioStage" placeholder="Stade physiologique... (ex: juste avant feuille drapeau)">
              </div>

              <div class="form-group">
                <label class="sr-only" for="trialCode">Code de l'essai</label>
                <select class="selectpicker form-control" id="trialCode" data-width="100%" name="trialCode" data-live-search="true" title="Code de l'essai">
                  <?php
                  echo '<option value="" data-hidden="true">Aucun essai sélectionné</option>';
                  foreach ($trials as $trial) {
                    echo '<option value="'.$trial['trial_code'].'" '.set_select('trialCode', $trial['trial_code']).'>'.$trial['trial_code']."</option>";
                  }
                  ?>
                </select>
              </div>

              <div id="stageAlert" class="alert alert-danger" role="alert" hidden>
                <span id="stageAlertMsg"></span>
              </div>

              <div class="text-right">
                <span>
                  <button id="addStageBtn" type="button" name="addStageBtn"><span class="glyphicon glyphicon-plus"></span>Ajouter</button>
                </span>
              </div>

            </div>
          </div>

          <div class="row">
            <div id="input_file_box" class="boxed-content" >
              <div class="form-group">
                <label class="control-label">Sélectionnez un fichier</label>
                <input id="userfile" name="userfile" type="file" class="form-control filestyle">
              </div>
              <?php
              if(isset($error))
              echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
              ?>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <a id="dl_link" href="<?php echo site_url('data_import/download_csv_form/'); ?>">
                    <span class="glyphicon glyphicon-floppy-disk"></span>Télécharger le formulaire (.CSV)
                  </a>
                </div>
                <div class="col-md-6">
                  <div class="text-right">
                    <span>
                      <button id="preview-btn" type="button" name="preview"><span class="glyphicon glyphicon-eye-open"></span>Prévisualiser</button>
                    </span>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div id="preview-window" class="row" >
            <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading"><h4 class='panel-title'><span class="glyphicon glyphicon-eye-open"></span>Prévisualisation</h4></div>
              <!-- Table -->
              <div class="table-responsive">
                <table id="preview-table" class="table table-hover">
                </table>
              </div>
            </div>
          </div>

        </div> <!-- end left panel -->

        <div class="col-md-4">

          <div id="datasetInfo" class="boxed-content" >

            <div class="form-group">
              <label for="selectedDataset">Sélectionnez un jeu de données</label>
              <div class="form-input">
                <select class="selectpicker" id="selectedDataset" data-width="100%" name="selectedDataset" data-live-search="true" data-title="Jeu de données...">
                  <?php
                  echo '<option value="" data-hidden="true">Aucun jeu de données sélectionné</option>';
                  foreach ($allowed_datasets as $dataset) {
                    echo '<option value="'.$dataset['dataset_id'].'" '.set_select('selectedDataset', $dataset['dataset_id']).'>'.$dataset['dataset_name']."</option>";
                  }
                  foreach ($user_datasets as $dataset) {
                    echo '<option value="'.$dataset['dataset_id'].'" '.set_select('selectedDataset', $dataset['dataset_id']).'>'.$dataset['dataset_name']."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="alert alert-info">
              <span class="glyphicon glyphicon-question-sign"></span>Vous pouvez selectionner uniquement les jeu de données sur lesquels vous possédez les droits en écriture.
            </div>

            <div class="text-right">
              <span>
                <!-- Button trigger modal -->
                <button type="button" name="preview" data-toggle="modal" data-target="#myModal">
                  <span class="glyphicon glyphicon-plus"></span>
                  Créer
                </button>
              </span>
            </div>

          </div>

          <div id="visibilityAbstractPanel" class="boxed-content">
            <div class="form-group">
              <label>Visibilité</label>
              <h3>
                <span id="badgePrivate" hidden class="label label-danger"><span class="glyphicon glyphicon-lock"></span>Privé</span>
                <!--<span id="badgeGroup" hidden class="label label-warning"><span class="glyphicon flaticon-group"></span>Groupes</span>-->
                <span id="badgePublic" class="label label-info"><span class="glyphicon flaticon-world"></span>Public</span>
              </h3>

            </div>

            <div id="teamAccess" class="form-group" hidden>
              <label>Gestion des accés aux données</label>
              <?php echo $access_table; ?>
            </div>
          </div>
        </div> <!-- end right panel -->

      </div>

      <div class="row">
        <div class="col-md-11">
          <div class="text-right">
            <span>
              <button id="import-btn" type="submit" name="preview"><span class="glyphicon glyphicon-import"></span>Valider</button>
            </span>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Création d'un jeu de donnés</h4>
      </div>

      <div class="contact-form">
        <form action="#" method="post">

          <div class="modal-body">
            <div class="form-group">
              <label class="sr-only" for="datasetName">Nom du jeu de données</label>
              <input id="datasetName" class="form-control" type="text" name="datasetName" placeholder="Nom...">
            </div>

            <div class="form-group">
              <label class="sr-only" for="datasetDescription">Description</label>
              <input id="datasetDescription" class="form-control" type="text" name="datasetDescription" placeholder="Description...">
            </div>

            <div class="form-group">
              <label class="sr-only" for="datasetType">Type du jeu de données</label>
              <div class="form-input">
                <select class="selectpicker" id="datasetType" data-live-search="true" data-width="100%" data-size="5" name="datasetType" data-title="Type du jeu de données...">
                  <?php
                    foreach ($dataset_types as $type) {
                      echo "<option value=".$type['dataset_type'].">".$type['dataset_type']."</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="visibility">Visibilité</label>
              <div class="radio">
                <input type="radio" value="0" name="visibility" id="radio1" class="css-checkbox" checked /><label for="radio1" class="css-label radGroup1"><span class="glyphicon glyphicon-lock"></span>Privé</label>
              </div>
              <div class="radio">
                <input type="radio" value="1" name="visibility" id="radio2" class="css-checkbox" /><label for="radio2" class="css-label radGroup1"><span class="glyphicon flaticon-group"></span>Groupes</label>
              </div>
              <div class="radio">
                <input type="radio" value="2" name="visibility" id="radio3" class="css-checkbox" /><label for="radio3" class="css-label radGroup1"><span class="glyphicon flaticon-world"></span>Public</label>
              </div>
            </div>

            <div id="createDatasetAlert" class="alert alert-danger" hidden>
              <span class="glyphicon glyphicon-warning-sign"></span>Oops!
            </div>
          </div>

        <div class="modal-footer">
          <button type="button" class="btn-red" data-dismiss="modal">Annuler</button>
          <button id="createDatasetBtn" type="button"><span class="glyphicon glyphicon-plus"></span>Créer</button>
        </div>

      </form>
    </div>

  </div>

</div>
</div>
