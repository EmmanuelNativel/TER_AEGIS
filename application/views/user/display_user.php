<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li class="active"><a href="<?= site_url('users/display/' .  $login) ?>"><?=  $login ?></a></li>
        </ol>
        <h2 class="section-title"><?= $login  ?></h2>
        <div class="boxed-content">
            <dl class="dl-horizontal">
                <dt>Prenom</dt>
                <dd><?= $first_name ?></dd>
                <dt>Nom</dt>
                <dd><?= $last_name ?></dd>
                <dt>Email</dt>
                <dd><?= $email ?></dd>
                <dt>Date d'inscription</dt>
                <dd><?= $creation_date ?></dd>
            </dl>

            <div class="text-right">
        </div>
    </div>
        <hr>
    <div class="text-left">
        <a href="<?= site_url('users/index/') ?>" class="button"><span class="glyphicon glyphicon-chevron-left"></span>Tous les utilisateurs</a>
    </div>
</div>


