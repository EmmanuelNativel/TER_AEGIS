/**
* Application pour la création d'un projet dans DAPHNE.
* Gestion des Work Packages et des tâches associées.
*
* @author Medhi Boulnemour <medhi.boulnemour@live.fr>
*/

/////////// Déclaration des variables de l'application /////////////

//Variable formulaire
var new_project_form = $('#newProjectForm');
var new_project_form_btn = $('#newProjectFormBtn');

// Variables WP
var input_wp_name = $('#wp_name');
var input_wp_description = $('#wp_description');

// Variables tâche
var input_task_name = $('#task_name');
var input_task_description = $('#task_description');
var select_task_wp = $('#select_wp');

// Variables vues des paniers
var wp_cart_view = $('#wp_cart_view');
var task_cart_view = $('#task_cart_view');

//Variables boutons
var wp_add_btn = $('#wp_add_btn');
var task_add_btn = $('#task_add_btn');

// Variables paniers
var input_wp_cart = $('#work_packages');
var input_task_cart = $('#tasks');

var wp_cart = JSON.parse(input_wp_cart.val());
var task_cart = JSON.parse(input_task_cart.val());

// MessageBox modal
var alert_modal = $('#alertModal');
var alert_box = $('#alertModal .modal-body');

/////////////////// Interface de l'application /////////////////////
$(document).ready(function() {
  refresh_wp_cart();
  refresh_task_cart();
  update_selector(wp_cart, select_task_wp);
});

/**
* Bouton "Ajouter un Work Package au projet"
*/
wp_add_btn.click(function() {
  var name = input_wp_name.val();
  var description = input_wp_description.val();

  // IDEA: Réaliser la validation du formulaire autrement (ex: CodeIgniter(via Ajax), Jquery Validate Plugin...)
  // TODO: Penser à verifier si le nom du WP n'existe pas deja! (no_dash, alphanum_only, etc...)
  if (name.length <= 30 && name.length >= 3 && description.length <= 255) {
    add_wp(name, description);
    refresh_wp_cart();
    update_selector(wp_cart, select_task_wp);
    input_wp_name.val('');
    input_wp_description.val('');
  }
  else {
    $('.modal').modal('hide').modal('handleUpdate');
    alert_modal.modal('toggle').modal('handleUpdate');
    alert_box.html("<div class='alert alert-danger'>Le champ {Nom} doit être compris entre 3 et 30 caractères.<br>Le champ {Description} doit contenir moins de 255 caractères.</div>");
  }
});
/**
* Bouton "Supprimer un Work Package" du projet
* TODO: suprimer les tâches dépendantes du projet
*/
function wp_remove_btn(wp_name) {
  var result = confirm("La suppression du WP {"+ wp_name +"} entraînera la supression de ses tâches associées. Voulez-vous vraiment supprimer le work package {"+ wp_name +"} ?");

  if (result) {
    remove_wp(wp_name);
    for (var task in task_cart) {
      if (task_cart[task].parent == wp_name) {
        remove_task(task);
      }
    }
    update_selector(wp_cart, select_task_wp);
    refresh_wp_cart();
    refresh_task_cart();
  }

}

task_add_btn.click(function() {
  var name = input_task_name.val();
  var description = input_task_description.val();
  var wp_parent = select_task_wp.val();

  // IDEA: Réaliser la valmidation du formulaire autrement (ex: CodeIgniter(via Ajax), Jquery Validate Plugin...)
  // TODO: Penser à verifier si le nom de la tâche n'existe pas deja! (no_dash, alphanum_only, etc...)
  if (name.length <= 30 && name.length >= 3 && description.length <= 255 && wp_parent in wp_cart) {
    add_task(name, description, wp_parent);
    refresh_task_cart();
    input_task_name.val('');
    input_task_description.val('');
  }
  else {
    $('.modal').modal('hide').modal('handleUpdate');
    alert_modal.modal('toggle').modal('handleUpdate');
    alert_box.html("<div class='alert alert-danger'>Le champ {Nom} doit contenir entre 3 et 30 caractères.<br>Le champ {Description} moins de 255 caractères.<br>Votre tâche doit dépendre d'un Work Package : Le Champ {WP associé} est requis</div>");
  }
});

/**
* Bouton "Supprimer une tâche" du projet
*/
function task_remove_btn(task_name) {
  remove_task(task_name);
  refresh_task_cart();
}

//Ajoute les données des paniers comme données du formulaire à la soumission.
$('#newProjectFormBtn').click(function() {
  input_wp_cart.val(JSON.stringify(wp_cart));
  input_task_cart.val(JSON.stringify(task_cart));
  new_project_form.submit();
});

/////////////////// Fonctions de l'application /////////////////////

/**
* Ajoute un Work Package(WP) à la liste des WP du projet
* @param {String} name Nom du work package
* @param {String} description Description du work package
*/
function add_wp(name, description) {
  wp_cart[name] = description;
}

/**
* Ajoute une tâche à la liste des tâches du projet
* @param {String} name Nom de la tâche
* @param {String} description Description de la tâche
*/
function add_task(name, description, wp) {
  task_cart[name] = { 'description' : description, 'parent' : wp};
}

/**
* Supprime un Work Package(WP) de la liste des WP du projet
* @param {String} wp_name Nom du work package
*/
function remove_wp(wp_name) {
  delete wp_cart[wp_name];
}

/**
* Supprime un tâche(WP) de la liste des tâches du projet
* @param {String} task_name Nom de la tâche
*/
function remove_task(task_name) {
  delete task_cart[task_name];
}

/**
* Met à jour la liste des options d'un selecteur
* @param {Object} cart Tableau associatif listant les WorkPackages ou les tâches
* @param {Object} selector Selecteur à mettre à jour. (ex: $('#mySelector'))
*/
function update_selector(cart, selector) {
  selector.find('option').remove();
  $.each(cart, function(name, description) {
    selector.append($('<option>', {
        value: name,
        text: thirty_chars(name) + ' : ' + thirty_chars(description)
    }));
  });
  selector.selectpicker('refresh');
}

/**
* Retourne les 30 premiers caractères d'une chaine de caractères plus '...'
* Si la chaine fait plus de 30 caractères de long.
*
* @param {string} La chaine de caractères à tronquer
* @return {String} La chaine de caractères tronquée
*/
function thirty_chars(str) {
  if (str.length <= 30) return str;
  else                  return str.substring(30) + '...';
}

/**
* Met à jour la vue de la liste des WorkPackages du projet
*/
function refresh_wp_cart() {
  var html_cart = '';
  if(Object.keys(wp_cart).length === 0){
    wp_cart_view.html('');
  }
  else {
    for (var wp_name in wp_cart) {
      rm_btn = '<button type="button" onclick="wp_remove_btn(\''+ wp_name +'\');" class="remove glyphicon glyphicon-remove"></button>';
      html_cart += '<tr> <td>' + thirty_chars(wp_name) + '</td> <td>' + thirty_chars(wp_cart[wp_name]) + '</td> <td>'+ rm_btn +'</td> </tr>';
    }
  }
  wp_cart_view.html('<tr> <th>Nom</th> <th>Description</th> <th>Annuler</th> </tr>' + html_cart);
}

/**
* Met à jour la vue de la liste des tâches du projet
*/
function refresh_task_cart() {
  var html_cart = '';
  if(Object.keys(task_cart).length === 0){
    task_cart_view.html('');
  }
  else {
    for (var task_name in task_cart) {
      rm_btn = '<button type="button" onclick="task_remove_btn(\''+ task_name +'\');" class="remove glyphicon glyphicon-remove"></button>';
      html_cart += '<tr> <td>' + thirty_chars(task_name) + '</td> <td>' + thirty_chars(task_cart[task_name].description) + '</td> <td>'+ thirty_chars(task_cart[task_name].parent) +'</td> <td>'+ rm_btn +'</td> </tr>';
    }
  }
  task_cart_view.html('<tr> <th>Nom</th> <th>Description</th> <th>WP</th> <th>Annuler</th> </tr>' + html_cart);
}
