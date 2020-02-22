/*

Dans ce fichier il y a tout le code javascript gérant la partie "gestion des accès au dataset".
Notamment le code des boutons lecture/ecriture , les checkboxes etc...

Remarque1: comme le DOM est mis à jour régulièrement il y a des éléments qui sont ajoutés/supprimés de la vue
          et il faut que jQuery continue à fonctionner sur ces éléments dynamiques. On utilise donc la fonction $.on
          de jQuery sur un parent existant et on filtre ce parent avec un selector.

Remarque2: Toutes ces fonctionnalités peuvent passer à l'echelle jusqu'à environ 500-1000 utilisateurs/projet. Passé ce cap
           il y a des gros ralentissements voir même des plantages. Cela est dû aux nombreuses manipulations de DOM et
           à la quantité importante d'éléments présents en même temps. Pour remédier à cela il faudrait limiter réellement
           (et non juste cacher) le nombre d'élements présents simultanément grâce à un mécanisme de pull avec le serveur...

*/
var nbMembersPerGroup = 7;
var nbLoadMore = 5;
var nbLoadLess = 5;
var myHiddenClass = "customHide"; //nom de la classe appliquée lorsqu'on souhaite afficher/cacher des éléments
var noGroup = { name: 'noGroup', displayedName: 'Sans groupe'}; //infos sur le groupe qui va contenir les utilisateurs "sans groupe"


var selectedMembers = []; //variable globale qui contiendra la liste des membres sélectionnés (object property == username, permission, groupName)
var currentGroupsNumbers = {}; // variable globale qui contiendra des entrées du type group_number => project_code
                               // dans le but de détecter des changements dans l'affichage des groupes
load_groups(); //au lancement on charge tous les groupes (pas d'argument passé en paramètre)

/*******************************************************************************
 Charge les groupes à afficher en fonction du numéro de page
*******************************************************************************/
function loadGroupsPage(num_page){
  $("#loaderContainer").show();
  $('#groupsMembers').hide();

  $.ajax({
    url: SiteURL + '/datasets/loadGroupsHtmlForPage/'+ num_page,
    type: 'get',
    dataType: 'json',
    success: function(response){

      //console.log(response.groupsHtml);
      console.log("get page response !");

      $('#pagination').html(response.pagination);
      $('#groupsMembers').html(response.groupsHtml);
      $('#groupsMembers').show();
      $("#loaderContainer").hide();
      $("[name='rightsSwitch']").hide(); //cache tous les boutons lecture/ecriture des membres au lancement
      limitAllGroupMembers();

    }
  });
}

/*******************************************************************************
  Fonction ajax appelée pour rechercher des projets
*******************************************************************************/
function load_groups(searched_term='') {
  $("#loaderContainer").show();
  $('#groupsMembers').hide();

  $.ajax({
          url: SiteURL + '/datasets/search_members_groups' ,
          type:       'POST',
          data: { searched_term: searched_term },
          cache:      false,
          dataType: 'json',
          success: function(response){
              //console.log(response);
              //console.log(html);

              $('#groupsMembers').html(response.groupsGeneratedHtml);
              $('#groupsMembers').show();
              $("[name='rightsSwitch']").hide(); //cache tous les boutons lecture/ecriture des membres au lancement
              $('#pagination').html(response.pagination);
              $("#loaderContainer").hide();
              limitAllGroupMembers();


          },
          error: function(jqXHR, textStatus, errorThrown) {
                  $('#groupsMembers').html('<p>Impossible d\'accéder aux projets... Erreur : '+jqXHR.status+' ' + errorThrown + '</p>');
          }
  });
}

/*******************************************************************************
  Fonctions custom pour afficher cacher un membre dans un groupe
*******************************************************************************/
  // Le paramètre addClass sert ici à marquer d'un "indicateur" les éléments cachés
  // dans le but de les identifier plus facilement lors des manipulations/
  // (et par conséquent d'identifier ceux affichés aussi).

  //Ce paramètre est à true pour l'affichage/cachage des membres séléctionnés (partie Résumé)
  // et aussi pour les liens "afficher plus/moins" dans cette même partie

function myHide(element, addClass=false) {
  element.hide();
  if (addClass) element.addClass(myHiddenClass);
}

function myShow(element, addClass=false) {
  element.show();
  if (addClass) element.removeClass(myHiddenClass);
}


/*******************************************************************************
  Fonction appelée au lancement pour afficher un nombre minimum de membre
  dans chaque groupe
*******************************************************************************/
function limitAllGroupMembers(membersSelector=null) {
  //mode normal
  if (!membersSelector) {
    membersSelector= "[name=memberLi]";
  }
  //mode selected (dans la section Résumé)
  else {
    var selectedMode = true;
  }

  var all_members_li = $(membersSelector);
  var group_number = all_members_li.attr('group_number'); //attr retourne la valeur pour le premier élément
  var count = 0;

  //On parcours tous les membres actuellement affichés (tout groupes confondu)
  all_members_li.each(function( index ) {
    //console.log("limitAll");
    //Pour le groupe en cours en cache tous les membres "en trop"
    if (count >= nbMembersPerGroup) $(this).addClass('toHide'); //Ajout d'une classe toHide pour de meilleures performances
    count++;

    //Detection de changement de groupe pour la prochaine itération

    if(all_members_li.eq(index + 1).attr('group_number') != group_number) {


      // Si le groupe actuellement traité contient moins de membres que nbMembersPerGroup
      // alors on cache les liens "afficher plus/moins"
      if (count <= nbMembersPerGroup)
        $("[name=loadMoreOrLessLi][group_number="+group_number+"]").addClass('toHide');
      else
        //Dans les autres cas on cache quand même le lien "afficher moins" lors de l'initialisation
        $("[name=loadLess][group_number="+group_number+"]").addClass('toHide');

      //Itialisation des valeurs pour la prochaine itération
      group_number = all_members_li.eq(index + 1).attr('group_number');
      count = 0;
    }
  });

  //Masquage de tous les élements qu'on a marqué de la classe "toHide"
  myHide($('.toHide').removeClass('toHide'), selectedMode);

}

/*******************************************************************************
  Fonction pour réduire le nombre de membre affichés pour un groupe en particulier
  (notamment lors d'une recherche au sein d'un groupe pour limiter les resultats)
*******************************************************************************/

function limitGroupMembers(group_number, membersSelector=null) {
  if (membersSelector) {
    var groupMembersLi = $(membersSelector+"[group_number="+group_number+"]");
    var selectedMode = true;
  } else {
    var groupMembersLi = $("[name=memberLi][group_number="+group_number+"].activated");
  }

  // Si le group contient plus de membre que ce qu'on a le droit d'afficher
  // alors on cache les membres "en trop"
  if (groupMembersLi.length > nbMembersPerGroup) {

    var count = 0;
    groupMembersLi.each(function( index ) {
      if (count >= nbMembersPerGroup) $(this).addClass('toHide');
      count++;
    });

    //On cache le lien "afficher moins" et on affiche le lien "afficher plus".
    myShow( $("[name=loadMoreOrLessLi][group_number="+group_number+"]") , selectedMode );
    myHide( $("[name=loadLess][group_number="+group_number+"]") , selectedMode );
    myShow( $("[name=loadMore][group_number="+group_number+"]") , selectedMode );

  }
  // S'il n'y a pas besoin de cacher des membres on enlève les liens
  // "afficher plus/moins"
  else {
    myHide( $("[name=loadMoreOrLessLi][group_number="+group_number+"]") , selectedMode );
  }

  myHide($('.toHide').removeClass('toHide'), selectedMode);
}

/*******************************************************************************
  Handler pour les clics sur "afficher plus" au sein d'un groupe
*******************************************************************************/
$("#groupsMembers,#selectedMembersDiv").on('click', '[name=loadMore]',  function(e) {
  e.preventDefault();
  var link_positionBefore = $(this).offset().top;

  var group_number = $(this).attr('group_number');

  if (group_number.includes('S')) { //Si contient S alors on est dans la partie selectedMembersDiv
    var hidden_members = $("[name=selectedMemberLi][group_number="+group_number+"]:hidden");
    var selectedMode = true;
  }
  else
    var hidden_members = $("[name=memberLi][group_number="+group_number+"].activated:hidden"); // On filtre ceux qui ont la classe activated
                                                                                               // càd ceux qui ont été recherché par exemple

  //Parcours des membres cachés afin d'en afficher de nouveaux
  var count = 0;
  hidden_members.each(function( index ) {
    if (count < nbLoadMore) myShow($(this), selectedMode);
    else return;
    count++;
  });

  //Affichage du lien 'afficher moins'
  myShow( $("[name=loadLess][group_number="+group_number+"]"), selectedMode );
  //sauvegarde de la position du lien avant l'eventuelle suppression
  var link_positionAfter = $(this).offset().top;
  //Si on a atteint le max on peut cacher le lien "afficher plus"
  if (hidden_members.length - nbLoadMore <= 0) myHide($(this), selectedMode);

  //Calcule de la nouvelle position vers laquelle scroller pour avoir le curseur sur le lien "afficher plus"
  var moveDownShift = link_positionAfter - link_positionBefore;
  var newPosition = $(window).scrollTop() + moveDownShift;
  $(window).scrollTop(newPosition);
});

/*******************************************************************************
  Handler pour les clics sur "afficher moins" au sein d'un groupe
*******************************************************************************/
$("#groupsMembers,#selectedMembersDiv").on('click', '[name=loadLess]',  function(e) {
  e.preventDefault();
  var link_positionBefore = $(this).offset().top;
  var group_number = $(this).attr('group_number');

  if (group_number.includes('S')) {
    var visible_members = $("[name=selectedMemberLi][group_number="+group_number+"]:visible");
    var selectedMode = true;
  }
  else
    var visible_members = $("[name=memberLi][group_number="+group_number+"].activated:visible");

  var nbToHide = nbLoadLess;
  var hideLoadLessLink = false;
  //Si on va atteindre le nombre minimum de membres à afficher
  if (visible_members.length - nbLoadLess <= nbMembersPerGroup) {
    //Calcul du nombre exact de membres à cacher
    nbToHide = visible_members.length - nbMembersPerGroup;
    //On cachera à la fin le lien "afficher moins" car on aura atteint le minimum
    hideLoadLessLink = true;
  }

  //Parcours des membres visibles afin d'en cacher le bon nombre
  $(visible_members.get().reverse()).each(function( index ) {
    if (nbToHide > 0) myHide($(this), selectedMode);
    else return;
    nbToHide--;
  });

  //sauvegarde de la position du lien afficher moins avant l'eventuelle suppression
  var link_positionAfter = $(this).offset().top;
  if (hideLoadLessLink) myHide($(this), selectedMode);
  //Affichage du lien 'afficher plus' si ce n'est pas déjà le cas
  myShow( $("[name=loadMore][group_number="+group_number+"]"), selectedMode);


  //Calcule de la nouvelle position vers laquelle scroller pour avoir le curseur sur le lien "afficher moins"
  var moveUpShift = link_positionBefore - link_positionAfter;
  var newPosition = $(window).scrollTop() - moveUpShift;
  $(window).scrollTop(newPosition);
});

/*******************************************************************************
  Handler pour les clics sur les numéros de pagination etc
*******************************************************************************/
$('#pagination').on('click','a',function(e){
 e.preventDefault();
 var num_page = $(this).attr('data-ci-pagination-page');
 loadGroupsPage(num_page);
});

/*******************************************************************************
  Handler pour la barre de recherche de projet
*******************************************************************************/

$('#input_search_groups').keyup(function() {
  var searched_term = $(this).val();
  load_groups(searched_term);
})

/*******************************************************************************
  Handler pour les barres de recherche dans chaque groupe
*******************************************************************************/

$("#groupsMembers").on('keyup', '[name=membersSearchBar]',  function() {
  var searched_term = $(this).val().toLowerCase();
  var group_number = $(this).attr('group_number');

  //Affichage ou cachage de la checkbox pour selectionner tous les membres ou non
  // (car en mode recherche cela selectionnera également tous les membres non recherchés)
  var selectAllLine = $("[name=selectAllOption][group_number="+group_number+"]");

  if (searched_term != "") selectAllLine.hide();
  else                     selectAllLine.show();

  //Dans un premier temps on affiche tous les membres puis on cache ceux qui ne
  // "matchent" pas le terme recherché
  var all_members_li = $("[name=memberLi][group_number="+group_number+"]");
  all_members_li.show();
  all_members_li.addClass("activated"); // La classe activated servira à identifier les éléments
                                        // pour lesquels les liens afficher plus/moins fonctionneront
                                        // (Par exemple lors d'une recherche , il ne faudra afficher/cacher que les éléments de la recherche)

  all_members_li.each(function( index ) {
    var currentUsername = $(this).find("label").text().toLowerCase();
    if (!currentUsername.includes(searched_term)) {
      $(this).hide();
      $(this).removeClass("activated");
    }
  });

  limitGroupMembers(group_number);
})

/*******************************************************************************
  Ajoute les utilisateurs lorsqu'ils sont séléctionnés par groupe dans la section
  "Sélectionner par projet"
*******************************************************************************/

$("#groupsMembers").on('click', '[name=buttonAddGroupUsers]', function() {
  //Affichage d'un spinner dans le bouton
  var spinner = $(this).children('.fa');
  var group_number = $(this).attr('group_number');
  var group_name = $(this).parent().parent().parent().find(".panel-title a").html();

  var selected = $("[group_number="+group_number+"][name='chBox']").filter(':checked');

  if (selected.length > 0) {

    spinner.show(200, function() {
      //Extraction des infos liés aux utilisateurs sélectionnés
      selected.each(function() {
          var selectedUsername = $("label[for='"+ this.id +"']").text();
          //On regarde si cet utilisateur n'a pas déjà été sélectionné
          var alreadyAdded = selectedMembers.some(member => member.username === selectedUsername);
          if (!alreadyAdded) {
            //Pour récupérer la permission il faut chercher le rightSwitch associé.
            // Par exemple si le checkbox avait pour id cb1_1 alors le rightSwitch associé a pour id rs1_1.
            var rightSwitchId = "rs" + this.id.substring(2);
            var selectedPermission =  $("#" + rightSwitchId).val();

            selectedMembers.push({ username:selectedUsername, permission:selectedPermission, groupName:group_name});
          }
      });

      refreshAll(function() {   spinner.hide(); });
    });
  }
});


/*******************************************************************************
  Ajoute un utilisateur lorsqu'il est selectionné dans la section
  "Selectionner un utilisateur"
*******************************************************************************/
$('#addOneUser').on('click', function(){
  var selectedUsername =  $('#selectUser').val();
  if (selectedUsername) {
    //On regarde si cet utilisateur n'a pas déjà été sélectionné
    var alreadyAdded = selectedMembers.some(member => member.username === selectedUsername);
    if (alreadyAdded) {
      $('#myModal .modal-body').html("L'utilisateur <b>" + selectedUsername + "</b> a déjà été sélectionné !");
      $('#myModal').modal('show');
      return;
    }

    var permission = $('#rsOne').val();
    selectedMembers.push({ username:selectedUsername, permission:permission, groupName: noGroup.name});

    //Suppression des éléments précédemment recherchés dans le selectpicker
    $('#selectUser').find('option').remove();
    $('#selectUser').val('default').selectpicker("refresh");

    refreshAll();
  }
});


/*******************************************************************************
  Supprime un utilisateur dans un groupe sélectionné
*******************************************************************************/

$("#selectedMembersDiv").on('click', '[name="deleteUser"]',  function() {

    var indiceToDelete =  $(this).val();
    selectedMembers.splice(indiceToDelete, 1);
    refreshAll();

});

/*******************************************************************************
  Supprime un groupe (projet) entier
*******************************************************************************/

$("#selectedMembersDiv").on('click', '[name="deleteSelectedGroup"]',  function() {

    var projectCodeToDelete = $(this).val();
    //Filtrage de la variable globale des membres sélectionnés en enlevant tout ceux dont le groupName doit être supprimé.
    selectedMembers = $.grep(selectedMembers, function(member) {return member.groupName == projectCodeToDelete}, true);
    refreshAll();

});



/*******************************************************************************
  Petite function pour rafraichir tout le html et le hidden input
*******************************************************************************/

function refreshAll(_callback) {
  //Sauvegarde de la nouvelle liste dans le champ caché
  updateSelectedMembersField()
  //(Re)génération du html qui affichera la liste des membres séléctionnés.
  generateSelectedMembersHtml(function() { if (_callback) _callback() });

}

/*******************************************************************************
  Met à jour le "hidden input" qui contient la liste des membres sélectionnés
*******************************************************************************/
function updateSelectedMembersField() {
  $('#hiddenSelectedMembers').val(JSON.stringify(selectedMembers)); //store array
}

/*******************************************************************************
  Genère le html qui affiche la liste des membres sélectionnés (partie Résumé)
*******************************************************************************/
function generateSelectedMembersHtml(_callback) {
  if (selectedMembers.length > 0) {
    // Transformation du tableau des membres séléctionnés afin de le compartimenter en groupe
    // On initialise le tableau avec noGroup pour qu'il s'affiche toujours en haut de la liste.
    var structuredMembers = { noGroup: [] };
    selectedMembers.forEach(function(member,indice) {
      var groupName = member.groupName ? member.groupName : noGroup.name;
      var newMember = {originalIndice: indice, ...member}; //On conserve l'indice original pour une eventuelle suppression

      if (structuredMembers.hasOwnProperty(groupName)) {
        structuredMembers[groupName].push(newMember);
      } else {
        structuredMembers[groupName] = [newMember];
      }
    });

    // Phase de détection de changement de groupes afin de déterminer quel groupe il faut
    // réduire (limiter le nombre de membres affichés) ou pas.
    // La logique ici est de ne limiter que les nouveaux groupes et ceux qui ont changés
    var newGroupsNumbers = {};
    var groupsNumbersToLimit = [];
    Object.keys(structuredMembers).forEach( function(project_code, indice) {
      //On ne traite noGroup que lorsqu'il n'est pas vide (important sinon bugs)
      if (project_code == noGroup.name && structuredMembers[noGroup.name].length <= 0)
        return; //stop cette itération
      //Enregistrement des valeurs groupNumber:project_code dans un tableau
      var groupNumber = "S"+indice; //S pour selected
      newGroupsNumbers[groupNumber] = project_code;

      // Si on a un nouveau groupNumber (=> nouveau groupe à afficher) ou que le project_code
      // a changé pour ce groupNumber alors c'est qu'on doit "limiter" ce groupe
      if ( !currentGroupsNumbers.hasOwnProperty(groupNumber) ||
           currentGroupsNumbers[groupNumber] !== project_code )
      {
        groupsNumbersToLimit.push(groupNumber);
      }
    });
    //console.log("oldGroupsNumbers= " + JSON.stringify(currentGroupsNumbers));
    //console.log("newGroupsNumbers= " + JSON.stringify(newGroupsNumbers));

    //On remplace la variable globale par le nouveau qu'on vient de construire
    currentGroupsNumbers = newGroupsNumbers;


    // Génération du html à afficher (utilisation de panel)
    var generatedHtml = [];

    Object.keys(structuredMembers).forEach(function(project_code, indice) {

      var nbGroupMemnbers = structuredMembers[project_code].length;
      //On vérifie que la liste associée au groupe n'est pas vide (c'est peut être le cas pour noGroup)
      if ( nbGroupMemnbers > 0) {

        //permet de conserver le "collapse state" avant refresh pour pas que tous les panels soient refermés à chaque fois
        var previousCollapseClass = $('#selectedGroup' + indice).is( ":visible" ) ? 'in' : '';
        var panelGroupTitle = (project_code == noGroup.name) ? noGroup.displayedName : project_code;
        panelGroupTitle += ' ( ' + nbGroupMemnbers +  (nbGroupMemnbers == 1 ? ' membre' : ' membres') + ' )';

        //Génération du html
        $.merge(generatedHtml, [
          '<div class="row">',
          '<div class="col-xs-7 col-sm-7 col-md-6" >',
          '<div class="panel panel-default" style="margin-bottom:5px;">',
              '<div class="panel-heading">',
                '<h4 class="panel-title selectedGroup">',
                  '<a data-toggle="collapse" href="#selectedGroup' + indice + '">' + panelGroupTitle + '</a>',
                '</h4>',
              '</div>',
              '<div id="selectedGroup'+ indice +'" class="panel-collapse collapse ' + previousCollapseClass + '">',
                '<ul class="list-group">'
        ]);

        var currentGroupNumber = "S" + indice; //S pour selected

        //Création d'un tableau qui détermine la visibilité avant refresh de chacun des membres
        // (Sinon à chaque refresh tous les membres seront affichés)
        var membersWasNotLoaded = {}; //entrées de type username: true/false (caché --> true)
        $("[name=selectedMemberLi][group_number="+currentGroupNumber+"]").each( function(index) {
          var username = $(this).children("label").text();
          membersWasNotLoaded[username] = $(this).hasClass("customHide");
        });

        //Pour chaque member
        structuredMembers[project_code].forEach(function(member) {
          var permissionIndicator = member.permission == "r" ?
                                    '<h3 style="display: inline"><span class="label label-primary">Lecture</span></h3>' :
                                    '<h3 style="display: inline"><span class="label label-warning">Ecriture</span></h3>';

          //Si ce membre n'était pas chargé à l'affichage (caché par "afficher moins") alors on le cache à nouveau
          var wasNotLoaded = membersWasNotLoaded[member.username];
          var notLoadedClass = (wasNotLoaded) ? 'customHide' : '';
          var memberStyle = (wasNotLoaded) ? ' style="display: none;" ' : '';

          $.merge(generatedHtml, [
            '<li class="list-group-item ' + notLoadedClass + '" name="selectedMemberLi" group_number=' + currentGroupNumber + memberStyle + '>',
              '<label>' + member.username + '</label>',
              permissionIndicator,
              '<button type="button" class="btn btn-danger btn-xs pull-right" name="deleteUser" value=' + member.originalIndice + '> Supprimer </button>',
            '</li>'
          ]);
        }); // Fin boucle membres

        //Conservation des états des liens "afficher plus/moins" et de leur container <li>
        var loadMoreOrLessLi_wasHidden =  $("[name=loadMoreOrLessLi][group_number="+currentGroupNumber+"]").hasClass("customHide");
        var loadMoreOrLessLi_hideClass = (loadMoreOrLessLi_wasHidden) ? 'customHide' : '';
        var loadMoreOrLessLi_style = (loadMoreOrLessLi_wasHidden) ? ' style="display: none;" ' : '';

        var loadMore_wasHidden = $("[name=loadMore][group_number="+currentGroupNumber+"]").hasClass("customHide");
        var loadMore_hideClass = (loadMore_wasHidden) ? 'customHide' : '';
        var loadMore_style = (loadMore_wasHidden) ? ' style="display: none;" ' : '';

        var loadLess_wasHidden = $("[name=loadLess][group_number="+currentGroupNumber+"]").hasClass("customHide");
        var loadLess_hideClass = (loadLess_wasHidden) ? 'customHide' : '';
        var loadLess_style = (loadLess_wasHidden) ? ' style="display: none;" ' : '';

        //End of panel pour chaque groupe
        $.merge(generatedHtml, [
                  //Liens afficher plus/moins
                  '<li class="list-group-item clearfix ' + loadMoreOrLessLi_hideClass + '" name="loadMoreOrLessLi" group_number=' + currentGroupNumber + loadMoreOrLessLi_style + '>',
                    '<a class="' + loadMore_hideClass + '" href="" name="loadMore" group_number=' + currentGroupNumber + loadMore_style + '>Afficher plus...</a>',
                    '<a class="pull-right ' + loadLess_hideClass + '" href="" name="loadLess" group_number=' + currentGroupNumber + loadLess_style + '>Afficher moins...</a>',
                  '</li>',
                '</ul>',
              '</div>', //panel-collapse
            '</div>', //panel-default
            '</div>', //end col

            '<div class="col-2"  id="buttonDeleteSelectedGroup">',
                '<button type="button" class="btn btn-danger btn-xs" name="deleteSelectedGroup" value=' + project_code + '> Supprimer </button>',
            '</div>',

          '</div>', //end row
        ]);
      }
    }); // Fin boucle groupes

    $('#selectedMembersDiv').html(generatedHtml.join('\n'));

    //Après affichage du html on "limite" les groupes précédemment détectés
    groupsNumbersToLimit.forEach(function(groupNumber) {
      limitGroupMembers(groupNumber, "[name=selectedMemberLi]");
    });
  } else {
    currentGroupsNumbers = {}; //S'il n'y a plus de membres il n'y a plus de groupes à "tracker"
    $('#selectedMembersDiv').html("<p>Aucun utilisateur sélectionné.</p>");
  }


  if (_callback) _callback(); //On appelle le callback quand le traitement est terminé
}

/*******************************************************************************
  Gère le switch des boutons lecture/écriture
*******************************************************************************/
$("#memberRightsPanel").on('click', '.rightsSwitch a',  function() {
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');
    $('#'+tog).prop('value', sel);

    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
});


/*******************************************************************************
  Fonction pour afficher/cacher le switch "lecture/ecriture" en fonction du
  numéro de ligne
*******************************************************************************/

$("#groupsMembers").on('change', "[name='chBox']",  function() {
  var line_number = $(this).attr('line_number');
  var group_number = $(this).attr('group_number');
  var associatedRightsSwitch = $("[name='rightsSwitch'][group_number="+group_number+"][line_number="+line_number+"]");

  if($(this).is(':checked')) {
    associatedRightsSwitch.show();
  } else {
    associatedRightsSwitch.hide();
    //On décoche le checkbox selectAll car au moins 1 n'est plus coché
    var selectAllCheckbox = $("[group_number="+group_number+"][name='chBoxAll']");
    selectAllCheckbox.prop("checked", false);
  }
});

/*******************************************************************************
  Fonction pour selectionner tout un groupe avec le checkbox global du groupe
*******************************************************************************/
$("#groupsMembers").on('click', "[name='chBoxAll']",  function() {
  var group_number = $(this).attr('group_number');
  var checkboxes = $("[group_number="+group_number+"][name='chBox']");

  var associatedRightsSwitches = $("[group_number="+group_number+"][name='rightsSwitch']");

  if($(this).is(':checked')) {
    checkboxes.prop("checked", true);
    associatedRightsSwitches.show();
  } else {
    checkboxes.prop("checked", false);
    associatedRightsSwitches.hide();
  }
});


/*******************************************************************************
    Fonction pour changer les droits de tout un groupe d'un coup
    avec le switch "lecture/ecriture" global du groupe
*******************************************************************************/
$("#groupsMembers").on('click', "[name='rightsSwitchAll'] a",  function() {
  var group_number = $(this).parent().attr('group_number');
  var switches =   $('.rightsSwitch[group_number='+group_number+'] a');
  var newValue = $(this).data('title');

  switches.each(function(idx, el){
    if (idx%2==0) {
      var toggleId = $(this).data('toggle');
      $('#'+toggleId).prop('value', newValue);
    }
  });

  switches.not('[data-title="'+newValue+'"]').removeClass('active').addClass('notActive');
  switches.filter($('[data-title="'+newValue+'"]')).removeClass('notActive').addClass('active');

});
