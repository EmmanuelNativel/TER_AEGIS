$(document).ready(function() {

  /**
   * ---------------------------------------------------------------------------
   *  Indique à DAPHNE que l'utilisateur a consulté ses dernières notifications.
   * ---------------------------------------------------------------------------
   */
  $('#menu4').click(function(event) {
    $.ajax({
      url: SiteURL + 'member/notifications_read'
    })
    .done(function() {
      $('#notification_nb').fadeOut('slow');
    });

  });
});
