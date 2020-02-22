<!-- TODO: Changer les paramètres pour cette visualisation -->
<div class="boxed-content">
  <h3><?=$datavizTitle?></h3>
  <div class="row">
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Unité(s) expérimentale(s) : </h3></p>
      <select class="selectpicker" id="unitExp_selectPicker" data-live-search="true" data-width="100%"
              data-size="5" data-title="Unité expérimentale..."
              data-selected-text-format="count" data-count-selected-text= "{0} unit. exp. selectionnées" multiple>
        <?php
          foreach($all_exp_unit as $exp_unit) {
            echo "<option value='" . $exp_unit["exp_unit_id"]. "'>" . $exp_unit["unit_code"] . "</option>";
          }
        ?>
      </select>
    </div>
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Variable(s) observée(s) : </h3></p>
      <select class="selectpicker" id="variables_selectPicker" data-live-search="true" data-width="100%"
              data-size="5" data-title="Variables..."
              data-selected-text-format="count" data-count-selected-text= "{0} variables selectionnées" multiple>
        <?php
          foreach($all_variablesName as $variable_name) {
            echo "<option value='" . $variable_name . "'>" . $variable_name . "</option>";
          }
        ?>
      </select>
    </div>
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Taille du graphique : </h3></p>
      <select class="selectpicker" id="size_selectPicker"  data-width="100%"
              data-size="5" data-title="Taille du graphique...">

            <option value=100 selected>Petite</option>
            <option value=210>Moyenne</option>
            <option value=400>Grande</option>
      </select>
    </div>
  </div>
  <div id="expUnitGraph_header"></div>
  <div id="expUnitGraph">
    <script type="text/javascript" src="<?php echo js_url('display_trial/dataviz/animatedMap') ?>"></script>
  </div>
</div>
