<div class="fullwidth-block">
  <div class="container">
    <div class="contact-form">

      <div class="col-md-2"></div>
      <div class="col-md-8">

        <div class="row">
          <h2 class="section-title"><span class="glyphicon glyphicon-import"></span>Résultats d'importation</h2>
        </div>

        <div class="row">
        <?php
          if($tx_success == 0) {
            echo "<div class='alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> Echec de l'importation des données.</div>";
          }
          elseif ($tx_success == 100) {
            echo "<div class='alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> Importation des données terminé avec succès</div>";
          }
          elseif ($tx_success > 0 && $tx_success < 100) {
            echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-warning-sign' aria-hidden='true'></span> Attention, il y a des erreurs dans le fichier de soumissions. Cependant le fichier a été importé partiellement</div>";
          }
        ?>
        </div>

        <div class="row">
          <div class="boxed-content">
            <p>
              <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#collapseInfo" aria-expanded="false" aria-controls="collapseInfo">
                Informations
              </button>
            </p>
            <div class="collapse" id="collapseInfo">
                <div class="alert alert-info">
                  <p>
                    Données importée(s) à <?php echo $tx_success; ?> %
                  </p>
                  <p>
                    Nombre de ligne(s) traitée(s) : <?php echo $total_lines; ?>
                  </p>
                  <p>
                    Nombre de ligne(s) importée(s) : <?php echo $nb_success_lines; ?>
                  </p>
                  <p>
                    Nombre de ligne(s) erronée(s) : <?php echo $nb_error_lines; ?>
                  </p>
                </div>
            </div>
            <p>
              <button class="btn btn-danger" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Erreur(s) <span class="badge"><?php echo $nb_error_lines; ?></span>
              </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="alert alert-danger">
                  <?php
                  if ($nb_error_lines != 0) {
                    foreach ($error_lines as $line) {
                      echo "<p>CSV ligne : ".$line['csv_line_index'];
                      echo trim($line['error_msg'])."</p>";
                      echo "--------------------------------------";
                    }
                  } else {
                    echo "Aucune erreur d'importation.";
                  }

                  ?>
                </div>
            </div>

          </div>
        </div>

      </div>

    </div>
  </div>
</div>
