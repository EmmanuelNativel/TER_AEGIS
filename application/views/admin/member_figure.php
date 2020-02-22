<?php
/**
 * Template pour l'affichage des membres dans la section Admin/utilisateurs
 *
 * Variables du template:
 * @param String $member_status Status du membre (admin/user)
 * @param String $creation_date Date de création du compte
 * @param String $member_username Pseudo du membre
 * @param String $member_first_name Prénom du membre
 * @param String $member_last_name Nom du membre
 * @param String $member_organization Entreprise/lab/groupe du membre
 * @param String $member_email Adresse e-mail du membre
 *
 * @author Medhi Boulnemour <boulnemour.medhi@live.fr>
 */
?>

<div class="grid-sizer"></div>
<div class="gutter-sizer"></div>

<figure class="user-item feature" data-groups='["<?php if($member_status == 't') echo 'admin'; else echo 'user'; ?>"]' data-date="<?php echo $creation_date; ?>">
  <div class="row"> <!-- top member menu -->
    <div class = "col-md-3">
      <span data-toggle="tooltip" data-placement="left" data-container="body" title="STATUS: <?php if($member_status == 't') echo 'Administrateur'; else echo 'Utilisateur'; ?>"
        class="glyphicon flaticon-fashion status_crown <?php if($member_status == 't') echo 'admin'; else echo 'user'; ?>_crown"></span>
    </div>
    <div class = "col-md-6"></div>
    <div class = "col-md-3">
      <span data-login="<?php echo $member_username; ?>" data-toggle="tooltip" data-placement="right" data-container="body" title="Supprimer" class="glyphicon glyphicon-remove remove_cross member-remove-btn"></span>
    </div>
  </div> <!-- end top member menu -->

  <div class="row">
    <img src="<?php echo img_url('profile-icon.png'); ?>" alt="" />

    <div class="user-item__details"> <!-- member details -->
      <figcaption class="user-item__login"><b><?php echo $member_username; ?></b></figcaption>
      <ul>
        <li class="user-item__name"><?php echo $member_first_name.' '.$member_last_name; ?></li>
        <li class="user-item__organization"><?php echo $member_organization; ?></li>
        <li hidden class="user-item__email"><?php echo $member_email; ?></li>
      </ul>
    </div> <!-- end member details -->
  </div>

  <div class="row"> <!-- bottom member menu -->
    <div class = "col-md-3">
      <a href="mailto:<?php echo $member_email; ?>">
        <span data-toggle="tooltip" data-placement="left" data-container="body" data-original-title="<?php echo $member_email; ?>" class="glyphicon glyphicon-envelope"></span>
      </a>
    </div>
    <div class = "col-md-6"></div>
    <div class = "col-md-3" data-toggle="tooltip" data-container="body" data-placement="right" data-original-title="Plus d'informations...">
      <!-- Button trigger modal -->
      <button type="button" class="btn-transparent" data-toggle="modal" data-target="#Modal_<?php echo $member_username; ?>">
        <span class="glyphicon glyphicon-list"></span>
      </button>
    </div>
  </div> <!-- end bottom member menu -->
</figure>

<!-- Modal -->
<div class="modal fade" id=<?php echo "Modal_".$member_username; ?> tabindex="-1" role="dialog" aria-labelledby="ModalLabel_<?php echo $member_username; ?>">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Informations du compte: <?php echo $member_username; ?></h4>
      </div>
      <div class="modal-body">
        <p><span>Identifiant: </span> <?php echo $member_username; ?></p>
				<p><span>Prénom: </span> <?php echo $member_first_name; ?> </p>
				<p><span>Nom: </span> <?php echo $member_last_name; ?> </p>
				<p><span>Organisation: </span> <?php echo $member_organization ?></p>
				<p><span>E-mail: </span> <?php echo $member_email; ?></p>
				<p><span>Date d'inscription: </span> <?php echo $creation_date; ?></p>
				<p><span>Status: </span> <?php if ($member_status == 't') echo "Administrateur"; else echo "Utilisateur"; ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="alert('Cette fonction est en cours de développement...')" class="btn btn-primary">Modifier</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

