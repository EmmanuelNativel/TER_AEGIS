<div class="fullwidth-block">
    <div class="container">

        <!-- hidden input afin de rendre accessible certaines données à jQuery -->
        <input id="hiddenDatasetId" type="hidden" value=<?= $dataset_id ?>>
        <input id="hiddenDatasetOwnerLogin" type="hidden" value=<?= $dataset_owner_login ?>>


        <ol class="breadcrumb">
          <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>

          <?php if ($project_code) {?>

              <li><a href="<?= site_url('projects') ?>">Projets</a></li>
              <li><a href="<?= site_url('projects/display/' . $project_code) ?>"> <?= $project_code ?></a></li>

          <?php } else { ?>

              <li><a href="<?= site_url('datasets/index/') ?>">Mes données</a></li>

          <?php } ?>
          <li class="active"><?= $dataset_name ?></li>
        </ol>
        <h2 class="section-title">Le jeu de données : <?= $dataset_name ?></h2>
        <div class="boxed-content">
            <dl class="dl-horizontal">
                <dt>Nom</dt>
                <dd><a href="<?= site_url('datasets/display/' . $dataset_id) ?>"><?= $dataset_name ?></a></dd>
                <dt>Type</dt>
                <dd><?= $dataset_type ?></dd>
                <dt>Description</dt>
                <dd><?= $dataset_description ?></dd>
                <dt>Crée par</dt>
                <dd><?= $dataset_owner_login ?></dd>
            </dl>
            <div class="text-right">
                <?php if ($this->session->userdata('username') == $dataset_owner_login || $permissions == ACCESS_WRITE): ?>
                    <a href="#" class="button" data-toggle="modal" data-target="#updateModal"><span
                                class="glyphicon glyphicon-pencil"></span>Modifier</a>
                <?php endif; ?>
                <?php if ($this->session->userdata('username')==$dataset_owner_login || $permissions==ACCESS_WRITE ): ?>
                    <a href="#" class="button button-danger" data-toggle="modal" data-target="#deleteModal"><span
                                class="glyphicon glyphicon-trash"></span>Supprimer</a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#members" aria-controls="members" role="tab" data-toggle="tab">Membres
                    <span class="badge badge-inverse"><?= $nb_members ?></span>
                </a>
            </li>
            <li role="presentation">

                <a href="#linkedProjects" aria-controls="linkedProjects" role="tab" data-toggle="tab">Projets associés
                    <span class="badge badge-inverse"><?= $nb_linkedProjects ?></span>
                </a>
            </li>
        </ul>
        <?php if ($this->session->userdata('username')==$dataset_owner_login || $permissions==ACCESS_WRITE): ?>
            <!-- DELETE Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteModalLabel"><span
                                        class="glyphicon glyphicon-trash"></span>Supprimer</h4>
                        </div>
                        <?= form_open('datasets/delete', 'class="contact-form"'); ?>
                        <input type="text" name="dataset_id" value="<?= $dataset_id ?>" hidden="true">
                        <div class="modal-body">
                            <p>
                                Voulez-vous vraiment supprimer ce jeu de données ?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span
                                        class="glyphicon glyphicon-remove"></span>Annuler
                            </button>
                            <button type="submit" class="button button" name="button"><span
                                        class="glyphicon glyphicon-ok"></span>Je supprime ce jeu de données
                            </button>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($this->session->userdata('username') == $dataset_owner_login || $permissions == ACCESS_WRITE): ?>
            <!-- UPDATE Modal -->
            <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="updateModalLabel"><span
                                        class="glyphicon glyphicon-pencil"></span>Modifier</h4>
                        </div>
                        <?= form_open('datasets/update', 'class="contact-form"'); ?>
                        <input type="text" name="dataset_id" value="<?= $dataset_id ?>" hidden="true">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="sr-only" for="datasetName">Nom</label>
                                <input id="datasetName" class="form-control" type="text" name="datasetName"
                                       value="<?= $dataset_name ?>" required="true" placeholder="Nom...">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="datasetType">Type</label>
                                <div class="form-input">
                                  <select class="selectpicker" id="datasetType" data-live-search="true" data-width="100%" data-size="5" name="datasetType" data-title="Type du jeu de données...">
                                      <?php
                                      foreach ($dataset_types as $type) {
                                          $selected = ($dataset_type == $type['dataset_type']) ? 'selected' : '';
                                          echo "<option value='" . $type['dataset_type'] . "' " . $selected .">" . $type['dataset_type'] . "</option>";
                                      }
                                      ?>
                                  </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="datasetDescription">Description</label>
                                <textarea id="datasetDescription" class="form-control" type="text"
                                          name="datasetDescription" placeholder="Description..."
                                          rows="1"><?= $dataset_description ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="cancelBtn" type="button" class="button button-danger" data-dismiss="modal"
                                    name="button"><span class="glyphicon glyphicon-remove"></span>Annuler
                            </button>
                            <button type="submit" class="button button" name="button"><span
                                        class="glyphicon glyphicon-ok"></span>Modifier
                            </button>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tab panes -->
        <div class="tab-content">

          <!-- Les onglets suivants vont être affichés dynamiquement grâce à Ajax  -->
          <!-- Cela permettra de créer une pagination pour chaque onglet -->

            <!--========================== TAB 1 ============================-->

            <div role="tabpanel" class="tab-pane active fade in active" id="members">

                <div class="ajaxTable"></div>

                <div class="text-right">
                    <?php if ($this->session->userdata('username') == $dataset_owner_login): ?>
                        <a href="#" class="button" data-toggle="modal" data-target="#invitModal"><span
                                    class="glyphicon glyphicon-plus"></span>Ajouter un membre</a>
                    <?php endif; ?>
                    <?php if ($is_member): ?>
                        <a href="#" class="button button-danger" data-toggle="modal" data-target="#quitModal"><span
                                    class="glyphicon glyphicon-remove"></span>Quitter</a>
                    <?php endif; ?>
                </div>
            </div>

            <!--========================== TAB 2 ============================-->

            <div role="tabpanel" class="tab-pane fade" id="linkedProjects">

              <div class="ajaxTable"></div>

            </div>

            <!--=============================================================-->

            <!-- buttons -->
            <hr>
            <div class="text-left">
              <?php
                  if ($project_code) {
                    $backButtonUrl = $previousUrl;
                    $backButtonLabel = "Retour au projet " . $project_code;
                  } else {
                    $backButtonUrl = site_url('datasets/');
                    $backButtonLabel = "Tous les jeux de données";
                  }
              ?>
                <a href="<?= $backButtonUrl ?>" class="button">
                  <span class="glyphicon glyphicon-menu-left"></span>
                  <?= $backButtonLabel ?>
                </a>
            </div>
            <!-- end buttons -->

            <!-- ////////////////////////// MODALS & SCRIPTS ////////////////////////// -->
            <?php if ($this->session->userdata('username') == $dataset_owner_login): ?>
                <!-- Modal -->
                <div class="modal fade" id="invitModal" tabindex="-1" role="dialog" aria-labelledby="invitModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="invitModalLabel"><span
                                            class="glyphicon glyphicon-envelope"></span>Ajouter</h4>
                            </div>
                            <form class="contact-form" action="<?= site_url("datasets/invit/" . $dataset_id) ?>" method="post">
                                <div class="modal-body">
                                    <p>Qui voulez-vous ajouter à rejoindre le jeu de données <strong><?= $dataset_name ?></strong> ?
                                    </p>
                                    <div class="form-group">
                                        <label class="sr-only" for="user">Utilisateurs</label>
                                        <select class="selectpicker" id="user"
                                                data-url="datasets/searched_not_dataset_members/<?= $dataset_id ?>"
                                                data-width="100%" name="user" data-live-search="true"
                                                data-title="Identifiant...">
                                            <?php if (set_value('user')) echo '<option value="' . set_value('user') . '" selected>' . set_value('user') . '</option>'; ?>
                                        </select>
                                    </div>
                                    <?php if (form_error('user')) echo form_error('user'); ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                                    <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="removeMemberModal" tabindex="-1" role="dialog" aria-labelledby="removeMemberModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="removeMemberModalLabel"><spanclass="glyphicon glyphicon-remove"></span>Supprimer un membre</h4>
                            </div>
                            <form id="removeForm" class="contact-form" action="<?= site_url("datasets/remove_member/" . $dataset_id) ?>" method="post">
                                <div class="modal-body">
                                    <p>
                                        Voulez-vous vraiment retirer <strong class="msgUserModal"></strong> de la liste des membres du jeu de données <strong><?= $dataset_name ?></strong> ?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                                    <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je supprime cet membre</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="changeStatutModal" tabindex="-1" role="dialog" aria-labelledby="changeStatutModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="changeStatutModalLabel"><span class="glyphicon flaticon-fashion"></span>Changer les droits d'un membre</h4>
                            </div>
                            <form id="statutForm" class="contact-form" action="<?= site_url("datasets/change_member_statut/" . $dataset_id) ?>" method="post">
                                <div class="modal-body">
                                    <p>
                                        Voulez-vous vraiment changer les droits de <strong class="msgUserModal"></strong>
                                        ?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler
                                    </button>
                                    <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je change les droits du membre
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($is_member) : ?>
                <!-- Modal -->
                <div class="modal fade" id="quitModal" tabindex="-1" role="dialog" aria-labelledby="quitModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="quitModalLabel"><span class="glyphicon glyphicon-remove"></span>Quitter</h4>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Voulez-vous vraiment quitter le dataset <strong><?= $dataset_name ?></strong> ?
                                </p>
                            </div>
                            <div class="modal-footer">
                                <a href="#" class="button button-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>Annuler</a>
                                <a href="<?= site_url('datasets/quit/' . $dataset_id); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Je quitte ce jeu de données</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            <script type="text/javascript">
                // Script permettant d'ajouter un élément selectionné comme une entrée
                // d'un formulaire de suppression ou de changement de statut.

                var focused_user = "";


                function focus_member(login) {
                    focused_user = login;
                    $('.msgUserModal').html(focused_user);
                    return true;
                }

                $('#removeForm').submit(function (event) {
                    $('<input />').attr('type', 'hidden')
                        .attr('name', "username")
                        .attr('value', focused_user)
                        .appendTo($(this));
                    return true;
                });

                $('#statutForm').submit(function (event) {
                    $('<input />').attr('type', 'hidden')
                        .attr('name', "username")
                        .attr('value', focused_user)
                        .appendTo($(this));
                    return true;
                });
            </script>

            <script type="text/javascript">
                //script update
                $(document).ready(function () {
                    var dataset_data = <?= json_encode(array('datasetName' => $dataset_name,
                        'datasetType' => $dataset_type,
                        'datasetDescription' => $dataset_description)) ?>;
                    $("#cancelBtn").click(function (event) {
                        $("#name").val(dataset_data.datasetName);
                        $("#type").val(dataset_data.datasetType);
                        $("#description").val(dataset_data.datasetDescription);
                    });
                });
            </script>
