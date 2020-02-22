<?php echo doctype('html5'); ?>
<html lang="fr">
  <head>
  	<meta charset="UTF-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1">

  	<title>DAPHNE Mail</title>

  	<!-- Loading third party fonts -->
  	<link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|" rel="stylesheet" type="text/css">
  	<link href="<?php echo base_url('assets') ?>/fonts/font-awesome.min.css" rel="stylesheet" type="text/css">
  	<link href="<?php echo base_url('assets') ?>/fonts/flaticon.css" rel="stylesheet" type="text/css">

  	<!-- Loading css files -->
  	<link rel="stylesheet" href="<?php echo css_url('bootstrap.min') ?>" type="text/css">
  	<link rel="stylesheet" href="<?php echo css_url('style') ?>?v=<?=time();?>" type="text/css" />

  	<!--[if lt IE 9]>
  	<script src="<?php echo js_url('ie-support/html5') ?>"></script>
  	<script src="<?php echo js_url('ie-support/respond') ?>"></script>
  	<![endif]-->

  	<script src="<?php echo js_url('jquery-1.12.3.min') ?>"></script>
  	<!-- <script src="<?php echo js_url('jquery-1.9.1.min') ?>"></script> -->
  	<script src="<?php echo js_url('bootstrap') ?>"></script>
  	<script src="<?php echo js_url('bootstrap-filestyle.min') ?>"></script>
    <style media="screen">
    html {
      margin:0;
      padding:0;
      background: url("<?= img_url('background-hd-green-blue.png') ?>") no-repeat center fixed;
      -webkit-background-size: cover; /* pour anciens Chrome et Safari */
      background-size: cover; /* version standardisée */
      }
    </style>
  </head>

  <body>
    <div class="fullwidth-block">
      <div class="container">

        <div class="panel panel-default">
          <div class="panel-body">
          <img src="<?= img_url('logo-daphne-small.png') ?>"><span>DAPHNE - DAtabase PHenotype plaNt intEgration</span>
        </div></div>

          <div class="panel panel-default">
            <div class="panel-body">
              <h2 class="section-title">Demande de validation d'un nouveau projet.</h2>

                <p>
                  <strong><?= ucfirst($username) ?></strong> solicite votre attention concenant une demande de projet.
                </p>

                <div class="well">
                  <dl class="dl-horizontal">
                    <dt>Code Projet</dt>
                    <dd><?= $project_code ?></dd>
                    <dt>Utilisateur</dt>
                    <dd><?= $username ?></dd>
                  </dl>
                </div>

              <div class="text-center">
                <a href="<?= site_url('projects/display/'.$project_code) ?>" class="button">Voir le Projet</a>
              </div>

            </div>
          </div>
      </div>
    </div>
  </body>
</html>
