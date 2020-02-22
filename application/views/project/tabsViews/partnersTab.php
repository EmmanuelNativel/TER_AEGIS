<?php
if (count($partners) > 0) {
  $template = array('table_open' => '<table class="table table-hover">');
  $this->table->set_template($template);
  $this->table->set_heading(array('Nom', 'Ville', 'Pays'));

  foreach ($partners as $partner) {

    ob_start(); ?>
    <a href="#" data-toggle="modal" data-target="#removePartnerModal" onclick="<?= 'focus_partner('.$partner['partner_code'].', \''.$partner['partner_name'].'\')' ?>" class="only-icon">
      <span data-toggle="tooltip" title="Supprimer" class="close">
        <span aria-hidden="true">×</span>
      </span>
    </a>
    <?php
    $this->table->add_row(array($partner['partner_name'], $partner['city'], $partner['country'].ob_get_clean()));
  }
  echo $this->table->generate();
}
else {
  echo "Aucun...";
}
?>

<div class="row text-center">
  <div class="pagination" tabId="partners">
    <?php echo $pagination ?>
  </div>
</div>
