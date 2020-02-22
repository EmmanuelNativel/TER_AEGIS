<div class="boxed-content">
  <h3><?=$datavizTitle?></h3>
  <div class="row">
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Unité(s) expérimentale(s) : </h3></p>
      <select class="selectpicker" id="unitExp_selectPicker" data-live-search="true" data-width="100%"
              data-size="5" data-title="Unité expérimentale..."
              data-selected-text-format="count" data-count-selected-text= "{0} unit. exp. selectionnées" multiple data-actions-box="true">
        <?php
          foreach($all_exp_unit as $exp_unit) {
            echo "<option value='" . $exp_unit["exp_unit_id"]. "'>" . $exp_unit["unit_code"] . "</option>";
          }
        ?>
      </select>
    </div>
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Variable en x : </h3></p>
      <select class="selectpicker" id="variableX_selectPicker" data-width="100%"
              data-size="5" data-title="Variable..." >
        <?php
          foreach($all_variablesName as $variable_name) {
            echo "<option value='" . $variable_name . "'>" . $variable_name . "</option>";
          }
        ?>
      </select>
    </div>
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Variable en y : </h3></p>
        <select class="selectpicker" id="variableY_selectPicker" data-width="100%"
                data-size="5" data-title="Variable..." >
          <?php
            foreach($all_variablesName as $variable_name) {
              echo "<option value='" . $variable_name . "'>" . $variable_name . "</option>";
            }
          ?>
        </select>
    </div>
  </div>
  <div id="expUnit2D">
    <script type="text/javascript" src="<?php echo js_url('display_trial/dataviz/expUnit2D') ?>"></script>
  </div>
</div>
