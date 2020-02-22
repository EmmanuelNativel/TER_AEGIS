<div class="fullwidth-block">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('ressources/import') ?>">IMPORTATION/SAISIE</a></li>
            <li class="active">Saisie</li>
        </ol>
        <h2 class="section-title">Nouvelle accession</h2>
        <?php echo form_open('accessions/create', 'class="contact-form"'); ?>
        <?php echo validation_errors() ?>

            <div class="row">
                <div class="col-md-8">
                  <div class="boxed-content">
                    <div class="row">
                      <div class="col-xs-9 col-md-8">
                        <div class="form-group">
                             <label class="sr-only" for="name_code">Nom taxo</label>
                             <select class="selectpicker" id="taxo_name" data-url="taxos/searched_options" data-width="100%" name="taxo_name" data-live-search="true" data-title="Nom taxonomique... (miscanthus sinensis, Sorghum bicolor bicolor)">
                                 <?php if (set_value('taxo_name')) echo '<option value="' . set_value('taxo_name') . '" selected>' . set_value('taxo_name') . '</option>'; ?>
                             </select>
                         </div>
                         <?php if (form_error('taxo_name')) echo form_error('taxo_name'); ?>
                      </div>
                      <div class="col-xs-3 col-md-4">
                       <a href="<?php echo site_url('taxos/create') ?>" class="button bg-info">Nouveau</a>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-xs-9 col-md-8">
                        <div class="form-group">
                         <label class="sr-only" for="accession_code">Code accession</label>
                         <input id="accession_code" class="form-control" type="text" name="accession_code"
                                value="<?php echo set_value('accession_code'); ?>" placeholder="Code accession">
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="text-center">
                      <input type="submit" name="submit" value="Créer">
                  </div>

                  <?php echo form_close(); ?>

              </div>
          </div>


    </div> <!-- end container -->


</div>

<script type="text/javascript">
    $(window).load(function () {
        $root_depth_range = $('#root_depth_range');
        $root_depth_txt = $('#root_depth_txt');

        synch_title_root_depth();

// UI Calendar
        $('#datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "fr"
        });

// Loading
        $('form').submit(function (event) {
            $('input[type=submit]').val("Chargement...");
            $(document).off("click");
        });

// Synchronize root_depth fields
        $root_depth_range.on('input', function (event) {
            synch_title_root_depth();
            $root_depth_txt.val($(this).val());
        });

// Synchronize root_depth fields (bis)
        $root_depth_txt.change(function (event) {
            if ($(this).val() > 200) {
                $(this).val(200.00);
            }
            else if ($(this).val() < 0 || isNaN($(this).val()) || !$(this).val()) {
                $(this).val(0.00);
            }
            synch_title_root_depth();
            $root_depth_range.val($(this).val());
        });

// Synchronize title of root_depth range
        function synch_title_root_depth() {
            $root_depth_range.prop('title', $root_depth_range.val());
        }
    });
</script>
