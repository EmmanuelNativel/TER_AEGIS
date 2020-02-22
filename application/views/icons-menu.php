<?php
// On détermine quel onglet est actif
$item_project_status =
$item_user_status =
/*$item_group_status =*/
$item_data_status =
$item_alert_status = '';

if (!isset($user_alerts)) {
	$user_alerts = array();
}

switch (current_url()) {
	case site_url("member/access_request"):
	$item_project_status = "current-menu-item";
	break;
	case site_url("projects/new_project"):
	$item_project_status = "current-menu-item";
	break;
	/*case site_url("data_import/create_group"):
	$item_group_status = "current-menu-item";
	break*/;
	case site_url("member/profil"):
	$item_user_status = "current-menu-item";
	break;
	case site_url("member/alert"):
	$item_alert_status = "current-menu-item";
	break;
	case site_url("data_import/my_data"):
	$item_data_status = "current-menu-item";
	break;
	case site_url("ressources/import"):
	$item_data_status = "current-menu-item";
	break;
}
if ($this->uri->segment(1) =='admin') {
	$item_user_status = "current-menu-item";
}
?>
<li class="menu-item <?php echo $item_data_status; ?>">
	<div class="dropdown">
		<a class="dropdown-toggle " id="menu5" data-toggle="dropdown">
			Données
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu menu-subtab" role="menu" aria-labelledby="menu5">
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("datasets") ?>"><span class="glyphicon glyphicon-folder-open"></span>Mes données</a></li>
				<!--<li role="presentation"><a class="link-subtab" tabindex="-1" href="--><?php /*echo site_url("groups")*/?><!--">--><!--<span class="glyphicon flaticon-group"></span>Mes Groupes de partage</a></li>-->
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("ressources/import") ?>"><span class="glyphicon glyphicon-import"></span>Importation / Saisie</a></li>
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url('trials/disposiplay') ?>"><span class="glyphicon glyphicon-import"></span>Traitement / Analyse</a></li>
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("data_viewer/visualize") ?>"><span class="glyphicon glyphicon-stats"></span>Consultation</a></li>
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("visualization") ?>"><span class="glyphicon glyphicon-eye-open"></span>Visualisation</a></li>

            <!-- <li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("Treeview") ?>"><span class="glyphicon glyphicon-file"></span>Dictionnaire des variables</a></li> -->
			<li role="presentation" class="divider"></li>
            <li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("samples") ?>"><span class="glyphicon glyphicon-barcode"></span>Gestion des échantillons</a></li>
            <li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("variables") ?>"><span class="glyphicon glyphicon-file"></span>Dictionnaire des variables</a></li>
			<li role="presentation" class="divider"></li>
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("ressources") ?>">
				<span class="glyphicon glyphicon-book"></span>Toutes les ressources</a>
			</li>
		</ul>
	</div>
</li>

<li class="menu-item <?php if(current_url() == site_url('variables/display_cart')) echo "current-menu-item"; ?>">
	<a href="<?php echo site_url('variables/display_cart') ?>">
		<span id="shopping_cart_nav" class="glyphicon glyphicon-shopping-cart glyphicon-menu"></span>
		<?php
		//$nb_var = count($this->session->userdata('array_variable'));
		//if ($nb_var) {
		//	echo '<span id="notification_nb_var" class="badge">'.$nb_var.'</span>';
		//}
		?>
	</a>
</li>


<li class="menu-item <?php echo $item_alert_status; ?>">
	<div class="dropdown">
		<a class="dropdown-toggle" id="menu4" data-toggle="dropdown">
			<span class="glyphicon glyphicon-menu glyphicon-bell"></span>
			<?php
			$notify_nb = 0;
			$notifications = $this->session->userdata('notifications');
			foreach ($notifications as $notification) {
				if ($notification['was_read'] == PGSQL_FALSE) {
					$notify_nb ++;
				}
			}
			if ($notify_nb) {
				echo '<span id="notification_nb" class="badge">'.$notify_nb.'</span>';
			}
			?>
		</a>
		<ul id="notify-menu" class="dropdown-menu menu-subtab menu-right" role="menu" aria-labelledby="menu4">
			<li>
				<ul class="notification-block">
			<?php
			if ($notifications) {
				foreach ($notifications as $notification) {
					echo '<li role="presentation">';
					$this->load->view('notification', $notification);
					echo '</li><li class="divider"></li>';
				}
			}
			else {
				echo '<div class="well-lg text-center"> <p>Vous n\'avez aucune notification...</p></div>';
			}
			?>
				</ul>
			</li>
			<li role="presentation" class="divider"></li>
			<li role="presentation"><a class="link-subtab text-center" tabindex="-1" href="<?php echo site_url("users/notifications") ?>">Afficher Tout <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></a></li>
		</ul>
	</div>
</li>

<li class="menu-item <?php echo $item_user_status; ?>">
	<div class="dropdown">
		<a class="dropdown-toggle " id="menu1" data-toggle="dropdown">
			<span class="glyphicon glyphicon-menu glyphicon-user"></span><?php echo $this->session->userdata('username'); ?>
		</a>
		<ul class="dropdown-menu menu-subtab menu-right" role="menu" aria-labelledby="menu1">
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url('member/profil'); ?>"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>Profil</a></li>
			<?php if($this->session->userdata('admin') == "t") echo '<li role="presentation"><a class="link-subtab" tabindex="-1" href="'.site_url('admin/index').'"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>Administration</a></li>'; ?>
			<li role="presentation" class="divider"></li>
			<li role="presentation">
				<a class="link-subtab" tabindex="-1" href="<?php echo site_url('welcome/logout') ?>">
					<span class="glyphicon glyphicon-off" aria-hidden="true"></span>Se déconnecter
				</a>
			</li>
		</ul>
	</div>
</li>
