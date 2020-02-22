<div class="fullwidth-block"> <!-- Start Admin_menu -->
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-tabs">
          <li role="presentation" <?php echo($this->uri->segment(2) == '' or $this->uri->segment(2) == 'index')?'class="active"':'';?>>
            <a href="<?php echo site_url('admin/index') ?>">Général</a>
          </li>
          <li role="presentation" <?php echo($this->uri->segment(2)=='tables')?'class="active"':'';?>>
            <a href="<?php echo site_url('admin/tables') ?>">Tables</a>
          </li>
          <li role="presentation" <?php echo($this->uri->segment(2)=='import_csv')?'class="active"':'';?>>
            <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              Import
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu menu-subtab" role="menu" aria-labelledby="menu1">
              <li role="presentation"><a class="link-subtab" tabindex="-1" href="<?php echo site_url('admin/import_csv') ?>">Données CSV</a></li>
            </ul>
          </li>
          <li role="presentation" <?php echo(($this->uri->segment(2) =='members'))?'class="active"':'';?>>
            <a href="<?php echo site_url('admin/members') ?>">Utilisateurs</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div> <!-- End Admin_menu -->
