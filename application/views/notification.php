<?php
  $this->load->helper(array('date'));
  $message = '';
  $url = '#';
  $color= 'inherit';
  if ($was_read == PGSQL_FALSE) $is_new = 'new';
  else                          $is_new = '';
  switch ($notification_type) {
   case PROJECT_REQUEST:
      $icon = 'glyphicon-star-empty';
      $title = 'Création de projet';
      $message = '<b>'.$sender_login.'</b> demande la validation du projet <b>'.$ressource.'</b>';
      $url = site_url('projects/display/'.$ressource);
      $color='warning';
      break;

   case PROJECT_REQUEST_ACCEPTED:
      $icon = 'glyphicon-ok';
      $title = 'Création de projet';
      $message = '<b>L\'Administrateur</b> à validé votre demande de projet <b>'.$ressource.'</b>';
      $url = site_url('projects/display/'.$ressource);
      $color='success';
      break;
   case PROJECT_REQUEST_DECLINED:
      $icon = 'glyphicon-remove';
      $title = 'Création de projet';
      $message = '<b>L\'Administrateur</b> à refusé votre demande de projet <b>'.$ressource.'</b>';
      $url = site_url('#');
      $color='danger';
      break;
   case PROJECT_INVIT:
      $icon = 'glyphicon-star-empty';
      $title = 'Invitation de projet';
      $message = '<b>'.$sender_login.'</b> vous propose de rejoindre le projet <b>'.$ressource.'</b>';
      $url = site_url('projects/display/'.$ressource);
      $color='warning';
      break;
   case PROJECT_INVIT_ACCEPTED:
      $icon = 'glyphicon-ok';
      $title = 'Invitation de projet';
      $message = '<b>'.$sender_login.'</b> à accepté(e) de rejoindre le projet <b>'.$ressource.'</b>';
      $url = site_url('projects/display/'.$ressource);
      $color='success';
      break;
   case PROJECT_INVIT_DECLINED:
      $icon = 'glyphicon-remove';
      $title = 'Invitation de projet';
      $message = '<b>'.$sender_login.'</b> à décliné(e) votre invitation à rejoindre le projet <b>'.$ressource.'</b>';
      $url = site_url('projects/display/'.$ressource);
      $color='danger';
      break;
   case DATASET_INVIT:
      $icon = 'glyphicon-star-empty';
      $title = 'Ajout aux jeux de données';
      $message = '<b>'.$sender_login.'</b> vous a ajouté à son jeu de données <b>'.$ressource.'</b>';
      $url = site_url('datasets/display/'.$ressource);
      $color='warning';
      break;
  case NEW_USER:
     $icon = 'glyphicon-user';
     $title = "Nouveau membre";
     $message = "<b>".$sender_login."</b> vient de s'inscrire sur DAPHNE";
     $url = site_url('users/display/'.$ressource);
     $color='info';
     break;
  }

?>
<a href=<?= $url ?> class="link-subtab notification <?= $is_new; ?>" data-id=<?= $id ?>>
  <div class="alert_title alert_title-<?= $color ?>"><span class="glyphicon <?= $icon ?>" aria-hidden="true"></span><?= $title; ?></div>
  <div class="small"><?= $message ?></div>
  <div class="small muted text-right"><?= nice_date($created_time, "d/m/Y H:i:s"); ?></div>
</a>
