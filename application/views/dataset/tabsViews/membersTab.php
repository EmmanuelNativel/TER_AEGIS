<?php
if (count($members) > 0) {
    $template = array('table_open' => '<table class="table table-hover">');
    $this->table->set_template($template);
    $this->table->set_heading(array('Identifiant', 'Nom', 'Droits d\'accès'));

    foreach ($members as $member) {
// On définit l'affichage des membres en fonction de leur status et de celui qui les vois.
        ob_start();

        if ($member['permissions'] == ACCESS_WRITE): ?>
            <span data-toggle="tooltip" title="Ecriture" class="glyphicons flaticon-lock"></span>
        <?php else: ?>
            <span data-toggle="tooltip" title="Lecture" class="glyphicon glyphicon-lock"></span>
        <?php endif;
        $status_icon = ob_get_clean();

        ob_start();
        if ($this->session->userdata('username') == $dataset_owner_login): ?>
            <div class="row" onclick="focus_member('<?= $member['username'] ?>');">
                <div class="col-xs-6 text-center">
                    <a href="#" data-toggle="modal" data-target="#changeStatutModal"
                       class="only-icon"><?= $status_icon ?></a>
                </div>
                <div class="col-xs-6">
                    <a href="#" data-toggle="modal" data-target="#removeMemberModal" class="only-icon"><span data-toggle="tooltip" title="Supprimer" class="close"><span aria-hidden="true">×</span></span>
                    </a>
                </div>
            </div>
            <?php
        else:
            echo $status_icon;
        endif;
        $member_status = ob_get_clean();
        $this->table->add_row(array($member['username'], $member['first_name'] . ' ' . $member['last_name'], $member_status));
    }
    echo $this->table->generate();
} else {
    echo "Aucun...";
}
?>

<div class="row text-center">
  <div class="pagination" tabId="members">
    <?php echo $pagination ?>
  </div>
</div>
