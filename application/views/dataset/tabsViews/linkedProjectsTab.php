<?php
if (count($linkedProjects) > 0) {
    $template = array('table_open' => '<table class="table table-hover">');
    $this->table->set_template($template);
    $this->table->set_heading(array('Code projet', 'Nom', 'Responsable(s)'));

    $table_data = array();

    foreach ($linkedProjects as $project) {
      //$status_label = '';
      //if ($project['is_validated'] == PGSQL_FALSE) {
      //  $status_label = '<span class="label label-warning">Non vérifié</span>';
      //}
      array_push($table_data, array(
        //$status_label.
        '<a target="_blank" href="'.site_url('projects/display/'.$project['project_code']).'">'.$project['project_code'].'</a>',
        $project['project_name'],
        $project['coordinator']
      ));
    }
    echo $this->table->generate($table_data); //génere le tableau html
} else {
    echo "Aucun...";
}
?>

<div class="row text-center">
  <div class="pagination" tabId="linkedProjects">
    <?php echo $pagination ?>
  </div>
</div>
