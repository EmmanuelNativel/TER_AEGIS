<div class="fullwidth-block">
  <div class="container">
    <?php if($from_project) : ?>
    <?= form_open_multipart('datasets/create/'.$project_code, 'class="contact-form"'); ?>
      <?php else : ?>
      <?= form_open_multipart('datasets/create', 'class="contact-form"'); ?>
        <?php endif; ?>
        <h2 class="section-title">Nouveau jeu de données <?php if($from_project) echo 'pour le projet ' . $project_code; ?></h2>
        <div class="row">
          <div class="col-md-8">
            <div class="boxed-content center">
              <p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>Champs obligatoires</p>
              <div class="contact-form">
                <div class="form-group has-feedback">
                  <label class="sr-only" for="datasetName">Nom du jeu de données</label>
                  <?php echo form_error('datasetName'); ?>
                  <input id="datasetName" class="form-control" type="text" name="datasetName" placeholder="Nom...">
                  <span class="glyphicon glyphicon-exclamation-sign form-control-feedback text-danger"></span>
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
                            echo "<option value='" . $type['dataset_type'] . "'>" . $type['dataset_type'] . "</option>";}
                        ?>
                    </select>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12">
            <div class="boxed-content">
              <label for="visibility">Visibilité</label>

              <div class="radio">
                <input data-target="#memberRightsPanel.in" data-toggle="collapse" type="radio" value="0" name="visibility" id="radio1" class="css-checkbox" checked/><label for="radio1" class="css-label radGroup1"><span class="glyphicon glyphicon-lock"></span> Privé (Moi uniquement)</label>
              </div>
              <div class="radio">
                <input data-target="#memberRightsPanel:not(.in)" data-toggle="collapse" type="radio" value="1" name="visibility" id="radio2" class="css-checkbox" /><label for="radio2" class="css-label radGroup1"><span class="glyphicon flaticon-group"></span> Partagé</label>
              </div>
              <div class="radio">
                <input data-target="#memberRightsPanel.in" data-toggle="collapse" type="radio" value="2" name="visibility" id="radio3" class="css-checkbox" /><label for="radio3" class="css-label radGroup1"><span class="glyphicon flaticon-world"></span> Public</label>
              </div>
            </div>
          </div>
        </div>
        <div id="memberRightsPanel" class="row collapse">
          <div class="col-md-12">
            <div class="boxed-content">

              <h1>Gestion des droits d'accès</h1>

              <h2>Résumé</h2>

              <!-- Ce div contiendra l'affichage des membres sélectionnés (généré dynamiquement grâce à jQuery) -->
              <div id="selectedMembersDiv">
                <p>Aucun utilisateur sélectionné.</p>
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

              <p><h2>Sélection</h2></p>

              <p><h3>Sélectionner un utilisateur</h3></p>
              <div class="row">
                  <div class="col col-xs-6 col-sm-6 col-md-6" style="padding-right:0 !important;">
                    <select class="selectpicker" id="selectUser"
                            data-url="datasets/searched_not_dataset_members"
                            name="selectUser" data-live-search="true"
                            data-width="100%"
                            data-title="Identifiant...">
                        <?php if (set_value('user')) echo '<option value="' . set_value('user') . '" selected>' . set_value('user') . '</option>'; ?>
                    </select>
                  </div>

                  <div class="col col-xs-3 col-sm-3 col-md-2 text-center" id="rightsSwitchOneUser_container">
                    <div id="rightsSwitchOneUser" class="btn-group rightsSwitch" name='rightsSwitchOneUser'>
                      <a class="btn btn-primary btn-sm active" data-toggle="rsOne" data-title="r">Lecture</a>
                      <a class="btn btn-primary btn-sm notActive" data-toggle="rsOne" data-title="w">Ecriture</a>
                    </div>
                    <input type="hidden" name="rsOne" value="r" id="rsOne">
                  </div>

                  <div class="col col-xs-1 col-sm-1 col-md-1 text-center" style="padding:0 !important;">
                    <button id="addOneUser" type="button" class="btn btn-primary" name="button">Ajouter</button>
                  </div>
                </div>

              <p><h3>Sélectionner par projet</h3></p>

              <!-- Barre de recherche groupes -->
              <div class="row">
                <div class="col-xs-12 col-md-6 form-group has-feedback" style="padding-right:0 !important;">
                  <input type="text" class="form-control" name="search_text" id="input_search_groups" placeholder="Rechercher un projet..."/>
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>


              <div class="row">

                <div class="col-md-6" id='groupsMembersContainer'>
                  <div id="loaderContainer">
                    <div id="groupSearchLoader">
                      <span></span>
                      <span></span>
                      <span></span>
                    </div>
                  </div>
                  <div id="groupsMembers">
                    <!-- Ce div va contenir tous les panels pour la séléction de membres par groupe -->
                  </div>
                </div>


              </div>

              <div class="row text-center">
                <div class="col-md-6">
                  <div id='pagination'></div>
                </div>
              </div>


            </div>
          </div>
        </div>

        <input id="hiddenSelectedMembers" type="hidden" name="selectedMembers" value="">

        <div class="row text-center">
          <input type="submit" name="submit" value="Créer">
        </div>
        <?= form_close() ?>
  </div>
</div>
