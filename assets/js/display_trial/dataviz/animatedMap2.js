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
  let WIDTH = document.getElementById(div_id).clientWidth / 1.5;
  let HEIGHT = document.getElementById(div_id).clientWidth / 1.5;
  let svgAnimation = null; // SVG contenant l'animation
  const MIN_COLOR = "#7DCEA0";
  const MAX_COLOR = "#196F3D";
  const DEFAULT_COLOR = "rgba(23,32,42,1)";
  const BACKGROUND_COLOR = "#784212";
  const LABEL_COLOR = "white";
  const NULL_COLOR = "#ACBD32"; // yellow
  let path = [];
  const FACTORS_NAME = [
    "management",
    "fertilization",
    "variety",
    "system",
    "crop",
  ];
  let selectedManagement = [];
  let selectedFertilization = [];
  let selectedVariety = [];
  let selectedSystem = [];
  let selectedCrop = [];
  let timer = null;

  // On redessine la dataviz quand on redimensionne la fenêtre
  // window.addEventListener("resize", redraw); //listener pour redessiner lors du resize

  var div = d3
    .select("body")
    .append("div")
    .attr("class", "tooltip")
    .style("width", "200px")
    // .style("opacity", 0)
    .style("position", "absolute")
    .style("text-align", "center")
    .style("padding", "10px")
    .style("border", "2px solid black")
    .style("border-radius", "5px")
    .style("background", "white")
    .style("display", "none");

  FACTORS_NAME.forEach((fName) => {
    const select_id = `#${fName}_selectPicker`;

    $(select_id).on("changed.bs.select", function (
      e,
      clickedIndex,
      isSelected,
      previousValue
    ) {
      var selected = $(this).find("option").eq(clickedIndex);

      // Sélection d'un unique élément
      if (selected.length > 0) {
        var selecText = $(this).find("option").eq(clickedIndex).text();

        switch (fName) {
          case "management":
            utils.arrayToggleValue(selectedManagement, selecText);
            break;
          case "fertilization":
            utils.arrayToggleValue(selectedFertilization, selecText);
            break;
          case "variety":
            utils.arrayToggleValue(selectedVariety, selecText);
            break;
          case "system":
            utils.arrayToggleValue(selectedSystem, selecText);
            break;
          case "crop":
            utils.arrayToggleValue(selectedCrop, selecText);
            break;

          default:
            break;
        }
      }
      // Sélection de tous les éléments grâce au bouton dédié
      else {
        const val = $(select_id).val() || [];

        switch (fName) {
          case "management":
            selectedManagement = val;
            break;
          case "fertilization":
            selectedFertilization = val;
            break;
          case "variety":
            selectedVariety = val;
            break;
          case "system":
            selectedSystem = val;
            break;
          case "crop":
            selectedCrop = val;
            break;

          default:
            break;
        }
      }

      onChange();
    });
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
    // onChange(/*() => drawSlider(dateMin, dateMax)*/);
    /*
    clearSlider();
    // drawSVG();
    
    getPath(current_element);
    */
    div.style("display", "none");
    loadValues(current_element.data.name, () => {
      updateValues(selected_date);
      getValuesRange(current_element.data.name); // On fixe les bornes des valeurs pour le scaling des couleurs
      drawChildren(current_element.children);
      drawSlider(dateMin, dateMax);
      getPath(current_element);
    });
  });

  function onChange(optionalCallback = () => {}) {
    if (
      selectedManagement.length === 0 &&
      selectedFertilization.length === 0 &&
      selectedVariety.length === 0 &&
      selectedSystem.length === 0 &&
      selectedCrop.length === 0
    ) {
      $("#expUnitGraph").html(
        "<br><p> Veuillez sélectionner au moins un facteur... </p>"
      );
    } else {
      clearSlider();
      drawSVG();
      loadHierarchy(() => {
        drawChildren(current_element.children, true);
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

    // const margin = { top: 0, right: 0, bottom: 0, left: 0 };

    //Création du svg de la visualisation
    svgAnimation = globalDiv
      .append("svg")
      // .attr("width", WIDTH + margin.left + margin.right + 5)
      // .attr("height", HEIGHT + margin.bottom + margin.top + 50)
      .attr("width", WIDTH)
      .attr("height", HEIGHT)
      // .attr("width", globalDiv.style("width"))
      // .attr("height", globalDiv.style("width"))
      .style("background-color", BACKGROUND_COLOR);
    // .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    svgAnimation.on("mouseout", () => div.style("display", "none"));
  }

  function loadHierarchy(onSuccessCallback) {
    const all_factors_level_selected = selectedManagement
      .concat(selectedFertilization)
      .concat(selectedVariety)
      .concat(selectedSystem)
      .concat(selectedCrop);

    $.ajax({
      url: SiteURL + "/Trials/ajaxLoadDataForAnimatedMap/",
      data: {
        trialCode: JSON.stringify(trialCode),
        factors: JSON.stringify(all_factors_level_selected),
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
        current_values = JSON.parse(JSON.stringify(obs_values));
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

  function prepareHierarchy2(data) {
    // Récupération des premiers enfants (les blocs)
    const reducer = (accumulator, currentValue) =>
      accumulator.add(currentValue.parent_unit_code);
    const parents = Array.from(data.reduce(reducer, new Set()));

    // Regroupement des données par factor et par factor_level
    const groups = groupBy(data, "exp_unit_id"); // Grouper par unité expérimentale
    const children = []; // Va contenir les enfants

    for (const exp_id in groups) {
      const exp = groups[exp_id];

      const res = exp.reduce((acc, curr) => {
        const factor = curr.factor;
        const factor_level = curr.factor_level;
        const factor_desc = curr.factor_level_description;
        const exp_unit_id = curr.exp_unit_id;
        const level_label = curr.level_label;
        const name = curr.name;
        const parent_level_label = curr.parent_level_label;
        const parent_unit_code = curr.parent_unit_code;

        if (!acc.hasOwnProperty("exp_unit_id")) {
          acc["exp_unit_id"] = exp_unit_id;
          acc["level_label"] = level_label;
          acc["name"] = name;
          acc["parent_level_label"] = parent_level_label;
          acc["parent_unit_code"] = parent_unit_code;
          acc["factors"] = {};
        }

        if (acc.factors.hasOwnProperty(factor)) {
          acc.factors[factor].level.push(factor_level);
          acc.factors[factor].description.push(factor_desc);
        } else {
          acc.factors[factor] = {
            level: [factor_level],
            description: [factor_desc],
          };
        }

        return acc;
      }, {});

      children.push(res);
    }

    // A chaque bloc on y ajoute ses parcelles
    const hierarchy = parents.map((p) => {
      return {
        name: p,
        children: children.filter((d) => d.parent_unit_code === p),
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
    const reducer1 = (accumulator, currentValue) => {
      if (
        currentValue.parent_level_label === "Bloc" ||
        currentValue.parent_level_label === "bloc"
      ) {
        return accumulator.add(currentValue.parent_unit_code);
      } else return accumulator;
    };
    const blocs = Array.from(data.reduce(reducer1, new Set()));

    // Récupération des parcelles
    // Pour prendre en compte les sous-parcelles qui ont un bloc comme parent, il faut ajouter les sous-parcelles ici !
    const reducer2 = (accumulator, currentValue) => {
      if (
        (currentValue.level_label === "parcelle" ||
          currentValue.level_label === "plot") &&
        (currentValue.parent_level_label === "Bloc" ||
          currentValue.parent_level_label === "bloc")
      ) {
        accumulator.push(currentValue);
      }
      return accumulator;
    };
    const parcelles = groupBy(
      Array.from(data.reduce(reducer2, [])),
      "exp_unit_id"
    );

    // Récupération des sous_parcelles
    const reducer3 = (accumulator, currentValue) => {
      if (
        currentValue.level_label === "sous_parcelle" &&
        (currentValue.parent_level_label === "parcelle" ||
          currentValue.parent_level_label === "plot")
      ) {
        accumulator.push(currentValue);
      }
      return accumulator;
    };
    const sous_parcelles = groupBy(
      Array.from(data.reduce(reducer3, [])),
      "exp_unit_id"
    );

    let sous_parcelles_grouped = []; // Va contenir les sous_parcelles
    let parcelles_grouped = []; // Va contenir les parcelles

    // Regroupement des sous_parcelles par factor et par factor_level
    if (sous_parcelles.length !== 0) {
      sous_parcelles_grouped = groupFactors(sous_parcelles);
    }

    // Regroupement des parcelles par factor et par factor_level
    if (parcelles.length !== 0) {
      parcelles_grouped = groupFactors(parcelles);
    }

    // Traitement des sous_parcelles --> Si il y a des horizons à ajouter, il faut ajouter ici !
    sous_parcelles_grouped = sous_parcelles_grouped.map((p) => {
      return {
        ...p,
        children: [],
      };
    });

    // A chaque parcelle on y ajoute ses sous-parcelles
    const hierarchy_parcelles = parcelles_grouped.map((p) => {
      return {
        ...p,
        children: sous_parcelles_grouped.filter(
          (d) => p.name.search(d.parent_unit_code) !== -1
        ),
      };
    });

    // A chaque bloc on y ajoute ses parcelles
    const hierarchy = blocs.map((p) => {
      return {
        name: p,
        children: hierarchy_parcelles.filter((d) => d.parent_unit_code === p),
      };
    });

    // On ajoute l'essai en racine de la hiérarchie
    data = {
      name: trialCode,
      children: hierarchy,
    };

    const root = d3.hierarchy(data);

    console.log("root", root);

    return root;
  }

  function groupFactors(tab) {
    let result = [];
    for (const exp_id in tab) {
      const exp = tab[exp_id];

      const res = exp.reduce((acc, curr) => {
        const factor = curr.factor;
        const factor_level = curr.factor_level;
        const factor_desc = curr.factor_level_description;
        const exp_unit_id = curr.exp_unit_id;
        const level_label = curr.level_label;
        const name = curr.name;
        const parent_level_label = curr.parent_level_label;
        const parent_unit_code = curr.parent_unit_code;

        if (!acc.hasOwnProperty("exp_unit_id")) {
          acc["exp_unit_id"] = exp_unit_id;
          acc["level_label"] = level_label;
          acc["name"] = name;
          acc["parent_level_label"] = parent_level_label;
          acc["parent_unit_code"] = parent_unit_code;
          acc["factors"] = {};
        }

        if (acc.factors.hasOwnProperty(factor)) {
          acc.factors[factor].level.push(factor_level);
          acc.factors[factor].description.push(factor_desc);
        } else {
          acc.factors[factor] = {
            level: [factor_level],
            description: [factor_desc],
          };
        }

        return acc;
      }, {});

      result.push(res);
    }

    return result;
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
    const breadcrumb_id = "breadcrumb";

    d3.select("#" + breadcrumb_id)
      .selectAll("li")
      .remove();

    path = element.ancestors().reverse();
    const breadcrumb = d3
      .select("#" + breadcrumb_id)
      .selectAll("li")
      .data(path);

    breadcrumb
      .enter()
      .append("li")
      .attr("class", (d, i) =>
        i === path.length - 1 ? "breadcrumb-item active" : "breadcrumb-item"
      )
      .html((d) => "<a  class='white-text'>" + d.data.name + "</a>")
      .on("click", (d) => {
        if (d === current_element) return;
        current_element = d;
        clearSlider();
        drawSlider(dateMin, dateMax);
        drawChildren(d.children, true);
        getPath(current_element);
        div.style("display", "none");
      });
  }

  /**
   * Retourne true si un élément match avec les facteurs sélectionnés.
   * Prend l'objet factors de l'élément à tester en paramètre
   * @param {Objet} factors
   */
  function factorFilter(factors) {
    let match = true;

    if (selectedManagement.length !== 0) {
      if (
        !factors.management ||
        !contains(selectedManagement, factors.management.level)
      )
        match = false;
    }

    if (selectedFertilization.length !== 0) {
      if (
        !factors.fertilization ||
        !contains(selectedFertilization, factors.fertilization.level)
      )
        match = false;
    }

    if (selectedVariety.length !== 0) {
      if (!factors.variety || !contains(selectedVariety, factors.variety.level))
        match = false;
    }

    if (selectedSystem.length !== 0) {
      if (!factors.system || !contains(selectedSystem, factors.system.level))
        match = false;
    }

    if (selectedCrop.length !== 0) {
      if (!factors.crop || !contains(selectedCrop, factors.crop.level))
        match = false;
    }

    return match;
  }

  /**
   * Filtre les éléments en fonction des facteurs sélectionnées (croisement des facteurs)
   * Retourne uniquement les éléments qui match avec les facteurs sélectionnés
   * @param {Array} elements
   */
  function filterElements(elements) {
    // Filtrage des éléments qui ont des valeurs
    if (elements[0].data.hasOwnProperty("factors")) {
      const res = elements.filter((d) => factorFilter(d.data.factors));
      return res;
    } else {
      // Filtrage des éléments qui n'ont pas de valeur
      const res = elements.filter((d) => {
        let match = [];
        d.data.children.forEach((child) => {
          const factors = child.factors;
          const matchChild = factorFilter(factors);
          match.push(matchChild);
        });
        return match.includes(true);
      });
      return res;
    }
  }

  /**
   * Retourne Vrai si tab2 contient au moins un élément de tab1
   * @param {Array} tab1
   * @param {Array} tab2
   */
  function contains(tab1, tab2) {
    // savoir si tab2 contient au moins un élément de tab1
    return tab2.some((e) => tab1.includes(e));
  }

  function drawChildren(objets, animation = false) {
    svgAnimation.selectAll("rect").remove(); // On efface les anciens éléments
    svgAnimation.selectAll("text").remove(); // On efface les anciens labels
    svgAnimation.selectAll(".tspan").remove();

    const elements = filterElements(objets); // On sélectionne uniquement les éléments qui correspondent aux factor_lvl sélectionnés

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
        return animation ? 0 : (i % maxRectInLine) * rectWidth;
      })
      .attr("y", (d, i) => {
        return Math.trunc(i / maxRectInLine) * rectHeight;
      })
      .attr("width", rectWidth - rectPadding)
      .attr("height", rectHeight - rectPadding)
      .attr("stroke", (d) => (d.data.children.length > 0 ? "white" : ""))
      .attr("rx", 15)
      .attr("ry", 15)
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
      })
      .on("mouseover", (d, i) => {
        if (d.depth > 1) {
          //coordornné x,y en fonction de la svg
          var x = (i % maxRectInLine) * rectWidth + rectWidth / 2 - rectPadding;
          var y = Math.trunc(i / maxRectInLine) * rectHeight + rectHeight;

          var div_main = document.getElementById("expUnitGraph");
          //conversion des coordornée x,y
          x =
            x +
            div_main.offsetLeft -
            Math.round(div.style("width").slice(0, -2)) / 2;
          y = y + div_main.offsetTop + 5;
          // Math.round(div.style("height").slice(0, -2)) / 2;

          div
            .transition()
            .duration(200)
            .style("opacity", 0.9)
            .on("start", () => {
              div
                .html(() => {
                  return getDescriptionText(d);
                })
                .style("display", "block")
                .style("left", x + "px")
                .style("top", y + "px");
            });
        }
      })
      .on("mouseout", (d, i) => {
        if (d.depth > 1) {
          div.style("display", "none");
        }
      });

    if (animation) {
      svgAnimation
        .selectAll("rect")
        .transition()
        .duration(500)
        .attr("x", (d, i) => {
          return (i % maxRectInLine) * rectWidth;
        });
    }

    drawLabels(
      elements,
      maxRectInLine,
      rectWidth,
      rectHeight,
      animation,
      square
    );
  }

  function drawLabels(
    elements,
    maxRectInLine,
    rectWidth,
    rectHeight,
    animation,
    square
  ) {
    const rectPadding = 2;
    const maxNumberOfTspan = 6;
    const maxTspanSize = (rectHeight / maxNumberOfTspan) * 0.7;
    const minTspanSize = 8;
    let fontsizes = [];
    let numberOfTspanFactor = 0;

    var labels = svgAnimation
      .selectAll("text")
      .data(elements)
      .enter()
      .append("text");

    function getSize(d, i) {
      const maxSize = 20;
      let scale = 0;
      if (d.depth > 1) {
        // var bbox = this.getBBox(),
        // cbbox = this.parentNode.getBBox(),
        let textLength = d3.select(this).text().length;
        scale = ((rectWidth - 4) / textLength) * 1.1;

        if (scale < minTspanSize) scale = minTspanSize;
        if (scale > maxTspanSize)
          scale = maxTspanSize > maxSize ? maxSize : maxTspanSize;
      } else {
        scale = maxSize;
      }
      if (!fontsizes.includes(scale)) fontsizes.push(scale);
    }

    function getNameSize(d, i) {
      const maxSize = 20;
      let scale = 0;
      if (d.depth > 1) {
        // var bbox = this.getBBox(),
        // cbbox = this.parentNode.getBBox(),
        let textLength = d3.select(this).text().length;
        scale = ((rectWidth - 4) / textLength) * 1.1;

        if (scale < minTspanSize) scale = minTspanSize;
        if (scale >= maxTspanSize)
          scale = maxTspanSize > maxSize ? maxSize : maxTspanSize;
      } else {
        scale = maxSize;
      }
      d.scale = scale;
    }

    // NAME
    labels
      .append("tspan")
      .text((d) => (d.depth === 1 ? d.data.name : d.data.exp_unit_id))
      .attr("x", (d, i) =>
        animation ? 0 : (i % maxRectInLine) * rectWidth + rectWidth / 2
      )
      .attr("y", (d, i) => {
        if (d.depth === 1)
          return Math.trunc(i / maxRectInLine) * rectHeight + rectHeight / 2;
        else
          return Math.trunc(i / maxRectInLine) * rectHeight + rectHeight * 0.1;
        // return (
        //   Math.trunc(i / maxRectInLine) * rectHeight +
        //   rectHeight / (d.depth === 1 ? 2 : 7)
        // );
      })
      .attr("id", (d, i) => "label_" + i)
      .attr("width", rectWidth)
      // .attr("font-size", "1rem")
      .attr("fill", LABEL_COLOR)
      .attr("text-anchor", "middle")
      .style("font-weight", "bold")
      .each(getNameSize)
      .style("font-size", function (d) {
        return d.scale + "px";
      });

    // Fertilization
    if (selectedFertilization.length > 0) numberOfTspanFactor++;
    for (let j = 0; j < selectedFertilization.length; j++) {
      labels
        .append("tspan")
        .attr("font-family", "FontAwesome")
        .text((d) => {
          if (d.depth > 1) {
            return "\uf067 : " + d.data.factors.fertilization.level;
          }
        })
        .attr("x", (d, i) =>
          animation ? 0 : (i % maxRectInLine) * rectWidth + 5
        )
        .attr("y", (d, i) => {
          // numberOfTspanFactor++;
          return (
            Math.trunc(i / maxRectInLine) * rectHeight +
            rectHeight / 6 +
            maxTspanSize * numberOfTspanFactor
          );
        })
        .attr("fill", LABEL_COLOR)
        .attr("text-anchor", "start")
        .attr("class", "tspan factor")
        .style("font-weight", "normal")
        .each(getSize)
        .style("font-size", function (d) {
          return d.scale + "px";
          // return "10px";
        });
    }

    // management
    if (selectedManagement.length > 0) numberOfTspanFactor++;
    for (let j = 0; j < selectedManagement.length; j++) {
      labels
        .append("tspan")
        .attr("font-family", "FontAwesome")
        .text((d) => {
          if (d.depth > 1) {
            return "\uf01c : " + d.data.factors.management.level;
          }
        })
        .attr("x", (d, i) =>
          animation ? 0 : (i % maxRectInLine) * rectWidth + 5
        )
        .attr("y", (d, i) => {
          // numberOfTspanFactor++;
          return (
            Math.trunc(i / maxRectInLine) * rectHeight +
            rectHeight / 6 +
            maxTspanSize * numberOfTspanFactor
          );
        })
        // .attr("width", rectWidth)
        .attr("fill", LABEL_COLOR)
        .attr("text-anchor", "start")
        .attr("class", "tspan factor")
        .style("font-weight", "normal")
        .each(getSize)
        .style("font-size", function (d) {
          return d.scale + "px";
          // return "10px";
        });
    }

    // System
    if (selectedSystem.length > 0) numberOfTspanFactor++;
    for (let j = 0; j < selectedSystem.length; j++) {
      labels
        .append("tspan")
        .attr("font-family", "FontAwesome")
        .text((d) => {
          if (d.depth > 1) {
            return "\uf021 : " + d.data.factors.system.level;
          }
        })
        .attr("x", (d, i) =>
          animation ? 0 : (i % maxRectInLine) * rectWidth + 5
        )
        .attr("y", (d, i) => {
          // numberOfTspanFactor++;
          return (
            Math.trunc(i / maxRectInLine) * rectHeight +
            rectHeight / 6 +
            maxTspanSize * numberOfTspanFactor
          );
        })
        .attr("fill", LABEL_COLOR)
        .attr("text-anchor", "start")
        .attr("class", "tspan factor")
        .style("font-weight", "normal")
        .each(getSize)
        .style("font-size", function (d) {
          return d.scale + "px";
          // return "10px";
        });
    }

    // Variety
    if (selectedVariety.length > 0) numberOfTspanFactor++;
    for (let j = 0; j < selectedVariety.length; j++) {
      labels
        .append("tspan")
        .attr("font-family", "FontAwesome")
        .text((d) => {
          if (d.depth > 1) {
            return "\uf06c : " + d.data.factors.variety.level;
          }
        })
        .attr("x", (d, i) =>
          animation ? 0 : (i % maxRectInLine) * rectWidth + 5
        )
        .attr("y", (d, i) => {
          // numberOfTspanFactor++;
          return (
            Math.trunc(i / maxRectInLine) * rectHeight +
            rectHeight / 6 +
            maxTspanSize * numberOfTspanFactor
          );
        })
        .attr("fill", LABEL_COLOR)
        .attr("text-anchor", "start")
        .attr("class", "tspan factor")
        .style("font-weight", "normal")
        .each(getSize)
        .style("font-size", function (d) {
          return d.scale + "px";
          // return "10px";
        });
    }

    // Crop
    if (selectedCrop.length > 0) numberOfTspanFactor++;
    for (let j = 0; j < selectedCrop.length; j++) {
      labels
        .append("tspan")
        .attr("font-family", "FontAwesome")
        .text((d) => {
          if (d.depth > 1) {
            return "\uf0c4 : " + d.data.factors.crop.level;
          }
        })
        .attr("x", (d, i) =>
          animation ? 0 : (i % maxRectInLine) * rectWidth + 5
        )
        .attr("y", (d, i) => {
          // numberOfTspanFactor++;
          return (
            Math.trunc(i / maxRectInLine) * rectHeight +
            rectHeight / 6 +
            maxTspanSize * numberOfTspanFactor
          );
        })
        .attr("fill", LABEL_COLOR)
        .attr("text-anchor", "start")
        .attr("class", "tspan factor")
        .style("font-weight", "normal")
        .each(getSize)
        .style("font-size", function (d) {
          return d.scale + "px";
          // return "10px";
        });
    }

    // Mise à jour de la taille de la police
    labels.selectAll(".factor").style("font-size", function (d) {
      return d3.min(fontsizes);
    });

    // VALUES
    labels
      .append("tspan")
      .text((d) => {
        if (d.depth > 1) {
          const exp = current_values[d.data.exp_unit_id];
          const value = exp ? Number(exp.value).toFixed(2) : null;
          const unite = exp ? exp.unite : null;
          return value === null ? "Aucune valeur" : value + " " + unite;
        }
      })
      .attr("x", (d, i) =>
        animation ? 0 : (i % maxRectInLine) * rectWidth + rectWidth / 2
      )
      .attr(
        "y",
        (d, i) =>
          Math.trunc(i / maxRectInLine) * rectHeight +
          // rectHeight / 4 +
          // maxTspanSize * 2
          rectHeight -
          rectHeight * 0.1
      )
      .attr("font-size", "1rem")
      .attr("fill", (d) =>
        current_values[d.data.exp_unit_id] ? LABEL_COLOR : "red"
      )
      // .attr("width", rectWidth)
      .attr("text-anchor", "middle")
      .attr("class", "tspan")
      .each(getNameSize)
      .style("font-size", function (d) {
        return d.scale * 0.9 + "px";
      });

    // AFFICHAGE DES DESCRIPTIONS
    labels
      .on("mouseover", (d, i) => {
        if (d.depth > 1) {
          //coordornné x,y en fonction de la svg
          var x = (i % maxRectInLine) * rectWidth + rectWidth / 2 - rectPadding;
          var y = Math.trunc(i / maxRectInLine) * rectHeight + rectHeight;

          var div_main = document.getElementById("expUnitGraph");
          //conversion des coordornée x,y
          x =
            x +
            div_main.offsetLeft -
            Math.round(div.style("width").slice(0, -2)) / 2;
          y = y + div_main.offsetTop + 5;
          // Math.round(div.style("height").slice(0, -2)) / 2;

          div
            .transition()
            .duration(200)
            .style("opacity", 0.9)
            .on("start", () => {
              div
                .html(() => {
                  return getDescriptionText(d);
                })
                .style("display", "block")
                .style("left", x + "px")
                .style("top", y + "px");
            });
        }
      })
      .on("mouseout", (d, i) => {
        if (d.depth > 1) {
          div.style("display", "none");
        }
      });

    if (animation) {
      svgAnimation.selectAll("text").each(function (d, i) {
        d3.select(this)
          .selectAll("tspan")
          .transition()
          .duration(500)
          .attr("x", (d, j) => (i % maxRectInLine) * rectWidth + rectWidth / 2);

        d3.select(this)
          .selectAll(".factor")
          .transition()
          .duration(500)
          .attr("x", (d, j) => (i % maxRectInLine) * rectWidth + 5);
      });
    }
  }

  function getDescriptionText(d) {
    let text = "<h3>Description</h3>";
    const factors = d.data.factors;
    if (selectedManagement.length > 0) {
      text += "<b>Management :</b><br/>";
      factors["management"].description.forEach((e) => (text += `- ${e}<br/>`));
      text += "<br/>";
    }
    if (selectedFertilization.length > 0) {
      text += "<b>Fertilization :</b><br/>";
      factors["fertilization"].description.forEach(
        (e) => (text += `- ${e}<br/>`)
      );
      text += "<br/>";
    }
    if (selectedVariety.length > 0) {
      const titre = "<b>Variety :</b><br/>";
      let list = "";
      factors["variety"].description.forEach(
        (e) => e && (list += `- ${e}<br/>`)
      );
      if (list !== "") {
        text += `${titre}${list}<br/>`;
      } else {
        factors["variety"].level.forEach((e) => (list += `- ${e}<br/>`));
        text += `${titre}${list}<br/>`;
      }
    }
    if (selectedSystem.length > 0) {
      text += "<b>System :</b><br/>";
      factors["system"].description.forEach((e) => (text += `- ${e}<br/>`));
      text += "<br/>";
    }
    if (selectedCrop.length > 0) {
      text += "<b>Crop :</b><br/>";
      factors["crop"].description.forEach((e) => (text += `- ${e}<br/>`));
      text += "<br/>";
    }

    return text;
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

  function clearSlider() {
    $("#slider").html("");
    d3.select("#sliderButton")
      .style("display", "none")
      .select("i")
      .attr("class", "fa fa-play");
    clearInterval(timer);
  }

  function drawSlider(dMin, dMax) {
    // On efface le slider
    $("#slider").html("");

    var sliderButton = d3.select("#sliderButton");
    sliderButton.select("i").attr("class", "fa fa-play");
    if (timer) clearInterval(timer);

    if (
      !current_values.hasOwnProperty(
        current_element.children[0].data.exp_unit_id
      )
    ) {
      clearInterval(timer);
      sliderButton.select("i").attr("class", "fa fa-play");
      sliderButton.style("display", "none");
      return;
    } else sliderButton.style("display", "block");

    var margin = { top: 0, right: 50, bottom: 0, left: 50 };
    var width = 500;
    var height = 100;

    var formatDateIntoMY = d3.timeFormat("%m/%Y");
    var formatDate = d3.timeFormat("%b %Y");
    var formatDateComplet = d3.timeFormat("%d-%m-%Y");

    var moving = false;
    var currentValue = 0;
    var targetValue = width;

    var svgSlider = d3
      .select("#slider")
      .append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height);

    sliderButton.on("click", () => {
      animationSlider();
    });

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
              moving = false;
              clearInterval(timer);
              sliderButton.select("i").attr("class", "fa fa-play");
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
        .enter()
        .append("text")
        .attr("x", x)
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

      function animationSlider() {
        if (moving) {
          moving = false;
          clearInterval(timer);
          sliderButton.select("i").attr("class", "fa fa-play");
          // sliderButton.attr("class", "btn-success")
        } else {
          moving = true;
          timer = setInterval(step, 100);
          sliderButton.select("i").attr("class", "fa fa-pause");
        }
      }

      function step() {
        update(x.invert(currentValue));
        currentValue = currentValue + targetValue / 151;
        if (currentValue > targetValue) {
          moving = false;
          currentValue = 0;
          clearInterval(timer);
          sliderButton.select("i").attr("class", "fa fa-play");
        }
      }

      function update(h) {
        // update position and text of label according to slider scale
        handle.attr("cx", x(h));
        label.attr("x", x(h)).text(formatDate(h));
        selected_date = h;
        if (!moving) currentValue = x(h);
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
    var selectLabel = d3.select("#label_" + id); // LABELS
    var selectTextZone = selectLabel.select(function () {
      return this.parentNode;
    });

    selectSqr.raise(); //On met le carré au premier plan
    selectTextZone.raise();

    selectSqr
      .transition()
      .duration(500)
      .attr("x", 0)
      .attr("y", 0)
      .attr("width", WIDTH)
      .attr("height", HEIGHT)
      .transition()
      .duration(500)
      .style("opacity", 0)
      .on("start", (d) => {
        svgAnimation.selectAll("rect").attr("opacity", 0);
        selectTextZone.selectAll("tspan").attr("opacity", 0);
        selectSqr.attr("opacity", 100);
        selectLabel.attr("opacity", 100);
      })
      .on("end", () => {
        drawChildren(data, true); // On affiche les enfants de l'élément courant
      });

    selectLabel
      .transition()
      .duration(500)
      .attr("x", WIDTH / 2)
      .attr("y", HEIGHT / 2)
      .attr("font-size", "50px")
      .transition()
      .duration(500)
      .style("opacity", 0)
      .on("start", (d) => {
        svgAnimation.selectAll("text").remove(); // On efface les anciens labels
        svgAnimation.selectAll(".label").remove();
      })
      .on("end", (d) => {
        selectLabel.remove();
      });
  }
})(d3);
