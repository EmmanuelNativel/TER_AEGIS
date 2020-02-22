<div class="fullwidth-block">
  <div class="container">
    <div class="col-md-8 col-md-offset-2">
    <?php
      $attributes = array(
        'class' => 'form-contact'
      );
      form_open('#', $attributes); ?>

      <div class="row">
        <div class="col-xs-6 col-md-4 text-left">
          <?php echo $limit ?> élément(s) par page
        </div>
        <div class="hidden-sm hidden-xs col-md-4 text-center">
          <?php  echo $this->pagination->create_links(); ?>

        </div>
        <div class="col-xs-6 col-md-4 text-right">
          Nombre total d'élément(s) <?php echo $total_rows ?>
        </div>
      </div>

      <div class="hidden-md hidden-lg text-center">
        <br>
        <?php  echo $this->pagination->create_links(); ?>
      </div>
      <br>

      <?php form_close(); ?>
      <?php if ($total_rows): ?>
        <?php foreach ($notifications as $notification):?>
          <div class="boxed-content">
            <?php $this->load->view('notification', $notification); ?>
           <!-- <span data-toggle="tooltip" data-placement="right" data-container="body" title="Supprimer" class="glyphicon glyphicon-remove remove_cross notification-remove-btn"></span>-->
          </div>
        <?php endforeach; ?>
        <?php else: ?>
          <div class="boxed-content text-center">
            Vous n'avez aucune notification...
          </div>
      <?php endif; ?>

      <div class="">

      </div>
      <div class="text-center">
        <?php  echo $this->pagination->create_links(); ?>
      </div>
  </div>
  </div>
</div>

<!--<script>
    $('.notification-remove-btn').on('click', function () {
        var id = $(this).data('notifications');

        if (confirm("Voulez-vous vraiment supprimer " + id + " de DAPHNE?")) {
            alert(id);
            $.ajax({
                url: window.SiteURL + "/users/remove_notifications/" + id,
                dataType: 'html',
            })
                .done(function (result) {
                    if (result == "1") {
                        alert("La notification " + id + " a été supprimer avec succés!");
                    }
                    console.log("Remove user:" + id +" with success");
                });
        }
    });
</script>-->
