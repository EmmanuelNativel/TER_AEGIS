<!-- TODO: Changer les paramètres pour cette visualisation -->
<div class="boxed-content">
  <h3><?= $datavizTitle ?></h3>
  <div class="row">

    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p>
        <h4>Facteur(s) : </h3>
      </p>

      <!-- <select class="selectpicker" id="factor_selectPicker" data-live-search="true" data-width="100%" data-size="5" data-title="Facteurs..." data-selected-text-format="count" data-count-selected-text="{0} facteurs selectionnées" multiple> -->
      <?php
      /*
        $factors = array();
        foreach ($all_exp_unit as $exp_unit) {
          $factor = $exp_unit["factor"];
          $factor_id = $exp_unit["factor_id"];
          if (!in_array($factor, $factors)) {
            array_push($factors, $factor);
            echo "<option value='" . $factor_id . "'>" . $factor . "</option>";
          }
          //echo "<option value='" . $exp_unit["exp_unit_id"]. "'>" . $exp_unit["unit_code"] . "</option>";
          //echo "<script> console.log('test', ".$exp_unit["factor"]."); </script>";
        }
        */
      ?>
      <!-- </select> -->

      <!-- <select class="selectpicker" id="factor_selectPicker" data-live-search="true" data-width="100%" data-size="5" data-title="Facteurs..." data-selected-text-format="count" data-count-selected-text="{0} facteurs selectionnées" multiple></select>  -->

      <?php
      $factors = array();
      foreach ($factors_list as $f) {
        $factor = $f["factor"];
        $factor_level = $f["factor_level"];
        if (!array_key_exists($factor, $factors)) {
          $factors[$factor] = array();
          array_push($factors[$factor], $factor_level);
        } else array_push($factors[$factor], $factor_level);
      }

      foreach ($factors as $factor => $factors_lvl) {
        echo "<select class='selectpicker' id='" . $factor . "_selectPicker' data-live-search='true' data-width='100%' data-size='" . count($factors_lvl) . "' data-title='<i class=\"" . $iconsName[$factor] . "\"></i>  " . $factor . "' data-selected-text-format='count' data-count-selected-text='<i class=\"" . $iconsName[$factor] . "\"></i> : {0} " . $factor . "' multiple data-actions-box='true'>";
        foreach ($factors_lvl as $factor_lvl) {
          echo "<option value='" . $factor_lvl . "'>" . $factor_lvl . "</option>";
        }

        echo '</select><br/><br/>';
      }

      ?>


    </div>

    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p>
        <h4>Variable à observer : </h3>
      </p>
      <select class="selectpicker" id="variable_selectPicker" data-width="100%" data-size="5" data-title="Variable..." data-live-search='true'>
        <?php
        foreach ($all_variablesName as $variable_name) {
          echo "<option value='" . $variable_name . "'>" . $variable_name . "</option>";
        }
        ?>
      </select>
    </div>



    <?php
    /*
    <div class="col col-xs-4 col-sm-4 col-md-4">
      <p><h4>Taille du graphique : </h3></p>
      <select class="selectpicker" id="size_selectPicker"  data-width="100%"
              data-size="5" data-title="Taille du graphique...">

            <option value=100 selected>Petite</option>
            <option value=210>Moyenne</option>
            <option value=400>Grande</option>
      </select>
    </div>
    */
    ?>
  </div>

  <div class="row" style="display:flex; flex-direction:row; align-items:center;">
    <div id="slider"></div>
    <button type="button" class="btn" id="sliderButton" style="margin-bottom:5px; display:none"><i class="fa fa-play"></i></button>
  </div>

  <div class="row">
    <div class="light-font">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb" id="breadcrumb">
        </ol>
      </nav>
    </div>
  </div>
  <div class="row">
    <div id="expUnitGraph_header"></div>
    <div id="expUnitGraph">
      <script type="text/javascript" src="<?php echo js_url('display_trial/dataviz/animatedMap2') ?>"></script>
    </div>

  </div>