(function (d3) {
  /*============================================================================
                     Code d3js pour le multi graph des unités expérimentales
      =============================================================================*/
  //var selectedGraphHeight = $("#size_selectPicker :selected").val();
  var trialCode = $("#dataviz").attr("trial_code"); // Récupère le code de l'essai courant
  var data = { name: trialCode, children: [] }; //variable locale à ce fichier qui va contenir les données
  let selectedFactors = []; //Liste des facteurs
  let selectedVariable = null; // Valeur à observer
  let date = null;
  let svgAnimation = null;
  let svgInfo = null;
  let pathElement = null;
  let currentElement = null;

  drawSlider();

  /**
   *  Handler pour la selection des facteurs
   */
  $("#factor_selectPicker").on("changed.bs.select", function (
    e,
    clickedIndex,
    isSelected,
    previousValue
  ) {
    var selected = $(this).find("option").eq(clickedIndex);
    //var selectedFactorId = selected.val();
    var selecFactor = selected.text();
    utils.arrayToggleValue(selectedFactors, selecFactor);

    //Lors de la séléction/désélection d'un facteur, on (re)charge les données d'observations
    // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
    var reloadData = true;
    onChange(reloadData);
  });

  /*
   *  Handler pour la selection de la variable à observer
   */
  $("#variable_selectPicker").on("changed.bs.select", function (
    e,
    clickedIndex,
    isSelected,
    previousValue
  ) {
    var selectedValue = $(this).find("option").eq(clickedIndex).val();
    selectedVariable = selectedValue;
    //Lors de la séléction/désélection d'une variable, on (re)charge les données d'observations
    // liée à cette unité, puis on refresh le graphique D3.js avec les nouvelles données.
    var reloadData = true;
    onChange(reloadData);
  });

  /**
   * Fonction appelée à chaque changement de paramètres
   */
  function onChange(reloadData) {
    if (selectedFactors.length === 0) {
      cleanAllDiv();
      $("#expUnitGraph").html(
        "<br><p> Veuillez sélectionner au moins un facteur... </p>"
      );
    } else if (reloadData) {
      loadData(() => redraw());
    } else redraw();
  }

  // On redessine la dataviz quand on redimensionne la fenêtre
  window.addEventListener("resize", redraw); //listener pour redessiner lors du resize

  /**
   * Efface tout
   */
  function cleanAllDiv() {
    $("#expUnitGraph").html("");
    $("#expUnitGraph_header").html("");
  }

  /**
   * Fonction ajax permettant de charger les données pour construire l'arbre (blocs et parcelles associées)
   */
  function loadData(onSuccessCallback) {
    $.ajax({
      url: SiteURL + "/Trials/ajaxLoadDataForAnimatedMap/",
      data: {
        //trialCode: JSON.stringify("Matrice_Andrano_0304"),
        trialCode: JSON.stringify(trialCode), //global var
        factors: JSON.stringify(selectedFactors),
        obs_value: JSON.stringify(selectedVariable),
      },
      type: "POST",
      dataType: "json",
      success: function (response) {
        //data.children = response.expData; // data est une variable globale
        prepareData(response.expData, response.expValues);
        //parseData();
        onSuccessCallback();
      },
    });
  }

  /**
   * Mise en forme des données pour qu'elles soient utilisables par la dataviz
   * Effet de bord : Insertion des données dans la variable globale data
   */
  function prepareData(dataExp, values) {
    // Récupère les parents (les blocs)
    const reducer = (accumulator, currentValue) =>
      accumulator.add(currentValue.parent_unit_code);
    const parents = Array.from(dataExp.reduce(reducer, new Set()));

    // Insertion des valeurs et de leurs dates dans chaque parcelle
    const dataWithValues = dataExp.map((d) => {
      const valuesChildren = values.filter(
        (child) => child.exp_unit_id === d.exp_unit_id
      );
      const valuesNeeded = valuesChildren.map((v) => ({
        date: v.date,
        value: v.value,
      }));
      return { ...d, values: valuesNeeded };
    });

    // A chaque bloc on y ajoute ses parcelles
    const hierarchy = parents.map((p) => {
      return {
        name: p,
        children: dataWithValues.filter((d) => d.parent_unit_code === p),
      };
    });

    // On ajoute l'essai en racine de la hiérarchie
    data = {
      //name: "Matrice_Andrano_0304",
      name: trialCode,
      children: hierarchy,
    };
    console.log("data", data);
  }

  function resetDisplay() {
    svgAnimation.selectAll("rect").remove();
    svgAnimation.selectAll("text").remove();
    svgInfo.selectAll("text").remove();
    //pass.select("text").text("data")
  }

  function redraw() {
    //on supprime les svg avant de réafficher
    var globalDivEl = document.getElementById("expUnitGraph");
    var globalDiv = d3.select(globalDivEl);
    globalDiv.html("");

    // La div contenant la visualisation
    var divBlocs = document.createElement("div");
    divBlocs.id = "BlocAnimation";
    globalDivEl.appendChild(divBlocs);

    // Calcul des tailles
    const div_id = "expUnitGraph";
    const taill_coef = 2 / 3;
    var agr = 0.8;
    const cote = document.getElementById(div_id).clientWidth * taill_coef * agr;

    const taill_coef_info = 1 / 3;
    const cote_info =
      document.getElementById(div_id).clientWidth * taill_coef_info * agr - 20;

    const margin = { top: 0, right: 0, bottom: 0, left: 0 };
    const width = cote;
    const height = cote;

    //Création du svg de la visualisation
    svgAnimation = d3
      .select("#" + divBlocs.id)
      .append("svg")
      .attr("width", width + margin.left + margin.right + 5)
      .attr("height", height + margin.bottom + margin.top + 50)
      .style("border", "1px solid red")
      .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    // Création du svg chargé d'afficher les informations
    svgInfo = d3
      .select("#" + divBlocs.id)
      .append("svg")
      .attr("x", 100)
      .attr("width", cote_info)
      .attr("height", height + 50)
      .style("border", "1px solid blue");

    //Modification des données pour être plus simple a les traité
    const pack = (d) =>
      d3.pack().size([width, height]).padding(2)(d3.hierarchy(d));

    const root = pack(data);

    // drawPath(width, height);
    drawBlocs(root.children, width, height);
  }

  /**
   * Dessin des blocs
   */
  function drawBlocs(currentSelection, width, height) {
    currentElement = currentSelection;
    const nb = currentSelection.length;
    const maxRectInLine = Math.ceil(Math.sqrt(nb));
    const rectWidth = width / maxRectInLine;
    const rectHeight = height / maxRectInLine;
    const rectPadding = 2;

    //var qui contient la svgAnimation qui gères l'affichage des parcelles
    var square = svgAnimation
      .selectAll("rect")
      .data(currentSelection)
      .enter()
      .append("rect")
      .attr("x", (d, i) => {
        return (i % maxRectInLine) * rectWidth + 1;
      })
      .attr("y", (d, i) => {
        return Math.trunc(i / maxRectInLine) * rectHeight + 45;
      })
      .attr("width", rectWidth - rectPadding)
      .attr("height", rectHeight - rectPadding)
      .attr("fill", "green");

    var labels = svgAnimation
      .selectAll("text")
      .data(currentSelection)
      .enter()
      .append("text")
      .text((d) => d.data.name)
      .attr("x", (d, i) => {
        return (i % maxRectInLine) * rectWidth + 1 + rectWidth / 2;
      })
      .attr("y", (d, i) => {
        return Math.trunc(i / maxRectInLine) * rectHeight + 45 + rectHeight / 2;
      })
      .attr("font-size", "11px")
      .attr("fill", "white")
      .attr("text-anchor", "middle");

    //fct click sur les rectangles
    square.on("click", (d, i) => {
      //condition pour éviter de descendre plus bas que la feuille
      if (d.depth != 2) {
        //ex_path = d.parent; // -> On récuperer les parents de "d" qu'on stock dans une var global
        //printPass(d)// Ne fonctionne pas dans cette fonction
        resetDisplay();
        drawBlocs(d.children, width, height);
      } else {
        updateInformations(d);
      }
    });
  }

  function updateInformations(d) {
    if (d.depth == 2) {
      svgInfo.selectAll("text").remove();
      var i = 1;
      $.each(d.data, function (key, val) {
        // console.log(key + " : " + val);
        if (key === "values") {
          var parseDate = d3.utcParse("%Y-%m-%d %H:%M:%S%Z");
          const valueMatched = val
            .filter((v) => parseDate(v.date) <= date)
            .slice(-1)[0];
          // val = `${parseDate(valueMatched.date)} => ${valueMatched.value}`
          val = valueMatched.value;
        }
        svgInfo
          .append("text")
          .attr("x", 20)
          .attr("y", i * 30)
          .text(val);
        i++;
      });
    }
  }

  /**
   * Gestion du slider
   */
  function drawSlider() {
    var margin = { top: 0, right: 50, bottom: 0, left: 50 };
    var width = 500;
    var height = 200;

    var formatDateIntoYear = d3.timeFormat("%Y");
    var formatDate = d3.timeFormat("%b %Y");
    var parseDate = d3.timeParse("%m/%d/%y");

    var startDate = new Date("2004-11-01"),
      endDate = new Date("2017-04-01");

    var svgSlider = d3
      .select("#slider")
      .append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height);

    var x = d3
      .scaleTime()
      .domain([startDate, endDate])
      .range([0, width])
      .clamp(true);

    var slider = svgSlider
      .append("g")
      .attr("class", "slider")
      .attr("transform", "translate(" + margin.left + "," + height / 2 + ")");

    slider
      .append("line")
      .attr("class", "track")
      .attr("x1", x.range()[0])
      .attr("x2", x.range()[1])
      .select(function () {
        return this.parentNode.appendChild(this.cloneNode(true));
      })
      .attr("class", "track-inset")
      .select(function () {
        return this.parentNode.appendChild(this.cloneNode(true));
      })
      .attr("class", "track-overlay")
      .call(
        d3
          .drag()
          .on("start.interrupt", function () {
            slider.interrupt();
          })
          .on("start drag", function () {
            update(x.invert(d3.event.x));
          })
      );

    slider
      .insert("g", ".track-overlay")
      .attr("class", "ticks")
      .attr("transform", "translate(0," + 18 + ")")
      .selectAll("text")
      .data(x.ticks(10))
      .enter()
      .append("text")
      .attr("x", x)
      .attr("y", 10)
      .attr("text-anchor", "middle")
      .text(function (d) {
        return formatDateIntoYear(d);
      });

    var handle = slider
      .insert("circle", ".track-overlay")
      .attr("class", "handle")
      .attr("r", 9);

    var label = slider
      .append("text")
      .attr("class", "label")
      .attr("text-anchor", "middle")
      .text(formatDate(startDate))
      .attr("transform", "translate(0," + -25 + ")");

    function update(h) {
      // update position and text of label according to slider scale
      handle.attr("cx", x(h));
      label.attr("x", x(h)).text(formatDate(h));

      date = h;
      drawBlocs(currentElement, width, height);
    }
  }

  /**
   * Gestion du fil d'ariane
   */
  function drawPath(width, height) {
    var margin = { top: 0, right: 0, bottom: 0, left: 0 };
    pathElement = svgAnimation.append("g").attr("class", "ClassforText");

    pathElement
      .append("rect")
      .attr("width", width + margin.left + margin.right)
      .attr("height", 40)
      .attr("fill", "lightgrey");

    pathElement
      .append("text")
      .attr("x", 6)
      .attr("y", 6 - margin.top)
      .attr("dy", ".75em")
      //.text("Back <==")
      .text(() => {
        return "<== ";
      });

    //On ajoute une fct Onclick sur le le svg pass
    // pathElement.on("click", () => {
    //   if (ex_path.depth != 1) {
    //     resetDisplay();
    //     pathElement.select("text").text("<==");
    //     resetDisplay();
    //     display(ex_path.children); // retourne au parent
    //   }
    // });

    //affiche le nom de la données dans le svg pass
    // function printPass(d) {
    //   pass.select("text").text(d.data.name);
    // }

    //function hoverclick
    // function handleMouseOver(d, i) {
    //   //d et i pour des futures modifications
    //   /**Idée de function :
    //    * affiche les donneés de la parcelles (+horizons)
    //    * affiche les logos liées au données
    //    */
    //   printPass(d);
    // }
  }
})(d3); //end of this file
