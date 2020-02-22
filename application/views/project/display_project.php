<div class="fullwidth-block">
  <div class="container">

    <!-- hidden input afin de rendre accessible le project_code à jQuery -->
    <input id="hiddenProjectCode" type="hidden" value=<?= $project_code ?>>

    <ol class="breadcrumb">
      <li><a href="<?= site_url('ressources') ?>">Ressources</a></li>
      <li><a href="<?= site_url('projects') ?>">Projets</a></li>
      <li class="active"><?= $project_code ?></li>
    </ol>

    <h2 class="section-title"><?= ucfirst($project_name); ?></h2>

    <div class="row">
      <div class="col-lg-12">
        <div class="boxed-content">
          <dl class="dl-horizontal">
            <dt>Code projet</dt>
            <dd><a href="#"><?= $project_code; ?></a></dd>
            <dt>Nom</dt>
            <dd><?= ucfirst($project_name); ?></dd>
            <dt>Description</dt>
            <dd><?= ucfirst($project_resume); ?></dd>
            <dt>Responsable(s)</dt>
            <dd><?= ucfirst($coordinator); ?></dd>
            <dt>Organisation</dt>
            <dd><?= $coord_company; ?></dd>
          </dl>
          <div class="text-right">
            <?php if($this->session->userdata('admin') || $is_leader) : ?>
              <a href="<?= site_url('projects/update/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-pencil"></span>Modifier</a>
            <?php endif; ?>
            <?php if($this->session->userdata('admin')) : ?>
              <a href="#" class="button button-danger" data-toggle="modal" data-target="#deleteModal"><span class="glyphicon glyphicon-trash"></span>Supprimer</a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div>
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
              <a href="#members" aria-controls="members" role="tab" data-toggle="tab">
                Membres <span class="badge badge-inverse"><?= $nb_members ?></span>
              </a>
            </li>
            <li role="presentation">
              <a href="#partners" aria-controls="partners" role="tab" data-toggle="tab">
                Partenaires <span class="badge badge-inverse"><?= $nb_partners ?></span>
              </a>
            </li>
            <li role="presentation">
              <a href="#trials" aria-controls="trials" role="tab" data-toggle="tab">
                Essais <span class="badge badge-inverse"><?= $nb_trials ?></span>
              </a>
            </li>
            <li role="presentation">
              <a href="#datasets" aria-controls="datasets" role="tab" data-toggle="tab">
                Datasets <span class="badge badge-inverse"><?= $nb_datasets ?></span>
              </a>
            </li>


          </ul>

          <!-- Tab panes -->
          <div class="tab-content">

            <!-- Les onglets suivants vont être affichés dynamiquement grâce à Ajax  -->
            <!-- Cela permettra de créer une pagination pour chaque onglet -->

            <!--========================= new TAB ===========================-->

            <div role="tabpanel" class="tab-pane active fade in active" id="members">

              <div class="ajaxTable"></div>

              <div class="text-right">
                <?php if ($this->session->userdata('admin') || $is_leader): ?>
                  <a href="#" class="button" data-toggle="modal" data-target="#invitModal"><span class="glyphicon glyphicon-plus"></span>Inviter</a>
                <?php endif; ?>
                <?php if ($is_member): ?>
                  <a href="#" class="button button-danger" data-toggle="modal" data-target="#quitModal"><span class="glyphicon glyphicon-remove"></span>Quitter</a>
                <?php endif; ?>
              </div>
            </div>

            <!--========================= new TAB ===========================-->

            <div role="tabpanel" class="tab-pane fade" id="partners">

              <div class="ajaxTable"></div>

              <div class="text-right">
                <?php if ($this->session->userdata('admin') || $is_leader): ?>
                  <a href="#" class="button" data-toggle="modal" data-target="#addPartnerModal"><span class="glyphicon glyphicon-plus"></span>Ajouter</a>
                <?php endif; ?>
              </div>
            </div>

            <!--========================= new TAB ===========================-->

            <div role="tabpanel" class="tab-pane fade" id="trials">

              <div class="ajaxTable"></div>

              <div class="text-right">
                  <?php if ($this->session->userdata('admin') || $is_leader): ?>
                      <a href="#" class="button" data-toggle="modal" data-target="#addTrialModal"><span class="glyphicon glyphicon-plus"></span>Ajouter</a>
                  <?php endif; ?>
              </div>
            </div>

            <!--========================= new TAB ===========================-->

            <div role="tabpanel" class="tab-pane fade" id="datasets">

              <div class="ajaxTable"></div>


              <div class="text-right">
                <?php if ($this->session->userdata('admin') || $is_leader): ?>
                  <a href="#" class="button" data-toggle="modal" data-target="#addDatasetModal"><span class="glyphicon glyphicon-plus"></span>Ajouter</a>
                <?php endif; ?>
              </div>
            </div>

          </div>
    </div>
  </div>
</div>


<!-- buttons -->
<hr>
<div class="text-left">
  <a href="<?= site_url('projects/'); ?>" class="button"><span class="glyphicon glyphicon-menu-left"></span>Tous les projets</a>
</div>
<!-- end buttons -->

</div>
</div>

<!-- ////////////////////////// MODALS & SCRIPTS ////////////////////////// -->

<?php if($this->session->userdata('admin')) : ?>
  <!-- Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="deleteModalLabel"><span class="glyphicon glyphicon-trash"></span>Supprimer</h4>
        </div>
        <div class="modal-body">
          <p>
            Voulez-vous vraiment supprimer le projet <strong><?= $project_code ?></strong> ?
          </p>
          <div class="alert alert-danger">
            <span class="glyphicon glyphicon-warning-sign"></span><strong>Attention</strong>, La suppression du projet entraîne aussi la supression de toutes les données liées à celui-ci.
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="button button-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>Annuler</a>
          <a href="<?= site_url('projects/delete/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Je supprime ce projet</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="rejectModalLabel"><span class="glyphicon glyphicon-remove"></span>Refuser</h4>
        </div>
        <form class="contact-form" action="<?= site_url("projects/reject/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Pour quelle(s) raison(s) refusez-vous le projet <strong><?= $project_code ?></strong> ?
            </p>
            <div class="form-group">
              <label class="sr-only" for="reject_motif">Description</label>
              <textarea required class="form-control" name="reject_motif" id="reject_motif" rows=8 placeholder="Motif du refus..."><?= set_value('reject_motif'); ?></textarea>
            </div>
            <div class="alert alert-danger">
              <span class="glyphicon glyphicon-warning-sign"></span>
              <strong>Attention</strong>, Le refus du projet entraîne aussi la supression de ce dernier ainsi que de toutes les données liées à celui-ci.
              De plus, Le motifs du refus seras envoyé par email aux membres actuels du projet.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
            <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je refuse ce projet</button>
          </div>
        </form>

      </div>
    </div>
  </div>
<?php endif; ?>

<?php if ($this->session->userdata('admin') || $is_leader): ?>
  <!-- Modal -->
  <div class="modal fade" id="invitModal" tabindex="-1" role="dialog" aria-labelledby="invitModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="invitModalLabel"><span class="glyphicon glyphicon-envelope"></span>Inviter</h4>
        </div>
        <form class="contact-form" action="<?= site_url("projects/invit/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Qui voulez-vous inviter à rejoindre le projet <strong><?= $project_code ?></strong> ?
            </p>
            <div class="form-group">
              <label class="sr-only" for="user">Utilisateurs</label>
              <select class="selectpicker" id="user" data-url="projects/searched_not_project_members/<?= $project_code ?>" data-width="100%" name="user" data-live-search="true" data-title="Identifiant...">
                <?php if(set_value('user')) echo '<option value="'.set_value('user').'" selected>'.set_value('user').'</option>'; ?>
              </select>
            </div>
            <?php if(form_error('user')) echo form_error('user'); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
            <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Inviter</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addPartnerModal" tabindex="-1" role="dialog" aria-labelledby="addPartnerModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="addPartnerModalLabel"><span class="glyphicon glyphicon-briefcase"></span>Ajouter un partenaire</h4>
        </div>
        <form class="contact-form" action="<?= site_url("projects/add_partner/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Selectionnez un organisme à ajouter à la liste des partenaires du projet <strong><?= $project_code ?></strong>
            </p>
            <div class="form-group">
              <label class="sr-only" for="partner">Partenaire</label>
              <select class="selectpicker" id="partner" data-dropup-auto="false" data-url="projects/searched_not_project_partners/<?= $project_code ?>" data-size="5" data-width="100%" name="partner" data-live-search="true" data-title="Partenaire...">
                <?php if(set_value('partner')) echo '<option value="'.set_value('partner').'" selected>'.set_value('partner').'</option>'; ?>
              </select>
            </div>
            <?php if(form_error('partner')) echo form_error('partner'); ?>
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

<div class="modal fade" id="addDatasetModal" tabindex="-1" role="dialog" aria-labelledby="addDatasetModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="addDatasetModalLabel"><span class="glyphicon glyphicon-briefcase"></span>Ajouter un dataset</h4>
        </div>
        <form class="contact-form" action="<?= site_url("projects/add_dataset/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Selectionnez un Dataset à ajouter à la liste de ceux du projet <strong><?= $project_code ?></strong>

              <div class="alert alert-info">
                  <span class="glyphicon glyphicon-exclamation-sign"></span>Si vous souhaitez créer un nouveau jeu de données qui sera associé à ce projet, cliquer sur le bouton [Créer].
              </div>
            </p>
            <div class="form-group">
              <label class="sr-only" for="dataset">Dataset</label>
              <select class="selectpicker" id="dataset" data-dropup-auto="false" data-url="projects/searched_not_project_datasets/<?= $project_code ?>" data-size="5" data-width="100%" name="dataset" data-live-search="true" data-title="Dataset...">
                <?php if(set_value('dataset')) echo '<option value="'.set_value('dataset').'" selected>'.set_value('dataset').'</option>'; ?>
              </select>
            </div>
            <?php if(form_error('dataset')) echo form_error('dataset'); ?>
          </div>
          <div class="modal-footer">
            <a href="<?= site_url('datasets/create/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok-circle"></span>Créer</a>
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
          <h4 class="modal-title" id="removeMemberModalLabel"><span class="glyphicon glyphicon-remove"></span>Supprimer un membre</h4>
        </div>
        <form id="removeForm" class="contact-form" action="<?= site_url("projects/remove_member/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Voulez-vous vraiment retirer <strong class="msgUserModal"></strong> de la liste des membres du projet <strong><?= $project_code ?></strong> ?
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
            <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je supprime le membre</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="removePartnerModal" tabindex="-1" role="dialog" aria-labelledby="removePartnerModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="removePartnerModalLabel"><span class="glyphicon glyphicon-remove"></span>Supprimer un Partenaire</h4>
        </div>
        <form id="removePartnerForm" class="contact-form" action="<?= site_url("projects/remove_partner/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Voulez-vous vraiment retirer <strong class="msgPartnerModal"></strong> de la liste des partenaires du projet <strong><?= $project_code ?></strong> ?
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
            <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je supprime le partenaire</button>
          </div>
        </form>
      </div>
    </div>
  </div>

    <!-- Modal -->
    <div class="modal fade" id="addTrialModal" tabindex="-1" role="dialog" aria-labelledby="addTrialModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addTrialModalLabel"><span class="glyphicon glyphicon-briefcase"></span>Ajouter un essai</h4>
                </div>
                <form class="contact-form" action="<?= site_url("projects/add_trial/".$project_code) ?>" method="post">
                    <div class="modal-body">
                        <p>
                            Selectionnez un essai à ajouter au  projet <strong><?= $project_code ?></strong> ? <br>
                        <div class="alert alert-info">
                            <span class="glyphicon glyphicon-exclamation-sign"></span>Si un essai n'existe pas dans la liste, cliquer sur le boutton [Créer] pour ajouter un nouveau.
                        </div>

                        </p>
                        <div class="form-group">
                            <label class="sr-only" for="trial">Essais</label>
                            <select class="selectpicker" id="trial" data-dropup-auto="false" data-url="projects/searched_not_project_trials/<?= $project_code ?>" data-size="5" data-width="100%" name="trial" data-live-search="true" data-title="Essais...">
                                <?php if(set_value('trial')) echo '<option value="'.set_value('trial').'" selected>'.set_value('trial').'</option>'; ?>
                            </select>
                        </div>
                        <?php if(form_error('trial')) echo form_error('trial'); ?>
                    </div>
                    <div class="modal-footer">
                        <a href="<?= site_url('trials/create/'); ?>" class="button"><span class="glyphicon glyphicon-ok-circle"></span>Créer</a>
                        <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                        <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Sélectionner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="removeTrialModal" tabindex="-1" role="dialog" aria-labelledby="removeTrialModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="removeTrialModalLabel"><span class="glyphicon glyphicon-remove"></span>Supprimer un Essai</h4>
                </div>
                <form id="removeTrialForm" class="contact-form" action="<?= site_url("projects/remove_trial/".$project_code) ?>" method="post">
                    <div class="modal-body">
                        <p>
                            Voulez-vous vraiment retirer <strong class="msgTrialModal"></strong> de la liste des essais du projet <strong><?= $project_code ?></strong> ?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                        <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je supprime l'essai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="removeDatasetModal" tabindex="-1" role="dialog" aria-labelledby="removeDatasetModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="removeDatasetModalLabel"><span class="glyphicon glyphicon-remove"></span>Supprimer un Dataset</h4>
                </div>
                <form id="removeDatasetForm" class="contact-form" action="<?= site_url("projects/remove_dataset/".$project_code) ?>" method="post">
                    <div class="modal-body">
                        <p>
                            Voulez-vous vraiment retirer <strong class="msgDatasetModal"></strong> de la liste des datasets du projet <strong><?= $project_code ?></strong> ?
                            Cela n'entrainera pas sa suppression dans la base de données.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
                        <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je retire le dataset</button>
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
          <h4 class="modal-title" id="changeStatutModalLabel"><span class="glyphicon flaticon-fashion"></span>Changer le statut d'un membre</h4>
        </div>
        <form id="statutForm" class="contact-form" action="<?= site_url("projects/change_member_statut/".$project_code) ?>" method="post">
          <div class="modal-body">
            <p>
              Voulez-vous vraiment changer le statut de <strong class="msgUserModal"></strong> ?
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="button button-danger" data-dismiss="modal" name="button"><span class="glyphicon glyphicon-remove"></span>Annuler</button>
            <button type="submit" class="button button" name="button"><span class="glyphicon glyphicon-ok"></span>Je change le statut du membre</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if($is_member) : ?>
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
            Voulez-vous vraiment quitter le projet <strong><?= $project_code ?></strong> ?
          </p>
        </div>
        <div class="modal-footer">
          <a href="#" class="button button-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>Annuler</a>
          <a href="<?= site_url('projects/quit/'.$project_code); ?>" class="button"><span class="glyphicon glyphicon-ok"></span>Je quitte le projet</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script type="text/javascript">
// Script permettant d'ajouter un élément selectionné comme une entrée
// d'un formulaire de suppression ou de changement de statut.

//Bryan (non-auteur initial de ce code)
//Remarque : les methodes suivantes permettent de récupérer les infos de la ligne
//           sur laquelle on a cliqué afin de les afficher dans les modals et aussi
//           de les passer au serveur via des hidden-input.

var focused_partner = "";
var focused_user = "";
var focused_trial = "";
var focused_dataset = "";


function focus_partner(partner_code, partner_name) {
  focused_partner = partner_code;
  $('.msgPartnerModal').html(partner_name);
  return true;
}

function focus_member(login) {
  focused_user = login;
  $('.msgUserModal').html(focused_user);
  return true;
}

function focus_trial(trial_code) {
    focused_trial= trial_code;
    $('.msgTrialModal').html(trial_code);
    return true;
}

function focus_dataset(dataset_id, dataset_name) {
    focused_dataset= dataset_id;
    $('.msgDatasetModal').html(dataset_name);
    return true;
}

$('#removePartnerForm').submit(function(event) {
  $('<input />').attr('type', 'hidden')
  .attr('name', "partner")
  .attr('value', focused_partner)
  .appendTo($(this));
  return true;
});


$('#removeDatasetForm').submit(function(event) {
  $('<input />').attr('type', 'hidden')
  .attr('name', "dataset_id")
  .attr('value', focused_dataset)
  .appendTo($(this));
  return true;
});




$('#removeForm').submit(function(event) {
  $('<input />').attr('type', 'hidden')
  .attr('name', "username")
  .attr('value', focused_user)
  .appendTo($(this));
  return true;
});

$('#statutForm').submit(function(event) {
  $('<input />').attr('type', 'hidden')
  .attr('name', "username")
  .attr('value', focused_user)
  .appendTo($(this));
  return true;
});
</script>
