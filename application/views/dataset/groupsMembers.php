<!-- Template utilisé lors de la création de nouveau datasets pour choisir
     les droits des utilisateurs -->

<div class="panel-group">

  <!--  Affichage de tous les groupes  -->

  <?php
      $group_number = 0;
      foreach ($projectsMembers as $panel_project_code => $projetMembers) {
        $isCurrentProject = ($panel_project_code == $project_code);
        $highlight = ($allowHighlight && $isCurrentProject);
  ?>
    <div class="panel panel-default" style="margin-left:10px;">
      <div class="panel-heading">
        <h4 class="panel-title">
                   <a <?php if ($highlight) echo 'aria-expanded="true"'?> data-toggle="collapse" href="#collapse<?= $group_number ?>"><?= $panel_project_code ?></a>
                   <?php if ($isCurrentProject) echo ' (Project actuel)'?>
        </h4>
      </div>

      <div id="collapse<?= $group_number ?>" class="panel-collapse collapse <?php if ($highlight) echo 'in' ?>">
        <ul class="list-group">

          <!-- barre de recherche membres -->
          <li class="list-group-item">
            <input type="text" class="form-control" id="membersSearchBar<?= $group_number ?>" name="membersSearchBar" group_number=<?= $group_number ?> placeholder="Rechercher un membre dans <?= $panel_project_code ?>" />
          </li>


          <li class="list-group-item" name="selectAllOption" group_number=<?= $group_number ?>>
            <input type="checkbox" id="chBoxAll<?= $group_number ?>" name="chBoxAll" group_number=<?= $group_number ?>>
            <label for="chBoxAll<?= $group_number ?>" style="color:black">Tous les membres</label>

            <div id="radioBtn" class="btn-group rightsSwitch pull-right" group_number=<?= $group_number ?> name='rightsSwitchAll'>
              <a class="btn btn-primary btn-sm active" data-toggle='rs<?= $group_number ?>' data-title="r">Lecture</a>
              <a class="btn btn-primary btn-sm notActive" data-toggle='rs<?= $group_number ?>' data-title="w">Ecriture</a>
            </div>
            <input type="hidden" name='rs<?= $group_number ?>' value="r" id='rs<?= $group_number ?>'>

          </li>

          <!--  Affichage des membres de chaque groupe  -->
          <?php
              foreach ($projetMembers as $line_number => $member_name) {
          ?>
            <li class="list-group-item activated" name="memberLi" group_number=<?= $group_number ?>>
              <input type="checkbox" id="cb<?= $group_number.'_'.$line_number ?>" name="chBox" group_number=<?= $group_number ?> line_number=<?= $line_number ?>>
              <label for="cb<?= $group_number.'_'.$line_number ?>"><?= $member_name ?></label>
              <div id="radioBtn" class="btn-group rightsSwitch pull-right" name='rightsSwitch' group_number=<?= $group_number ?> line_number=<?= $line_number ?>>
                <a class="btn btn-primary btn-sm active" data-toggle="rs<?= $group_number.'_'.$line_number ?>" data-title="r">Lecture</a>
                <a class="btn btn-primary btn-sm notActive" data-toggle="rs<?= $group_number.'_'.$line_number ?>" data-title="w">Ecriture</a>
              </div>
              <input type="hidden" name="rs<?= $group_number.'_'.$line_number ?>" value="r" id="rs<?= $group_number.'_'.$line_number ?>">
            </li>

          <?php  }  ?>

          <!-- Liens load more/less -->
          <li class="list-group-item clearfix" name="loadMoreOrLessLi" group_number=<?= $group_number ?>>
            <a href="" group_number=<?= $group_number ?> name="loadMore">Afficher plus...</a>
            <a class="pull-right" href="" group_number=<?= $group_number ?> name="loadLess">Afficher moins...</a>
          </li>

        </ul>
        <div class="panel-footer text-center">
          <button type="button" class="btn btn-primary" name="buttonAddGroupUsers" group_number=<?= $group_number ?> >
            Ajouter&nbsp;
            <i class="fa fa-spinner fa-spin" style="display:none"></i>
          </button>
        </div>
      </div>
    </div>

  <?php $group_number++; } ?>

</div>
