<div class="fullwidth-block">
    <div class="container">
        <?php if (empty($lignesErrors) && empty($messageErrors)){ ?>
        <div class="alert alert-success">
            <strong>Importation réussie !</strong> Les données d'unité expérimentale ont été importées avec succès!

        </div>
		
		<?php } elseif (!empty($messageErrors)){ ?>
            <div class="alert alert-danger">
                <strong>L'importation a échoué !</strong><br>
				<?php foreach ($messageErrors as $messageError){?>
                    Erreur : <strong><?php echo $messageError ?></strong> </br>
				<?php }
				?>
            </div>
		
        <?php } else { ?>
            <div class="alert alert-danger">
                <strong>L'importation des lignes suivantes a échoué !</strong><br>
                <?php
                foreach ($lignesErrors as $ligne){ ?>
                    Ligne : <strong><?php echo $ligne ?></strong> </br>
                <?php }
                ?>
            </div>
        <?php } ?>
    </div>
</div>
