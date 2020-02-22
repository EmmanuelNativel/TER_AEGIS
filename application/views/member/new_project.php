<div class="fullwidth-block">
  <div class="container">
    <div class="contact-form">

      <div class="row">
        <div class="alert alert-danger alert-dismissible fade in" <?php if($project_request_status == TRUE or $project_request_status == NULL) echo "hidden"; ?>>
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <span class="glyphicon glyphicon-alert"></span>
          Une erreur s'est produite pendant la transmission des données du formulaire. La demande de projet n'a pas été ajoutée!
        </div>

      </div>


      <div class="row">
        <div class="col-md-6">
          <div id="accordion" role="tablist" aria-multiselectable="true" class="boxed-content panel-group">
            <div class="panel panel-project">
              <div id="headerProject" role="tab">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#sectionProject" aria-expanded="true" aria-controls="sectionProject">
                  <h2 class="section-title">
                    <span class="glyphicon glyphicon-star-empty"></span>Projet
                  </h2>
                </a>
              </div>
              <div id="sectionProject" role="tabpanel" class="panel-collapse collapse in" aria-labelledby="headerProject">
                <form id="newProjectForm" action="<?php echo current_url(); ?>" method="post">
                  <p>
                    <label for="project_code">Nom de code (acronyme, simplifié, etc...)</label>
                    <input id="project_code" type="text" name="project_code" value="<?php echo set_value('project_code'); ?>" placeholder="Nom de code...">
                  </p>
                  <?php
                  if(form_error('project_code') != NULL)
                  echo '<div class="alert alert-danger" role="alert">'.form_error('project_code'). '</div>';
                  ?>
                  <p>
                    <label for="project_name">Nom</label>
                    <input id="project_name" type="text" name="project_name" value="<?php echo set_value('project_name'); ?>" placeholder="Nom...">
                  </p>
                  <?php
                  if(form_error('project_name') != NULL)
                  echo '<div class="alert alert-danger" role="alert">'.form_error('project_name'). '</div>';
                  ?>
                  <p>
                    <label for="project_resume">Description</label>
                    <textarea name="project_resume" id="project_resume" placeholder="Description..."><?php echo set_value('project_resume'); ?></textarea>
                  </p>
                  <?php
                  if(form_error('project_resume') != NULL)
                  echo '<div class="alert alert-danger" role="alert">'.form_error('project_resume'). '</div>';
                  ?>
                  <p>
                    <label for="coordinator">Responsable</label>
                    <input id="coordinator" type="text" name="coordinator" value="<?php echo set_value('coordinator'); ?>" placeholder="Responsable...">
                  </p>
                  <?php
                  if(form_error('coordinator') != NULL)
                  echo '<div class="alert alert-danger" role="alert">'.form_error('coordinator'). '</div>';
                  ?>
                  <p>
                    <label for="coord_company">Société affiliée</label>
                    <input id="coord_company" type="text" name="coord_company" value="<?php echo set_value('coord_company'); ?>" placeholder="Société affiliée...">
                  </p>
                  <?php
                  if(form_error('coord_company') != NULL)
                  echo '<div class="alert alert-danger" role="alert">'.form_error('coord_company'). '</div>';
                  ?>
                  <p>
                    <label for="select_partners">Partenaires</label>
    								<select multiple id="select_partners" name="select_partners[]" placeholder="Partenaires...">
    									<option value=""></option>
    									<?php
    										foreach ($list_partner as $partner) {
                          if (in_array($partner['partner_code'], $select_partners)) $status = 'selected';
                          else $status = '';
    											echo '<option '.$status.' value='.$partner['partner_code'].'>'.$partner['partner_name']."</option>";
    										}
    									?>
    								</select>
                    <a href=<?php echo '"'.site_url('DataImport/add_partner').'"'; ?>><span class="glyphicon glyphicon-plus"></span>Ajouter un Partenaire</a>
    							</p>
                  <?php
                    if(form_error('select_partners') != NULL)
                    echo '<div class="alert alert-danger" role="alert">'.form_error('select_partners'). '</div>';
                  ?>
                </form>
              </div>
            </div>

          </div>
        </div>

        <div class="col-md-6"> <!-- Start Graph -->
          <div class="boxed-content">
            <h2 class="section-title"><span class="glyphicon resize flaticon-business"></span>Représentation graphique</h2>
          </div>
        </div> <!-- End Graph -->

      </div>

      <div class="row">
        <div class='col-md-12'>
          <form action="#" method="post">
            <p class="text-right">
              <button id="newProjectFormBtn" type="button" name="submit-btn"><span class="glyphicon glyphicon-share-alt"></span>Soumettre le projet</button>
            </p>
          </form>

        </div>
      </div>


    </div>
  </div>
</div>
