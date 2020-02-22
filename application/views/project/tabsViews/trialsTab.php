<?php
if (count($trials) > 0) {
    $template = array('table_open' => '<table class="table table-hover">');
    $this->table->set_template($template);
    $this->table->set_heading(array('Essai', 'Localisation'));

    foreach ($trials as $trial) {

        ob_start(); ?>

        <a href="<?= site_url('trials/display/'.$trial['trial_code'].'?project_code='. $project_code) ?>"><?= $trial['trial_code'] ?></a>

      <!--  <a href="#" data-toggle="modal" data-target="#removeTrialModal" onclick="<?/*= 'focus_trial('.$trial['trial_code'].', \')' */?>" class="only-icon">
    <span data-toggle="tooltip" title="Supprimer" class="close">
      <span aria-hidden="true">×</span>
    </span>
        </a>-->

        <?php
        $trial_code = ob_get_clean();
        $this->table->add_row(array($trial_code, $trial['site_code']));
    }
    echo $this->table->generate();
}
else {
    echo "Aucun...";
}
?>

<div class="row text-center">
  <div class="pagination" tabId="trials">
    <?php echo $pagination ?>
  </div>
</div>
