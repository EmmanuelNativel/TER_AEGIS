(function (d3) {

  /*============================================================================
                 Code d3js pour le multi graph des unités expérimentales
  =============================================================================*/

   var data = []; //variable locale à ce fichier qui va contenir les données
   var selectedUnitExp = [];
   var expUnitCodes = {};
   var selectedVariables = [];
   var selectedGraphHeight = $('#size_selectPicker :selected').val();

  /*
   *  Handler pour la selection des unités expérimentales
   */
  $('#unitExp_selectPicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

      var selected = $(this).find('option').eq(clickedIndex)
      var selectedUnitId = selected.val();
      var selectedUnitCode = selected.text();

      utils.arrayToggleValue(selectedUnitExp, selectedUnitId);

      //On maintient à jour la liste des unit_code disponibles pour pouvoir les afficher à tout moment (notamment dans le header)
      if (! expUnitCodes.hasOwnProperty(selectedUnitId)) expUnitCodes[selectedUnitId] = selectedUnitCode;

      //Lors de la séléction/désélection d'une unité expérimentale, on (re)charge les données d'observations
      // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
      var reloadData = true;
      onChange(reloadData)
  });

  /*
   *  Handler pour la selection des variables observées
   */
  $('#variables_selectPicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

      var selectedValue = $(this).find('option').eq(clickedIndex).val();

      utils.arrayToggleValue(selectedVariables, selectedValue);

      //Lors de la séléction/désélection d'une variable, on (re)charge les données d'observations
      // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
      var reloadData = true;
      onChange(reloadData)
  });

  /*
   *  Handler pour la selection de la taille du graphique
   */
  $('#size_selectPicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

      var selectedValue = $(this).find('option').eq(clickedIndex).val();
      selectedGraphHeight = selectedValue

      //Lors du changement de taille de graphique, on se contente de redessiner le graph
      var reloadData = false;
      onChange(reloadData);
  });


  /*
   * Fonction appelée à chaque changement de paramètres
   */
  function onChange(reloadData) {
    //Si pas d'unités expérimentales séléctionnées
    if (selectedUnitExp.length == 0) {
      cleanAllDiv()
      $("#expUnitGraph").html('<br><p> Veuillez sélectionner au moins une unité expérimentale... </p>');
    }
    //Si pas de variables séléctionnées
    else if (selectedVariables.length == 0) {
      cleanAllDiv()
      $("#expUnitGraph").html('<br><p> Veuillez sélectionner au moins une variable... </p>');
    }
    else {
      if (reloadData) loadExpUnitData(() => redraw()); else redraw();
    }
  }

  /*
    Fonction ajax permettant de charger les données d'observations pour toutes
    les unités expérimentales et les variables choisies
    (en utilisant les paramètres globaux) et de redessiner le tout.
  */
  function loadExpUnitData(onSuccessCallback) {

      $.ajax({
        url: SiteURL + '/Trials/ajaxLoadExpUnitData/',
        data: {
          selectedUnitExp: JSON.stringify(selectedUnitExp), //global var
          selectedVariables : JSON.stringify(selectedVariables) //global var
        },
        type: 'POST',
        dataType: 'json',
        success: function(response){
          data = response.exp_unit_data; //set global var data
          //console.log(data);
          parseData();
          onSuccessCallback();
        }
      });
  }

  /*
    Fonction pour parser les données après les avoir récupérées
    (utilise la variable locale 'data')
  */
  function parseData() {
    var parseDate = d3.utcParse("%Y-%m-%d %H:%M:%S%Z");

    data.forEach(function (d) {
      d.obs_value = +d.obs_value;
      d.obs_date = parseDate(d.obs_date);
    });
  }

  /*
    Fonction pour dessiner le header du graphique (noms des unités expérimentales + couleur)
  */
  function drawHeader() {
    //nettoyage du div
    var headerDivEl = document.getElementById("expUnitGraph_header");
    var headerDiv = d3.select(headerDivEl);
    headerDiv.html('');

    //Taille du header
    var margin = {top: 30, right: 20, bottom: 10, left: 30},
    width = headerDivEl.clientWidth - margin.left - margin.right;
    height = 20;

    // Création de couleurs variables
    var myColor = d3.scaleOrdinal().domain(selectedUnitExp)
      .range(utils.colorArray);

    var svg = headerDiv.append('svg')
                          .attr("width", width + margin.left + margin.right)
                          .attr("height", height + margin.top + margin.bottom)

    var g = svg.append("g")
                  .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


    //Ajout des noms des unités expérimentales en positionnant de manière intelligente
    // + passage à la ligne automatique

    var offsetX = 0;
    var offsetY = 0;
    var legendBlockWidth = 15;
    var verticalMargin = 5;
    var oneLineHeight = legendBlockWidth + verticalMargin;
    var textMargin = 5;

    g.selectAll('.legend').data(selectedUnitExp).enter().append('g')
      	.attr('class', 'legend')
      	.each(function(key, i) {
        	var item = d3.select(this);

        	var text = item.append('text')
                        	.attr('transform',
                                `translate(${(legendBlockWidth + textMargin)},${legendBlockWidth})`)
                        	.text(expUnitCodes[key]);

          item.append("rect")
              .attr("width", legendBlockWidth).attr("height", legendBlockWidth)
              .style("fill", myColor(key))

        	// Positionnement du groupe texte + couleur
          item.attr('transform',`translate(${offsetX} , ${offsetY})`);

        	// Si l'offsetX est dépassé on passe à une nouvelle ligne en redéplaçant le dernier item sur la nouvelle ligne
          var newItemWidth = item.node().getBBox().width
          if (offsetX + newItemWidth <= width) {
            offsetX += newItemWidth + textMargin;
          } else {
            offsetY += oneLineHeight
            item.attr('transform',`translate(0 , ${offsetY})`); //repositionnement sur la nouvelle ligne
            offsetX = newItemWidth + textMargin
          }
        });

      //Augmente la taille du svg à chaque nouvelle ligne
      var totalHeight = oneLineHeight + offsetY // hauteur 1ere ligne + décalage Y
      if (totalHeight > height ) svg.attr("height", totalHeight + margin.top + margin.bottom)

  }

  /*
    Fonction pour (re)dessiner tous les élements du graphe
  */
  window.addEventListener("resize", redraw); //listener pour redessiner lors du resize

  function redraw() {

    drawHeader()

    //nettoyage du div
    var globalDivEl = document.getElementById("expUnitGraph");
    var globalDiv = d3.select(globalDivEl);
    globalDiv.html('');

    //Pas de données
    if (data.length == 0) {
      globalDiv.html('<br><p> Aucune donnée pour cette unité expérimentale... </p>');
      return;
    }

    var margin = {top: 10, right: 250, bottom: 20, left: 30},
    width = globalDivEl.clientWidth - margin.left - margin.right,
    height = selectedGraphHeight - margin.top - margin.bottom;

    var xScale = d3.scaleTime()
        .range([0, width]);

    var yScales = []; //tableau qui contiendra un yScale différent pour chaque variable

    var area = (yScale) => d3.area()
        .x(function(d) { return xScale(d.obs_date); })
        .y0(height)
        .y1(function(d) { return yScale(d.obs_value); });

    var line = (yScale) => d3.line()
        .x(function(d) { return xScale(d.obs_date); })
        .y(function(d) { return yScale(d.obs_value); });

    // Nest data => regroupe les données par variable dans un premier temps
    var variablesData = d3.nest()
        .key(function(d) { return d.obs_variable; })
        .entries(data);

    // yScales : Calcul des valeurs maximales pour chaque variables afin de construire les yScales
    variablesData.forEach(function(s) {
        var maxValue = d3.max(s.values, (d) => d.obs_value);
        yScales.push(
            d3.scaleLinear().range([height, 0])
                            .domain([0, maxValue])
        );
    });
    //console.log(variablesData);

    // xScale : Calcul des dates minimale et maximale de l'essai afin de calculer le xScale global
    xScale.domain([
      d3.min(variablesData, function(s) {
        var beginDate =  d3.min(s.values, (d) => d.obs_date);
        return d3.timeMonth.offset(beginDate, -2); //le graphique commencera 2 mois avant la première mesure
      }),
      d3.max(variablesData, function(s) {
        var endDate = d3.max(s.values, (d) => d.obs_date);
        return d3.timeDay.offset(endDate, 5);
      })
    ]);

    //Après avoir calculé les x et y scales on nest par variable ET par exp_unit_id
    variablesData = d3.nest()
        .key(function(d) { return d.obs_variable; })
        .key(function(d) { return d.unit_id; })
        .entries(data);

    // Création de couleurs variables pour chaque unité expérimentale
    var myColor = d3.scaleOrdinal().domain(selectedUnitExp)
      .range(utils.colorArray);

    // Ajout d'un svg par variable qui contiendra un graphique
    svg = globalDiv.selectAll("svg").data(variablesData)
      .enter().append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    // Ajout des courbes
    svg.each(function (d,i) {
      d3.select(this)
        .selectAll('.line')
        .data(d.values)
        .enter()
        .append("path")
          .attr("class", "line")
          .attr("d", function(d1, i1) { return line(yScales[i])(d1.values); })
          .style("stroke", function (d1, i1) { return myColor(d1.key); });
    })

    // Ajout des points effectivement mesurés sur les lignes
    var circleRadius = 3;
    svg.each(function (d,i) {
      var svg = this;
      d3.select(svg)
        .selectAll('.line')
        .each(function (d1, i1) {
          d3.select(svg)
            .selectAll("myCircles")
              .data(d1.values)
              .enter()
              .append("circle")
                .attr("class", "circle")
                .attr("cx", function(d2) { return xScale(d2.obs_date) })
                .attr("cy", function(d2) { return yScales[i](d2.obs_value) })
                .attr("r", circleRadius)
                .style("stroke", function (d2) { return myColor(d1.key); });
        });
    });

    // Ajout d'un focusCircle par courbe
    // (rond rouge qui apparaitra quand notre curseur sera sur une valeur effectivement mesurée)
    svg.each(function (d,i) {
      var svg = this;
      d3.select(svg)
        .selectAll('.line')
        .each(function (d1, i1) {
            d3.select(svg)
              .append("circle")
                .attr("class", "circle focusCircle")
                .attr("r", 2*circleRadius)
                .style("display", "none");

        });
    });


    // Ajout d'indications à droite du graphique en fonction de la position du curseur

    var boxWidth =  0.75*margin.right; //en pixel
    var boxHeight = height + 0.8*margin.bottom//en pixel

    var indicatorsBox = svg.append("g")
                          .attr("transform", `translate(${width + margin.left + 10}, 0 )`);

    //background box
    indicatorsBox.append("rect")
                    .attr("class", "indicatorsBox")
                    .attr("width", boxWidth).attr("height", boxHeight );

    var dateLabel = indicatorsBox.append("text")
                      .attr("class", "indicatorDate")
                      .attr("text-anchor", "middle")
                      .attr("x", 0.5*boxWidth).attr("y", boxHeight - 10);

    var valueFontSize = 13; //px
    var smallRectSize = 0.8*valueFontSize;

    //Ajout d'un 'g' pour chaque petit carré et son value_label dans l'indicatorBox
    var valueContainer = indicatorsBox.selectAll('.valueContainer')
                                      .data(function(d) { return d.values })
                                      .enter()
                                      .append("g");

    var smallColorRect = valueContainer.append("rect")
                              .attr("width", smallRectSize).attr("height", smallRectSize)
                              .style("fill", function(d) { return myColor(d.key)})
                            .append("title")
                              .text(function(d) { return expUnitCodes[d.key] });

    //Ajout d'un label qui affichera la valeur pour chaque petit carré
    var valueLabel = valueContainer.append("text")
                      .attr("class", "valueLabel")
                      .attr("x", 1.2*smallRectSize)
                      .attr("y", 0.5*valueFontSize)
                      .style("font-size", valueFontSize + "px")
                      .text("-");

    var valueLabelWidth = 0.3*boxWidth; //utilisé plus tard pour "wrappé" le texte

    //Positionnement intelligent des valueContainer

    var maxNbValPerColumn = Math.floor((boxHeight - 30)/(1.2*valueFontSize)) //hauteur disponible divisé par hauteur d'une ligne
    var maxColNumber = 2

    valueContainer.each(function (d,i) {
      var colNumber = Math.floor(i/maxNbValPerColumn)
      var lineNumber = i % maxNbValPerColumn

      if ( (colNumber + 1) <= maxColNumber) {
        var thisValueContainer = d3.select(this)
              .attr("transform", `translate(${0.05*boxWidth + colNumber*(0.45*boxWidth)}, ${7 + lineNumber*(1.2*valueFontSize)} )`)

        if (colNumber == (maxColNumber - 1) && lineNumber == (maxNbValPerColumn - 1)) {
          thisValueContainer.selectAll("*").remove();

          //Si plus d'espace disponible on affiche un petit point d'interrogation avec une indication
          thisValueContainer.append("foreignObject")
                      .attr("width", "200px")
                      .attr("height", "200px")
                      .attr("x", 0*smallRectSize)
                      .attr("y", 0*valueFontSize)
                    .append("xhtml:div")
                      .style("font-size", valueFontSize + "px")
                      .html(`<button type="button" class="btn btn-default valueHelpBtn" data-container="body"
                              data-toggle="tooltip" data-placement="right"
                              title="Valeur(s) cachée(s). Veuillez augmenter la taille des graphiques.">
                                <span>?</span>
                             </button>`);

          $('[data-toggle="tooltip"]').tooltip()
        }
      } else {
        d3.select(this).attr("visibility", "hidden");
      }
    });

    // Ajout d'un label indiquant le nom de la variable à gauche du graphique
    var variableLabelG = svg.append("g")
                            .attr("transform", `translate(${6}, ${height - 6})`);
    var variableLabel = variableLabelG.append("text")
                            .attr("class", "variableLabelContainer")

    //Ajout du label de la variable
    variableLabel.append("tspan")
    .text(function(d) { return d.key; })
    .attr("class", "variableLabel");

    //Ajout de l'unité entre parenthèses (qui se trouve dans chaque observation, on prend donc la première observation de la première variable)
    variableLabel.append("tspan")
    .text(function(d) { return ` (${d.values[0].values[0].scale_code})` })
    .attr("class", "variableScaleCode");

    //Ajout d'un background semi transparent pour que le nom et l'unité restent visibles même s'il y a les courbes à l'arrière
    variableLabelG.each(function (d) {
      var bbox = this.getBBox();
      d3.select(this)
        .insert("rect","text")
          .attr("x", bbox.x).attr("y", bbox.y)
          .attr("width", bbox.width).attr("height", bbox.height)
          .style("fill", "white")
          .style("opacity", "0.8")
    })

    //Ajout des axes x et y
    var y = svg.append('g')
              .each( function (d,i) {
                d3.select(this)
                  .call(d3.axisRight(yScales[i]).ticks(Math.floor(selectedGraphHeight/30)))
              })
              .attr('transform', `translate(${width},0)`);

    var x = svg.append('g')
              .call(d3.axisBottom(xScale).tickFormat(utils.multiFormat))
              .attr('transform', `translate(0,${height})`);

    // ------------------- Gestion de l'interactivité ------------------------

    //Idée : Récupération de la position du globalDiv puis ajout d'un overlayBox
    //en position absolue qui capturera les mouvements de souris

    var globalDivPos = d3.select('#expUnitGraph').node().getBoundingClientRect();

    var overlayBox = globalDiv.append("svg")
                      .attr("id", "verticalLineContainer")
                      .style("top", (globalDivPos.top + window.scrollY) + "px")
                      .style("left", (globalDivPos.left + margin.left + window.scrollX) + "px")
                      .style("width", width + "px")
                      .style("height", (globalDivPos.height - margin.top)  +"px")

    ///Ajout d'une ligne verticale permettant de parcourir les graphiques
    var lineIndicator = overlayBox.append("line")
                .attr("id", "verticalLine")
                .attr("x1", globalDivPos.x)
                .attr("y1", 0)
                .attr("x2", globalDivPos.x)
                .attr("y2", globalDivPos.height);

    // Gestion du mouvement de la souris

    var formatTime = d3.timeFormat("%d %b %Y")
    var bisectDate = d3.bisector(function(d) { return d.obs_date; }).left;
    var catchDistance = 5 //px;

    overlayBox.append('rect')
      .attr('id', 'mouseRectangle')
      .attr('width', "100%")
      .attr('height', "100%")
      .on('mouseover', function() { lineIndicator.style('display', null); })
      .on('mouseout', function() { lineIndicator.style('display', 'none'); })
      .on('mousemove', function() {

          var mouse = d3.mouse(this);

          //Bouge la ligne verticale
          lineIndicator.attr("x1", mouse[0]).attr("x2", mouse[0]);

          //affichage de la date dans le cadran
          var mouseDate = xScale.invert(mouse[0]);
          dateLabel.text(formatTime(mouseDate));

          // On parcours chaque graphique, et pour chaque graphique on va afficher
          // les bonnes valeurs à droite et aussi on va afficher les cercles rouges ou non
          // en fonction de la position de la barre verticale.
          svg.each(function (d,i) {

            var linesValues = []; //le valueLabel à afficher pour chaque ligne
            var focusPoints = []; //Les coordonnées du focus circle pour chaque ligne

            d3.select(this)
              .selectAll('.line')
              .each( function (d1, i1) {
                  var currentLine = this;

                  //idée: pour chaque graphique on regarde si notre ligne verticale
                  // se trouve sur une valeur effectivement mesurée. Si c'est le cas alors
                  //on affiche la vraie valeur en surlignant le cercle en rouge,
                  //sinon on affiche la valeur "interpolée"

                  var idx = bisectDate(d1.values, mouseDate); //calcul de l'indice où la date du curseur serait potentiellement ajouté
                                                              //cela permet de récupérer l'élement précédent et le suivant

                  var leftVal = d1.values[Math.max(0, idx - 1)],
                      leftValX = xScale(leftVal.obs_date);

                  var rightVal = d1.values[Math.min(idx, d1.values.length - 1)],
                      rightValX = xScale(rightVal.obs_date);

                  //Calcul des distances par rapport aux points à gauche et à droite, ainsi on pourra
                  //savoir quel point surligner en rouge s'il est assez près
                  var mouseDistLeft = Math.abs(leftValX - mouse[0]),
                      mouseDistRight = Math.abs(rightValX - mouse[0]);

                  if (mouseDistLeft <= catchDistance && mouseDistLeft <= mouseDistRight) { //Le point à gauche doit être surligné
                    linesValues[i1] = leftVal.obs_value;
                    focusPoints[i1] = { x: leftValX, y: yScales[i](leftVal.obs_value) }
                    lineIndicator.attr("x1", leftValX).attr("x2", leftValX);
                    dateLabel.text(formatTime(leftVal.obs_date));
                  }
                  else if (mouseDistRight <= catchDistance && mouseDistRight <= mouseDistLeft) { //Le point à droite doit être surligné
                    linesValues[i1] = rightVal.obs_value;
                    focusPoints[i1] = { x: rightValX, y: yScales[i](rightVal.obs_value) }
                    lineIndicator.attr("x1", rightValX).attr("x2", rightValX);
                    dateLabel.text(formatTime(rightVal.obs_date));
                  }
                  else { //Une valeur interpolée doit être calculée
                    var point = utils.getPointAtX(currentLine, mouse[0]);
                    linesValues[i1] = (point != null) ? yScales[i].invert(point.y) : "-";
                    focusPoints[i1] = null;
                  }
              });

              //Grâce au tableau focusPoints, on a les coordonnées des focusCircles à afficher
              d3.select(this)
                .selectAll('.focusCircle')
                  .attr('cx', function(d1, i1) { return (focusPoints[i1] != null) ? focusPoints[i1].x : 0})
                  .attr('cy', function(d1, i1) { return (focusPoints[i1] != null) ? focusPoints[i1].y : 0})
                  .style("display", function (d1, i1) { return (focusPoints[i1] != null) ? null : "none" } );

              //Grâce au linesValues on peut désormais afficher la bonne valeur pour la bonne courbe
              d3.select(this)
                .selectAll('.valueLabel')
                  .text(function (d1, i1) { return(linesValues[i1]) })
                  .each(function(d1, i1) {
                      //Après avoir set la value on coupe le texte à la bonne taille sinon ne pourra pas tenir sur 2 colonnes
                      var self = d3.select(this);
                      self.text( utils.getWrappedText(self, valueLabelWidth) );
                  });
          });
    });

  } //end redraw()

  function cleanAllDiv() {
    $("#expUnitGraph").html("");
    $("#expUnitGraph_header").html("");
  }

}(d3)); //end of this file
