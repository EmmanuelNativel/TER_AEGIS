(function (d3) {

  /*============================================================================
                 Code d3js pour le graphique 2D d'unités expérimentales
  =============================================================================*/
  var trial_code = $('#dataviz').attr('trial_code');
  var data = []; //variable locale à ce fichier qui va contenir les données
  var selectedUnitExp = [];
  var expUnit_extraData = null;
  var selectedVariableX = null;
  var selectedVariableY = null;

  /*
   *  Handler pour la selection des unités expérimentales
   */
  $('#unitExp_selectPicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
      var selected = $(this).find('option').eq(clickedIndex)
      if (selected.length > 0) { //En théorie c'est qu'on a cliqué sur un seul element
        var selectedUnitId = selected.val();
        var selectedUnitCode = selected.text();
        utils.arrayToggleValue(selectedUnitExp, selectedUnitId);
      }
      else { //En théorie c'est qu'on a cliqué sur le bouton select all ou deselect all
        selectedUnitExp = $('#unitExp_selectPicker').val() || [];
      }
      //Lors de la séléction/désélection d'une unité expérimentale, on (re)charge les données d'observations
      // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
      var reloadData = true;
      onChange(reloadData)
  });

  /*
   *  Handler pour la selection de la variable X
   */
  $('#variableX_selectPicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
      var selectedValue = $(this).find('option').eq(clickedIndex).val();
      selectedVariableX = selectedValue;
      //Lors de la séléction/désélection d'une variable, on (re)charge les données d'observations
      // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
      var reloadData = true;
      onChange(reloadData)
  });

  /*
   *  Handler pour la selection de la variable Y
   */
  $('#variableY_selectPicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
      var selectedValue = $(this).find('option').eq(clickedIndex).val();
      selectedVariableY = selectedValue;
      //Lors de la séléction/désélection d'une variable, on (re)charge les données d'observations
      // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
      var reloadData = true;
      onChange(reloadData)
  });

  /*
   * Fonction appelée à chaque changement de paramètres
   */
  function onChange(reloadData) {
    //Si pas d'unités expérimentales séléctionnées
    if (selectedUnitExp.length == 0) {
      cleanAllDiv()
      $("#expUnit2D").html('<br><p> Veuillez sélectionner au moins une unité expérimentale... </p>');
    }
    //Si pas de variable x
    else if (selectedVariableX == null) {
      cleanAllDiv()
      $("#expUnit2D").html('<br><p> Veuillez sélectionner une variable en x... </p>');
    }
    //Si pas de variable y
    else if (selectedVariableY == null) {
      cleanAllDiv()
      $("#expUnit2D").html('<br><p> Veuillez sélectionner une variable en y... </p>');
    }
    //Si même variables selectionnées
    else if (selectedVariableY == selectedVariableX) {
      cleanAllDiv()
      $("#expUnit2D").html('<br><p> Veuillez sélectionner deux variables différentes en x et y... </p>');
    }
    else {
      if (reloadData) loadData(() => redraw()); else redraw();
    }
  }

  function loadData(onSuccessCallback) {
    //Chargement des données principales des unités expérimentales
    $.ajax({
      url: SiteURL + '/Trials/ajaxLoadExpUnitData/',
      data: {
        selectedUnitExp: JSON.stringify(selectedUnitExp), //global var
        selectedVariables : JSON.stringify([selectedVariableX, selectedVariableY]) //global var
      },
      type: 'POST',
      dataType: 'json',
      success: function(response){
        data = response.exp_unit_data; //set global var data
        //console.log(data);
        parseData();
        //Si premiere fois (extradata == null) on charge les extra data utilisés lors des hover
        if (expUnit_extraData == null) loadExtraData(onSuccessCallback); else onSuccessCallback();
      }
    });
  }

  function loadExtraData(onSuccessCallback) {
    //Chargement des données du dispositif expérimental (pour les infos supplémentaires lors du hover)
    $.ajax({
      url: SiteURL + '/Trials/ajaxLoadDispExp/' + trial_code,
      type: 'get',
      dataType: 'json',
      success: function(response){
        var dispExp_data = response.dispExp;
        //Création de la variable globale expUnit_extraData en lui donnant la structure voulue
        expUnit_extraData = {};
        dispExp_data.forEach(function (d) {
          //si nouvel unit_id on ajoute toutes les infos
          if ( ! expUnit_extraData.hasOwnProperty(d.exp_unit_id) ) {
            expUnit_extraData[d.exp_unit_id] = {
              unit_code: d.unit_code,
              factors: {
                [d.factor]: {
                  level: d.factor_level,
                  description: d.factor_level_description
                }}}
          }
          else {
          //si unit_id existe déjà on ajoute le nouveau facteur
              expUnit_extraData[d.exp_unit_id].factors[d.factor] = {
                level: d.factor_level,
                description: d.factor_level_description
              };
          }
        });
        onSuccessCallback(); //traitement terminé donc on appelle le callback
      }// end success
    });
  }

  /*
    Fonction pour parser les données après les avoir récupérées
    (utilise la variable locale 'data')
  */
  function parseData() {

    //Conversion des strings en "vraies" valeurs (parsing)
    var parseDate = d3.utcParse("%Y-%m-%d %H:%M:%S%Z");
    data.forEach(function (d) {
      d.obs_value = +d.obs_value;
      d.obs_date = parseDate(d.obs_date);
    });

    //Groupage de données par unit_exp puis par nom de variable
    // et on ne garde que la dernière observation (les observations doivent être triées par date)
    data = d3.nest()
        .key(function(d) { return d.unit_id; })
        .key(function(d) { return d.obs_variable; })
        .rollup(function(v) { return v[v.length - 1]; })
        .entries(data);
  }

  /*
    Fonction pour (re)dessiner tous les élements du graphe
  */
  window.addEventListener("resize", redraw); //listener pour redessiner lors du resize

  function redraw() {
    //nettoyage du div
    var globalDivEl = document.getElementById("expUnit2D");
    var globalDiv = d3.select(globalDivEl);
    globalDiv.html('');

    //Pas de données
    if (data.length == 0) {
      globalDiv.html('<br><p> Aucune donnée pour cette unité expérimentale... </p>');
      return;
    }

    //========================================== Initialisation des echelles ,
    // du zoom et du svg principal

    var margin = {top: 30, right: 30, bottom: 30, left: 60},
    width = globalDivEl.clientWidth - margin.left - margin.right,
    height = 450 - margin.top - margin.bottom;

    var maxXValue = d3.max(data, (d) => d.values[0].value.obs_value);
    var xScale = d3.scaleLinear().range([0, width])
                                 .domain([0, 1.1*maxXValue]);

    var maxYValue = d3.max(data, (d) => d.values[1].value.obs_value);
    var yScale = d3.scaleLinear().range([height, 0])
                                 .domain([0, 1.1*maxYValue]);

   //Définition du zoom D3
   var zoom = d3.zoom()
       .scaleExtent([.5, 20])  //Entre quelles echelles nous pouvons zoomer
       .extent([[0, 0], [width, height]])
       .wheelDelta(() => -d3.event.deltaY * (d3.event.deltaMode ? 120 : 1) / 3000) //calcul de la vitesse du zoom en fonction de la molette (voir documentation)
       .on("zoom", onZoom); //appelle onZoom() lors du zoom

   // Ajout du svg global et initialisation du zoom
   var svg = globalDiv.append("svg")
       .attr("width", width + margin.left + margin.right)
       .attr("height", height + margin.top + margin.bottom)
       .call(zoom)
     .append("g")
       .attr("transform", "translate(" + margin.left + "," + margin.top + ")")

    //======================================================== Création des axes

    //Ajout des axes x et y
    var y = svg.append('g')
               .call(d3.axisLeft(yScale).ticks(Math.floor(height/30)));

    var x = svg.append('g')
               .call(d3.axisBottom(xScale).ticks(Math.floor(width/60)))
               .attr('transform', `translate(0,${height})`);

    //Ajout des unités pour chaque axe
    //y
    svg.append("text")
       .text(`(${data[0].values[1].value.scale_code})`)
       .attr("x", 10).attr("y", 10)
       .attr("class", "variableScaleCode");
    //x
    svg.append("text")
      .text(`(${data[0].values[0].value.scale_code})`)
      .attr("text-anchor","end")
      .attr("x", width - 10 ).attr("y", height - 10)
      .attr("class", "variableScaleCode");

    //=========================== Création de la zone dessinable du graphique
    //  et de la grille en background

    // Ajout d'un clipPath: tout ce qui sera en dehors ne sera pas dessiné.
    // (sert notamment lors du drag pour cacher les éléments qui se retrouvent hors de la zone)
    var clip = svg.append("defs").append("SVG:clipPath")
        .attr("id", "clip")
        .append("SVG:rect")
        .attr("width", width )
        .attr("height", height )
        .attr("x", 0)
        .attr("y", 0);

    var graphArea = svg.append('g')
      .attr("clip-path", "url(#clip)")

   graphArea.selectAll("line.horizontalGrid").data(yScale.ticks(Math.floor(height/60))).enter()
       .each(function (d) {
          if (d>0) {
            d3.select(this)
              .append("line")
              .attr("class", "gridLine")
              .attr( "x1" , 0).attr("x2", width)
              .attr("y1", function(d){ return yScale(d) })
              .attr("y2", function(d){ return yScale(d) });
          }
        })

    graphArea.selectAll("line.verticalGrid").data(xScale.ticks(Math.floor(width/60))).enter()
        .each(function (d) {
           if (d>0) {
             d3.select(this)
               .append("line")
               .attr("class", "gridLine")
               .attr("x1", function(d){ return xScale(d) })
               .attr("x2", function(d){ return xScale(d) })
               .attr("y1", 0)
               .attr("y2", height);
           }
         })

    //=========================== Ajout des symboles (triangle) sur le graphique
     var triangle = d3.symbol()
             .type(d3.symbolTriangle)
             .size(30);

     var tooltip = d3.select("body")
            .append("div")
            .attr("id", "expUnit2D_tooltip")
            .text("a simple tooltip");

     var symbols = graphArea.selectAll(".symbols")
                      .data(data)
                      .enter()
                        .append("g")
                        .attr("transform", function(d) { return "translate(" + xScale(d.values[0].value.obs_value) + "," +
                                                                               yScale(d.values[1].value.obs_value) + ")"; });

     symbols.append("path")
            .attr("d", triangle)
            .attr("stroke", "red")
            .attr("fill", "red");

     symbols.append("text")
            .attr("x", 10)
            .attr("y", 5)
            .text(function (d) {
                return expUnit_extraData[d.values[0].value.unit_id].unit_code;
            });

      var formatTime = d3.timeFormat("%d %b %Y")
      symbols.on("mouseover", function(d){
                tooltipData = expUnit_extraData[d.values[0].value.unit_id];
                tooltip.html(
                `
                 <div class="tooltipTitle">${tooltipData.unit_code}</div>
                 <ul class="list-unstyled">
                    <li>
                        <span class="tooltipLi">x :</span>
                        <div style="display:inline-table">
                          <span class="tooltipItemText">${ftrunc(d.values[0].value.obs_value, 4)} ${d.values[0].value.scale_code}</span>
                          <br>
                          <span class="tooltipObsDate">(${formatTime(d.values[0].value.obs_date)})</span>
                        </div>
                    </li>
                    <li>
                        <span class="tooltipLi">y :</span>
                        <div style="display:inline-table">
                          <span class="tooltipItemText">${ftrunc(d.values[1].value.obs_value, 4)} ${d.values[1].value.scale_code}</span>
                          <br>
                          <span class="tooltipObsDate">(${formatTime(d.values[1].value.obs_date)})</span>
                        </div>
                    </li>
                    <li>
                        <span class="tooltipLi">Facteurs :</span><br>
                        ${Object.entries(tooltipData.factors).map(([factorName, factorLevel]) => {
                            return `<span class="tooltipFactorName">${factorName} :</span>
                                    <span class="tooltipFactorLevel">${factorLevel.level}</span>`;
                        }).join(" ")}
                    </li>
                 </ul>
                `);
                tooltip.style("visibility", "visible");

              })
             .on("mousemove", function(d){ tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX + 30)+"px");})
             .on("mouseout", function(d){ tooltip.style("visibility", "hidden");});

      //Fonction appelée lors du zoom
      function onZoom() {
        var newXScale = d3.event.transform.rescaleX(xScale);
        var newYScale = d3.event.transform.rescaleY(yScale);
        // mise à jour des axes
        x.call(d3.axisBottom(newXScale))
        y.call(d3.axisLeft(newYScale))
        // mise à jour de la position des symboles
        symbols.attr("transform", function(d) { return "translate(" + newXScale(d.values[0].value.obs_value) + "," +
                                                              newYScale(d.values[1].value.obs_value) + ")"; });
      }

  } //end redraw()

  function ftrunc(float, max_nb_decimals) {
    return +parseFloat((float).toFixed(max_nb_decimals));
  }

  function cleanAllDiv() {
    $("#expUnit2D").html("");
  }

}(d3)); //end of this file
