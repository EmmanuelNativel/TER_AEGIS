(function (d3) {
  let obs_values = []; // Les valeurs observées pour les facteurs et la variable choisie
  let VALUES = [];
  let selected_date = null; // La date sélectionnée
  let current_element = {}; // L'élément courant dans l'animation
  let current_values = []; // Les valeurs filtrées par la date sélectionnée
  let dateMin = null;
  let dateMax = null;
  let valueMin = null;
  let valueMax = null;
  const trialCode = $("#dataviz").attr("trial_code"); // Le code de l'essai courant
  let selectedFactors = []; // Liste des facteurs sélectionnés
  let selectedVariable = null; // La valeur à observer sélectionnée
  const div_id = "expUnitGraph";
  let WIDTH = document.getElementById(div_id).clientWidth;
  let HEIGHT = document.getElementById(div_id).clientWidth;
  let svgAnimation = null; // SVG contenant l'animation
  const MIN_COLOR = "#EAFAF1";
  const MAX_COLOR = "#186A3B";
  const DEFAULT_COLOR = "#17202A";
  const BACKGROUND_COLOR = "#AAB7B8";
  const NULL_COLOR = "yellow";
  let path = [];

  /**
   * Sélection des facteurs :
   * Handler pour la selection des facteurs
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
    onChange();
  });

  /**
   * Sélection de la variable à observer :
   * Handler pour la selection de la variable à observer
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
    onChange(/*() => drawSlider(dateMin, dateMax)*/);
  });

  function onChange(optionalCallback = () => {}) {
    if (selectedFactors.length === 0) {
      $("#expUnitGraph").html(
        "<br><p> Veuillez sélectionner au moins un facteur... </p>"
      );
    } else {
      drawSVG();
      loadHierarchy(() => {
        drawChildren(current_element.children);
        getPath(current_element);
        optionalCallback();
      });
    }
  }

  // On redessine la dataviz quand on redimensionne la fenêtre
  // window.addEventListener("resize", drawChildren); //listener pour redessiner lors du resize

  function drawSVG() {
    var globalDivEl = document.getElementById("expUnitGraph");
    var globalDiv = d3.select(globalDivEl);
    globalDiv.html("");

    const margin = { top: 0, right: 0, bottom: 0, left: 0 };

    //Création du svg de la visualisation
    svgAnimation = globalDiv
      .append("svg")
      // .attr("width", WIDTH + margin.left + margin.right + 5)
      // .attr("height", HEIGHT + margin.bottom + margin.top + 50)
      .attr("width", globalDiv.style("width"))
      .attr("height", globalDiv.style("width"))
      .style("background-color", BACKGROUND_COLOR)
      .style("border", "1px solid red")
      .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
  }

  function loadHierarchy(onSuccessCallback) {
    $.ajax({
      url: SiteURL + "/Trials/ajaxLoadDataForAnimatedMap/",
      data: {
        trialCode: JSON.stringify(trialCode),
        factors: JSON.stringify(selectedFactors),
        obs_value: JSON.stringify(selectedVariable),
      },
      type: "POST",
      dataType: "json",
      success: function (response) {
        const root = prepareHierarchy(response.expData); // Préparation de la hiérarchie
        current_element = root; // On indique l'élément courant
        // VALUES = response.expValues;
        // obs_values = prepareValues(response.expValues); // Préparation des valeurs
        // current_values = JSON.parse(JSON.stringify(obs_values));
        onSuccessCallback(); // On va afficher les enfants de l'élément courant
      },
    });
  }

  function loadValues(parentName, onSuccessCallback) {
    $.ajax({
      url: SiteURL + "/Trials/ajaxLoadValuesForAnimatedMap/",
      data: {
        trialCode: JSON.stringify(trialCode),
        obs_value: JSON.stringify(selectedVariable),
        parent_name: JSON.stringify(parentName),
      },
      type: "POST",
      dataType: "json",
      success: function (response) {
        VALUES = response.expValues;
        obs_values = prepareValues(response.expValues); // Préparation des valeurs
        current_values = JSON.parse(JSON.stringify(obs_values)); // UTIL ? meme chose que obs_values
        onSuccessCallback(); // On va afficher les enfants de l'élément courant
      },
    });
  }

  function prepareData(dataH, dataV) {
    // Récupération des premiers enfants (les blocs)
    const reducer = (accumulator, currentValue) =>
      accumulator.add(currentValue.parent_unit_code);
    const parents = Array.from(dataH.reduce(reducer, new Set()));

    var parseDate = d3.utcParse("%Y-%m-%d %H:%M:%S%Z");

    // Insertion des valeurs et de leurs dates dans chaque parcelle
    const dataWithValues = dataH.map((d) => {
      const valuesChildren = dataV.filter(
        (child) => child.exp_unit_id === d.exp_unit_id
      );
      const valuesNeeded = valuesChildren.map((v) => ({
        date: parseDate(v.date),
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
      name: trialCode,
      children: hierarchy,
    };

    const root = d3.hierarchy(data);

    return root;
  }

  function prepareHierarchy(data) {
    // Récupération des premiers enfants (les blocs)
    const reducer = (accumulator, currentValue) =>
      accumulator.add(currentValue.parent_unit_code);
    const parents = Array.from(data.reduce(reducer, new Set()));

    // A chaque bloc on y ajoute ses parcelles
    const hierarchy = parents.map((p) => {
      return {
        name: p,
        children: data.filter((d) => d.parent_unit_code === p),
      };
    });

    // On ajoute l'essai en racine de la hiérarchie
    data = {
      name: trialCode,
      children: hierarchy,
    };

    const root = d3.hierarchy(data);

    return root;
  }

  function groupBy(array, property) {
    return array.reduce(function (acc, item) {
      var key = item[property];
      acc[key] = acc[key] || [];
      acc[key].push(item);
      return acc;
    }, {});
  }

  function prepareValues(data) {
    const parseDate = d3.utcParse("%Y-%m-%d %H:%M:%S%Z");
    const allDates = [];
    const allValues = [];

    data = data.map((d) => {
      const parsedDate = parseDate(d.date); // Parsing des dates
      if (d.date !== null) {
        allDates.push(parsedDate); // Récupération de toutes les dates
      }
      if (d.value !== null) allValues.push(d.value); // Récupération de toutes les valeurs
      return { ...d, date: parsedDate };
    });

    if (allDates.length > 0) {
      dateMin = d3.min(allDates); // Récupération de la date la plus ancienne
      dateMax = d3.max(allDates); // Récupération de la date la plus récente
    }

    if (allValues.length > 0) {
      valueMin = Math.ceil(d3.min(allValues)); // Récupération de la date la plus ancienne
      valueMax = Math.ceil(d3.max(allValues)); // Récupération de la date la plus récente
    }

    selected_date = dateMin;

    const groupedValues = groupBy(data, "exp_unit_id"); // Regroupement des valeurs par expériences

    return groupedValues;
  }

  function getPath(element) {
    path = element.ancestors().reverse();
    let r = "";
    path.map((p) => (r += p.data.name + " / "));
    console.log("path -> ", path);
    console.log("path name -> ", r);
  }

  function drawChildren(elements) {
    svgAnimation.selectAll("rect").remove(); // On efface les anciens éléments
    svgAnimation.selectAll("text").remove(); // On efface les anciens labels

    const nb = elements.length; // Le nombre d'éléments à insérer
    const maxRectInLine = Math.ceil(Math.sqrt(nb)); // Le nombre maximum d'éléments par ligne

    const rectWidth = WIDTH / maxRectInLine;
    const rectHeight = HEIGHT / maxRectInLine;
    const rectPadding = 2;

    // Affichage des éléments sous forme de rectangles
    var square = svgAnimation.selectAll("rect").data(elements);

    var squareColor = d3
      .scaleLinear()
      .domain([valueMin, valueMax])
      .range([MIN_COLOR, MAX_COLOR]);

    square
      .enter()
      .append("rect")
      .attr("x", (d, i) => {
        return (i % maxRectInLine) * rectWidth;
      })
      .attr("y", (d, i) => {
        return Math.trunc(i / maxRectInLine) * rectHeight;
      })
      .attr("width", rectWidth - rectPadding)
      .attr("height", rectHeight - rectPadding)
      .attr("id", (d, i) => "sqr_" + i)
      .attr("fill", (d) => {
        if (d.depth > 1) {
          const exp = current_values[d.data.exp_unit_id];
          const value = exp ? exp.value : null;
          return value === null ? NULL_COLOR : squareColor(Number(value));
        } else return DEFAULT_COLOR;
      })
      .on("click", (d, i) => {
        if (d.hasOwnProperty("children")) {
          current_element = d; // On change l'élément courant
          loadValues(current_element.data.name, () => {
            updateValues(selected_date);
            getValuesRange(current_element.data.name); // On fixe les bornes des valeurs pour le scaling des couleurs
            AnimationZoom(i, current_element.children); //ajout Animation zoom
            drawSlider(dateMin, dateMax);
            getPath(d);
          });
        } else console.log("L'élément sélectionné n'a pas d'enfants !");
      });

    var labels = svgAnimation.selectAll("text").data(elements);

    labels
      .enter()
      .append("text")
      .text((d) => {
        if (d.depth > 1) {
          const exp = current_values[d.data.exp_unit_id];
          const value = exp ? exp.value : null;
          return value === null
            ? "Aucune valeur"
            : value + " " + d.data.exp_unit_id + " " + d.data.name;
        } else return d.data.name;
      })
      .attr("x", (d, i) => {
        return (i % maxRectInLine) * rectWidth + rectWidth / 2;
      })
      .attr("y", (d, i) => {
        return Math.trunc(i / maxRectInLine) * rectHeight + rectHeight / 2;
      })
      .attr("font-size", "11px")
      .attr("fill", "red")
      .attr("text-anchor", "middle");
  }

  /**
   * Détermiantion des valeurs minimale et maximale pour les enfant de l'élément sélectionné.
   */
  function getValuesRange(parentName) {
    const valuesOfCurrentElements = VALUES.filter(
      (v) => v.parent_unit_code === parentName
    ).map((v) => Number(v.value));
    valueMin = Math.floor(d3.min(valuesOfCurrentElements));
    valueMax = Math.ceil(d3.max(valuesOfCurrentElements));
  }

  function drawSlider(dMin, dMax) {
    // On efface le slider
    $("#slider").html("");

    var margin = { top: 0, right: 50, bottom: 0, left: 50 };
    var width = 500;
    var height = 150;

    var formatDateIntoMY = d3.timeFormat("%m/%Y");
    var formatDate = d3.timeFormat("%b %Y");
    var formatDateComplet = d3.timeFormat("%d-%m-%Y");

    var svgSlider = d3
      .select("#slider")
      .append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height);

    if (dMin === dMax) {
      svgSlider
        .append("text")
        .style("color", "black")
        .attr("text-anchor", "middle")
        .text("Date : " + formatDateComplet(dMin))
        .attr("x", 70)
        .attr("y", height / 2);
    } else {
      var x = d3.scaleTime().domain([dMin, dMax]).range([0, width]).clamp(true);

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
        // .data(x.ticks(10))
        .enter()
        .append("text")
        .attr("x", x)
        // .attr("y", 10)
        .attr("text-anchor", "middle")
        .text(function (d) {
          return formatDateIntoMY(d);
        });

      var handle = slider
        .insert("circle", ".track-overlay")
        .attr("class", "handle")
        .attr("r", 9);

      var label = slider
        .append("text")
        .attr("class", "label")
        .attr("text-anchor", "middle")
        .text(formatDate(dMin))
        .attr("transform", "translate(0," + -25 + ")");

      function update(h) {
        // update position and text of label according to slider scale
        handle.attr("cx", x(h));
        label.attr("x", x(h)).text(formatDate(h));
        selected_date = h;
        updateValues(selected_date);
        drawChildren(current_element.children);
      }
    }
  }

  /**
   * Mise à jour de la variable globale current_values avec la bonne valeur associé à la date sélectionnée
   */
  function updateValues(date) {
    const parseDate = d3.timeFormat("%d-%m-%y");
    // Parcours des valeurs
    for (exp_unit in obs_values) {
      const values = obs_values[exp_unit]; // Tableau contenant les valeurs et la dates associée pour l'unitée expérimentale courante

      const exactDates = values.filter(
        (v) => parseDate(v.date) === parseDate(date)
      );

      if (exactDates.length > 0) current_values[exp_unit] = exactDates[0];
      else {
        const bestLowerDateValue = values
          .filter((v) => v.date < date)
          .slice(-1)[0];
        const bestUpperDateValue = values.filter((v) => v.date > date)[0];
        let bestValue;
        if (bestUpperDateValue === undefined) bestValue = bestLowerDateValue;
        else if (bestLowerDateValue === undefined)
          bestValue = bestUpperDateValue;
        else {
          const lowerNumberOfDays = Math.abs(date - bestLowerDateValue.date);
          const upperNumberOfDays = Math.abs(date - bestUpperDateValue.date);
          bestValue =
            Math.min(lowerNumberOfDays, upperNumberOfDays) === lowerNumberOfDays
              ? bestLowerDateValue
              : bestUpperDateValue;
        }

        current_values[exp_unit] = bestValue;
      }
    }
  }

  //animation zoom
  function AnimationZoom(id, data) {
    var selectSqr = d3.select("#sqr_" + id);

    // var squareColor = d3
    //   .scaleLinear()
    //   .domain([valueMin, valueMax])
    //   .range([MIN_COLOR, MAX_COLOR]);

    // selectSqr.attr("fill", ZOOM_COLOR);

    selectSqr.raise(); //On met le carré au premier plan
    selectSqr
      .transition()
      .duration(1000)
      .attr("x", 0)
      .attr("y", 0)
      .attr("width", WIDTH)
      .attr("height", HEIGHT)
      .attr("fill", BACKGROUND_COLOR)

      //ajout de la couleur suivante
      // .attr("fill", () => {
      //       squareColor(Number(current_values[data.data.exp_unit_id].value))
      // })
      .on("end", () => {
        //Attend la fin de l'animation
        drawChildren(data); // On affiche les enfants de l'élément courant
      });
  }
})(d3);
