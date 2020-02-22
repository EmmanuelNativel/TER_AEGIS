<?php
// On détermine quel onglet est actif
$item_project_status =
$item_user_status =
//$item_group_status =
$item_dataset_status =
$item_data_status =
$item_alert_status = '';

if (!isset($user_alerts)) {
	$user_alerts = array();
}

switch (current_url()) {
	case site_url("member/access_request"):
		$item_project_status = "current-menu-item";
		break;
	case site_url("projects/create"):
		$item_project_status = "current-menu-item";
		break;
	case site_url("data_import/create_group"):
		$item_group_status = "current-menu-item";
		break;
	case site_url("member/profil"):
		$item_user_status = "current-menu-item";
		break;
	case site_url("member/alert"):
		$item_alert_status = "current-menu-item";
		break;
	case site_url("data_import/my_data"):
		$item_data_status = "current-menu-item";
		break;
	case site_url("data_import"):
		$item_data_status = "current-menu-item";
		break;
}
if ($this->uri->segment(1) =='admin') {
	$item_user_status = "current-menu-item";
}
?>

<li class="menu-item <?php echo $item_project_status; ?>">
	<div class="dropdown">
		<a class="dropdown-toggle " id="menu2" data-toggle="dropdown">
			Projets
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu menu-subtab" role="menu" aria-labelledby="menu2">
			<?php
				foreach ($this->ProjectMember_model->get_user_projects($this->session->userdata('username')) as $project) {
					echo '<li role="presentation">
								<a class="link-subtab" tabindex="-1" href="'.site_url("projects/display/".$project["project_code"]).'">
									<span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
									'.$project["project_code"].'
								</a>
								</li>';
				}
			?>
			<li role="presentation" class="divider"></li>
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("projects") ?>"><span class="glyphicon glyphicon-star" aria-hidden="true"></span>Tous les projets</a></li>
			<li role="presentation" class="divider"></li>
			<li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url("projects/create") ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Créer un projet</a></li>
		</ul>
	</div>
</li>
