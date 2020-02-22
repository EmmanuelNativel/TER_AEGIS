<?php
/**
 * Template pour l'affichage des projets DAPHNE dans la section Projets
 *
 * Variables du template:
 * @param {String} $project_code Code du projet
 * @param {String} $project_name Nom du projet
 * @param {String} $project_description Description du projet
 * @param {String} $coordinator Nom du responsable
 * @param {String} $company Société affiliée au projet
 * @param {array} $parnters Liste des partenaires du projet
 *
 * @author Medhi Boulnemour <boulnemour.medhi@live.fr>
 */
?>

<div class="grid-sizer"></div>
<div class="gutter-sizer"></div>

<figure class="user-item feature" data-groups='["project"]'>
  <div class="row"> <!-- top project menu -->
    <div class = "col-xs-9"></div>
    <div class = "col-xs-3">
      <span data-code="<?php echo $project_code; ?>" data-toggle="tooltip" data-placement="right" data-container="body" title="Supprimer" class="glyphicon glyphicon-remove remove_cross project-remove-btn"></span>
    </div>
  </div> <!-- end top project menu -->

  <img src="<?php echo img_url('form_icon.png'); ?>" alt="" />

  <div class="user-item__details"> <!-- project details -->
    <figcaption class="user-item__code"><b><?php echo $project_code; ?></b></figcaption>
    <ul>
      <li class="user-item__name"><?php echo $project_name; ?></li>
      <li class="user-item__description" hidden><?php echo $project_description; ?></li>
      <li class="user-item__coordinator" hidden><?php echo $coordinator; ?></li>
      <li class="user-item__company"><?php echo $company; ?></li>
    </ul>
  </div> <!-- end project details -->

  <div class="row"> <!-- bottom member menu -->
    <div class = "col-xs-3">
    </div>
    <div class = "col-xs-6"></div>
    <div class = "col-xs-3" data-toggle="tooltip" data-container="body" data-placement="right" data-original-title="Plus d'informations...">
      <!-- Button trigger modal -->
      <button type="button" class="btn-transparent" data-toggle="modal" data-target="#Modal_<?php echo $project_code; ?>">
        <span class="glyphicon glyphicon-list"></span>
      </button>
    </div>
  </div> <!-- end bottom member menu -->
</figure>

<!-- Modal -->
<div class="modal fade" id=<?php echo "Modal_".$project_code; ?> tabindex="-1" role="dialog" aria-labelledby="ModalLabel_<?php echo $project_code; ?>">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h2 class="modal-title" id="myModalLabel">Projet: <?php echo $project_code; ?></h2>
      </div>
      <div class="modal-body">
        <h3><span class="glyphicon glyphicon-user g-pad"></span>Projet</h3>

        <p><span>Code Projet: </span> <?php echo $project_code; ?></p>
				<p><span>Nom du Projet: </span> <?php echo $project_name; ?> </p>
				<p><span>Description: </span> <?php echo $project_description; ?> </p>
				<p><span>Responsable: </span> <?php echo $coordinator ?></p>
				<p><span>Société affiliée: </span> <?php echo $company; ?></p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        <button type="button" onclick="alert('Cette fonction est en cours de développement...')" class="btn btn-primary">Modifier</button>
        <button type="button" onclick="alert('Cette fonction est en cours de développement...')" class="btn btn-primary">Valider</button>
      </div>
    </div>
  </div>
</div>
