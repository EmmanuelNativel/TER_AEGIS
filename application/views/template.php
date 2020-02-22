<?php echo doctype('html5'); ?>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1">

	<title>AEGIS - Agro-Ecological Global Information System</title>

	<!-- Loading third party fonts -->
	<link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets') ?>/fonts/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets') ?>/fonts/flaticon.css" rel="stylesheet" type="text/css">

	<!-- Loading css files -->
	<link rel="stylesheet" href="<?php echo css_url('bootstrap.min') ?>" type="text/css">
	<link rel="stylesheet" href="<?php echo css_url('selectize.bootstrap3') ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo css_url('style') ?>?v=<?=time();?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo css_url('vis') ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo css_url('checkbox') ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo css_url('checkbox_access') ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo css_url('bootstrap-datepicker.min') ?>" type="text/css">
	<link rel="stylesheet" href="<?php echo css_url('dataTables.bootstrap') ?>" type="text/css">
	<link rel="stylesheet" href="<?php echo css_url('dvc') ?>" type="text/css">




	<link rel="stylesheet" href="<?php echo css_url('query-builder.default.min') ?>" type="text/css">

	<link rel="stylesheet" href="<?php echo css_url('dataviz') ?>" type="text/css">


	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="<?php echo css_url('bootstrap-select_1.10.0.min') ?>" />


	<!-- ============================== Leaflet  ==============================-->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
    integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
    crossorigin=""/>

	<!-- Make sure you put this AFTER Leaflet's CSS -->
	 <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
	   integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
	   crossorigin=""></script>

	<!-- plugin clustering -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
	<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
	<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster-src.js"></script>



	<!-- =========== Chargement des fichiers CSS passés en paramètres =========-->
	<?php
	if (isset($stylesheets)) {
		foreach ($stylesheets as $url) {
			echo '<link rel="stylesheet" href="' . css_url($url) . '" type="text/css">';
		}
	}
	?>


	<!--[if lt IE 9]>
	<script src="<?php echo js_url('ie-support/html5') ?>"></script>
	<script src="<?php echo js_url('ie-support/respond') ?>"></script>
	<![endif]-->

	<script src="<?php echo js_url('jquery-1.12.3.min') ?>"></script>
    <script src="<?php echo js_url('jquery-ui.custom') ?>"></script>
    <script src="<?php echo js_url('jquery-ui.custom.min') ?>"></script>
    <script src="<?php echo js_url('jquery.cookie') ?>"></script>
    <!--<script src="<?php /*echo js_url('jquery-1.9.1.min') */?>"></script> -->
	<script src="<?php echo js_url('bootstrap') ?>"></script>
	<script src="<?php echo js_url('bootstrap-filestyle.min') ?>"></script>

</head>

<body>
	<div class="site-content">
		<header class="site-header collapsed-nav" data-bg-image="">
			<div class="container">

				<div class="header-bar">
					<a href="<?php echo site_url('welcome/index') ?>" class="branding">
						<img src="<?php echo img_url('logo_aegis_small.png') ?>" alt="" class="logo">
						<div class="logo-type">
							<h1 class="site-title">AEGIS</h1>
							<small class="site-description">Agro-Ecological Global Information System</small>
						</div>
					</a>

					<nav class="main-navigation">
						<ul class="menu-icons">
							<li class="menu-item">
								<button class="menu-toggle"><i class="fa fa-bars"></i></button>
							</li>
						</ul>

						<ul class="menu">
							<!-- <li class="home menu-item <?php if(current_url() == site_url('welcome/index')) echo "current-menu-item"; ?>">
								<a href="<?php echo site_url('welcome/index') ?>"><img src="<?php echo img_url('home-icon.png') ?>" alt="Home"></a>
							</li> -->
							<li class="menu-item <?php if(current_url() == site_url('welcome/about')) echo "current-menu-item"; ?>">
								<a href="<?php echo site_url('welcome/about') ?>">A propos</a>
							</li>
							<li class="menu-item <?php if(current_url() == site_url('welcome/contact')) echo "current-menu-item"; ?>">
								<a href="<?php echo site_url('welcome/contact') ?>">Contact</a>
							</li>
							<?php
								if ($this->session->userdata('connected')){
									echo $this->load->view('header-menu-on', '', TRUE);
									echo $this->load->view('icons-menu', '', TRUE);
								}
							?>
						</ul>

						<?php if (!$this->session->userdata('connected')) {
							echo $this->load->view('header-menu-off', '', TRUE);
						} ?>
					</nav>
					<div class="mobile-navigation"></div>
				</div>
			</div>
		</header> <!-- end header-content -->

		<?php echo $page_title; ?>

		<main class="main-content">
			<?php echo $page; ?>
		</main> <!-- end main-content -->

		<div class="fullwidth-block" data-bg-color="#edf2f4"></div>

		<footer class="site-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<div class="widget">
							<h3 class="widget-title">Adresse</h3>
							<strong>CIRAD</strong>
							<address>Avenue Agropolis, 34398 Montpellier Cedex 5 France</address>
							<a href="tel:+33 4 67 61 58 00">+33 4 67 61 58 00</a> <br>
							<a href="mailto:<?= AEGIS_MAIL ?>"><?= AEGIS_MAIL ?></a>
						</div>
					</div>
					<div class="col-md-3">
						<div class="widget">
							<h3 class="widget-title">Press room</h3>
							<ul class="arrow-list">
								<li><a href="#">Accusantium doloremque</a></li>
								<li><a href="#">Laudantium totam aperiam</a></li>
								<li><a href="#">Eaque ipsa quae illo inventore</a></li>
								<li><a href="#">Veritatis et quasi architecto</a></li>
								<li><a href="#">Vitae dicta sunt explicabo</a></li>
							</ul>
						</div>
					</div>
					<div class="col-md-3">
						<div class="widget">
							<h3 class="widget-title">Research summary</h3>
							<ul class="arrow-list">
								<li><a href="#">Accusantium doloremque</a></li>
								<li><a href="#">Laudantium totam aperiam</a></li>
								<li><a href="#">Eaque ipsa quae illo inventore</a></li>
								<li><a href="#">Veritatis et quasi architecto</a></li>
								<li><a href="#">Vitae dicta sunt explicabo</a></li>
							</ul>
						</div>
					</div>
					<div class="col-md-3">
						<div class="widget">
							<h3 class="widget-title">Réseaux sociaux</h3
							<div class="social-links">
								<a href="#"><i class="fa fa-facebook"></i></a>
								<a href="https://twitter.com/AEGIS83382487"><i class="fa fa-twitter"></i></a>
							</div>
						</div>
					</div>
				</div> <!-- .row -->

				<p class="colophon">AEGIS - CIRAD 2019</p>
			</div> <!-- .container -->
		</footer> <!-- end footer-content -->

		<!-- back-to-top -->
		<a id="back-to-top" href="#" class="button back-to-top only-icon" role="button" title="vers le haut de page" data-toggle="tooltip" data-placement="left"><span class="glyphicon glyphicon-chevron-up"></span></a>
		<!-- end back-to-top -->
	</div>

	<!-- JavaScript -->
	<script src="<?php echo js_url('plugins') ?>"></script>
	<script src="<?php echo js_url('app') ?>"></script>
	<script src="<?php echo js_url('notification_mgr') ?>"></script>
	<script src="<?php echo js_url('parallax') ?>"></script>
	<script src="<?php echo js_url('back-to-top') ?>"></script>
	<script src="<?php echo js_url('particleground.min') ?>"></script>
	<script src="<?php echo js_url('particleground_config') ?>"></script>
	<script>
		/**
		 * Déclare les chemins d'accés aux fichiers
		 */
		var BaseURL = "<?php echo base_url() ?>";
		var SiteURL = "<?php echo site_url() ?>";
		var CurrentURL = "<?php echo current_url() ?>";
		var ImgURL = "<?php echo img_url() ?>";

		// Active les bulles d'aides
		$('[data-toggle="tooltip"]').tooltip({delay:{'show': 500, 'hide': 100}});
	</script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="<?php echo js_url('bootstrap-select.min') ?>"></script>

	<?php
	//Liste les scripts à utiliser dans la page
	if (isset($scripts))
	{
		foreach ($scripts as $url)
		{
			echo '<script src="'.js_url($url).'"></script>';
		}
	}

	if (isset($scripts_page))
	{
		foreach ($scripts_page as $script)
		{
			echo "<script>".$script."</script>";
		}
	}
	?>

</body>

</html>
