$(document).ready(function() {
  // Overrideable options (Options du script d'affichage de la grille des membres)
  var options = {
    speed: 1500, // Transition/animation speed (milliseconds).
    itemSelector: '.user-item',
    // use outer width of grid-sizer for columnWidth
    columnWidth: '.grid-sizer',
    gutter: '.gutter-sizer',
    percentPosition: true
  };

  var $grid = $('#grid'),
  $sizer = $grid.find('.shuffle__sizer');

  $grid.shuffle({
    itemSelector: '.user-item',
    sizer: $sizer
  });

  // Options de trie
  $('.sort-options').on('change', function() {
    var sort = this.value,
    opts = {};

    // We're given the element wrapped in jQuery
    if ( sort === 'date' ) {
      opts = {
        reverse: true,
        by: function($el) {
          return $el.data('date');
        }
      };
    } else if ( sort === 'alphanumeric' ) {
      opts = {
        reverse: false,
        by: function($el) {
          return $el.find('.user-item__details').text();
        }
      };
    }

    // Filter elements
    $grid.shuffle('sort', opts);
  });

  // Recherche (Filtre avancé) Recherche un term dans les infos des membres!
  $('.js-shuffle-search').on('keyup change', function() {
    var val = this.value.toLowerCase();
    $grid.shuffle('shuffle', function($el, shuffle) {

      // Only search elements in the current group
      if (shuffle.group !== 'all' && $.inArray(shuffle.group, $el.data('groups')) === -1) {
        return false;
      }

      var text = $.trim( $el.find('.user-item__details').text() ).toLowerCase();
      return text.indexOf(val) !== -1;
    });
  });

  //Filtre par tag
  var $filterOptions = $('.filter-options');
  var $btns = $filterOptions.children();

  $btns.on('click', function() {
    var $this = $(this),
    isActive = $this.hasClass( 'active' ),
    group = isActive ? 'all' : $this.data('group');

    // Hide current label, show current label in title
    if ( !isActive ) {
      $('.filter-options .active').removeClass('active');
    }

    $this.toggleClass('active');

    // Filter elements
    $grid.shuffle( 'shuffle', group );
  });


  $grid.on('done.shuffle', function() {
    console.log('Finished initializing shuffle!');
  });

  // Initialize shuffle (la grille de réprésentation des utilisateurs)
  $grid.shuffle( options );

  ///////////////////// Commandes sur les membres ///////////////////////////////
  //Changement de status de l'utilisateur (admin/user)
  $('.status_crown').on('click', function() {
    var userItem = $(this).parents(".user-item");

    if ($(this).hasClass('admin_crown')) {
      AdminToUser(userItem);
    } else {
      UserToAdmin(userItem);
    }
  });

  function UserToAdmin(userItem) {
    var login = userItem.find('.user-item__login').text();

    if (confirm("Voulez-vous vraiment faire de "+ login +" un administrateur de DAPHNE?")) {
      $.ajax({
        url: window.SiteURL + "/admin/change_admin_status/" + login,
        type: 'GET',
        dataType: 'html',
        data: {new_status: 't'}
      })
      .done(function(result) {
        if (result == "1") {
          alert("Le membre " + login + " est désormais administrateur de DAPHNE!");
          userItem.data('groups', ["admin"] );

          userItem.find('.status_crown[data-toggle="tooltip"]')
                  .attr('data-original-title', "STATUS: Administrateur")
                  .tooltip('fixTitle');

          userItem.find(".status_crown").addClass('admin_crown');
          userItem.find(".status_crown").removeClass('user_crown');

        }
      });
    }
  }

  function AdminToUser(userItem) {
    var login = userItem.find('.user-item__login').text();

    if (confirm("Voulez-vous vraiment retirer les droits d\'administration de "+ login +"?")) {
      $.ajax({
        url: window.SiteURL + "/admin/change_admin_status/" + login,
        type: 'GET',
        dataType: 'html',
        data: {new_status: 'f'}
      })
      .done(function(result) {
        if (result == "1") {
          alert("Le membre " + login + " n'est plus administrateur!");
          userItem.data('groups', ["user"] );

          userItem.find('.status_crown[data-toggle="tooltip"]')
                  .attr('data-original-title', "STATUS: Utilisateur")
                  .tooltip('fixTitle');

          userItem.find(".status_crown").addClass('user_crown');
          userItem.find(".status_crown").removeClass('admin_crown');

        }
      });
    }
  }

  //Remove user
  $('.member-remove-btn').on('click', function() {
    var login = $(this).data('login');
    var userItem = $(this).parents(".user-item");

    if (confirm("Voulez-vous vraiment supprimer " + login + " de DAPHNE?")) {
      $.ajax({
        url: window.SiteURL + "/admin/remove_user/" + login,
        dataType: 'html',
      })
      .done(function(result) {
        if (result == "1") {
          alert("L'utilisateur " + login + " a été supprimer avec succés!");
          $grid.shuffle('remove', userItem);
        }
        console.log("Remove user:" + login +" with success");
      });
    }
  });
  //////////////////////////////////////////////////////////////////////////////

  ///////////////////// Commandes sur les Demandes d'inscriptions ///////////////////////////////
  //Remove subscription request
  $('.subscriber-remove-btn').on('click', function() {
    var login = $(this).data('login');
    var userItem = $(this).parents(".user-item");

    if (confirm("Voulez-vous vraiment supprimer la demande d'inscription: " + login + "?")) {
      $.ajax({
        url: window.SiteURL + "/admin/remove_subscriber/" + login,
        dataType: 'html',
      })
      .done(function(result) {
        if (result == "1") {
          alert("La demande d'inscription: " + login + " a été supprimer avec succés!");
          $grid.shuffle('remove', userItem);
        }
        console.log("Remove subscriber:" + login +" with success");
      });
    }
  });
  //////////////////////////////////////////////////////////////////////////////
  //Active les bulles d'aides!
  $('[data-toggle="tooltip"]').tooltip();

});
