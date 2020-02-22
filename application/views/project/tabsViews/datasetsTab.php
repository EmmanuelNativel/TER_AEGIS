<?php
if (count($datasets) > 0) {
  $template = array('table_open' => '<table class="table table-hover">');
  $this->table->set_template($template);
  $this->table->set_heading(array('Nom'));

  foreach ($datasets as $dt) {
    ob_start(); ?>
    <a href="<?= site_url('datasets/display/'.$dt['dataset_id']).'?project_code='. $project_code ?>"><?= $dt['dataset_name'] ?></a>

    <a href="#" data-toggle="modal" data-target="#removeDatasetModal" onclick="<?= 'focus_dataset('. $dt['dataset_id'] .',\''. $dt['dataset_name'] .'\')' ?>" class="only-icon">
      <span data-toggle="tooltip" title="Supprimer" class="close">
        <span aria-hidden="true">×</span>
      </span>
    </a>

    <?php
    $dataset_name = ob_get_clean();
    $this->table->add_row(array($dataset_name));
  }

  echo $this->table->generate();
}
?>

<div class="row text-center">
  <div class="pagination" tabId="datasets">
    <?php echo $pagination ?>
  </div>
</div>
