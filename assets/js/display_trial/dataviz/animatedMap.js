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
  let MIN_COLOR = "#7DCEA0";
  let MAX_COLOR = "#196F3D";
  const DEFAULT_COLOR = "rgba(23,32,42,1)";
  let BACKGROUND_COLOR = "#784212";
  const LABEL_COLOR = "white";
  const NULL_COLOR = "rgb(90,90,90)"; // gris
  let R = 255,
    G = 0,
    B = 0;
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

  var div = d3
    .select("body")
    .append("div")
    .attr("class", "tooltip")
    .style("width", "200px")
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

    div.style("display", "none");
    console.log(
      "current_element.data.exp_unit_id",
      current_element.data.exp_unit_id
    );
    loadValues(
      current_element.data.name,
      current_element.data.exp_unit_id,
      () => {
        updateValues(selected_date);
        getValuesRange(current_element.data.name); // On fixe les bornes des valeurs pour le scaling des couleurs
        drawChildren(current_element.children);
        drawSlider(dateMin, dateMax);
        getPath(current_element);
        createColorSelect();
      }
    );
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
  window.addEventListener("resize", () => {
    WIDTH = document.getElementById(div_id).clientWidth / 1.5;
    HEIGHT = document.getElementById(div_id).clientWidth / 1.5;
    drawSVG();
    drawChildren(current_element.children);
  });

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
        // const root = prepareHierarchy(response.expData); // Préparation de la hiérarchie
        // current_element = root; // On indique l'élément courant
        // onSuccessCallback(); // On va afficher les enfants de l'élément courant
        loadParents(response.expData, onSuccessCallback);
      },
    });
  }

  function loadParents(data, onSuccessCallback) {
    $.ajax({
      url: SiteURL + "/Trials/ajaxLoadParents/",
      data: {
        trialCode: JSON.stringify(trialCode),
      },
      type: "POST",
      dataType: "json",
      success: function (response) {
        // const all_data = data.concat(response.parents);
        const root = prepareHierarchy(data, response.parents); // Préparation de la hiérarchie
        current_element = root; // On indique l'élément courant
        onSuccessCallback(); // On va afficher les enfants de l'élément courant
      },
    });
  }

  function loadValues(parentName, parentId, onSuccessCallback) {
    $.ajax({
      url: SiteURL + "/Trials/ajaxLoadValuesForAnimatedMap/",
      data: {
        trialCode: JSON.stringify(trialCode),
        obs_value: JSON.stringify(selectedVariable),
        parent_name: JSON.stringify(parentName),
        parent_id: JSON.stringify(parentId),
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

  // Préparation de la hiérarchie de données
  function prepareHierarchy(data, parents) {
    let max_lvl = 1; //va contenir la profondeur max des données

    // Transformation des num_level en Number et détermination de la profondeur max de l'arbre
    data.map((e) => {
      e.num_level = Number(e.num_level);
      e.parent_num_level = Number(e.parent_num_level);
      if (e.num_level > max_lvl) max_lvl = e.num_level;
    });
    parents.map((e) => {
      e.num_level = Number(e.num_level);
    });

    let sub_hierarchy = []; // va contenir la hiérarchie sans le premier niveau

    // On parcours les différents niveaux des données en commançant par le plus profond jusqu'au niveau 2 (sans les blocs)
    for (let profondeur = max_lvl; profondeur >= 2; profondeur--) {
      //On récupère les éléments qui ont la profondeur courante
      const reducer = (accumulator, currentValue) => {
        if (currentValue.num_level === profondeur) {
          accumulator.push(currentValue);
        }
        return accumulator;
      };

      // On les regroupe par exp_unit_id
      const elements = groupBy(
        Array.from(data.reduce(reducer, [])),
        "exp_unit_id"
      );

      // On les regroupe par facteurs
      const grouped_elements = groupFactors(elements);

      // On ajoute les en enfants de chaque élément, si il y en a
      sub_hierarchy = grouped_elements.map((p) => {
        return {
          ...p,
          children: sub_hierarchy.filter((e) => {
            return p.exp_unit_id === e.parent_unit_id;
            //return p.name.search(e.parent_unit_code) !== -1 && e.parent_num_level === p.num_level;
          }),
        };
      });
    }

    // On ajoute les éléments de profondeur 1
    const hierarchy = parents.map((p) => {
      return {
        ...p,
        children: sub_hierarchy.filter(
          (e) => e.parent_unit_id === p.exp_unit_id
        ),
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
        const num_level = curr.num_level;
        const name = curr.name;
        const parent_level_label = curr.parent_level_label;
        const parent_unit_code = curr.parent_unit_code;
        const parent_num_level = curr.parent_num_level;
        const parent_unit_id = curr.parent_unit_id;

        if (!acc.hasOwnProperty("exp_unit_id")) {
          acc["exp_unit_id"] = exp_unit_id;
          acc["level_label"] = level_label;
          acc["num_level"] = num_level;
          acc["name"] = name;
          acc["parent_level_label"] = parent_level_label;
          acc["parent_unit_code"] = parent_unit_code;
          acc["parent_num_level"] = parent_num_level;
          acc["parent_unit_id"] = parent_unit_id;
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
        div.style("display", "none");
        current_element = d;
        clearSlider();
        drawSlider(dateMin, dateMax);
        drawChildren(d.children, true);
        getPath(current_element);
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
          loadValues(
            current_element.data.name,
            current_element.data.exp_unit_id,
            () => {
              updateValues(selected_date);
              getValuesRange(current_element.data.name); // On fixe les bornes des valeurs pour le scaling des couleurs
              AnimationZoom(i, current_element.children); //ajout Animation zoom
              drawSlider(dateMin, dateMax);
              getPath(d);
            }
          );
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
          const value = exp ? Number(exp.value).toFixed(3) : null;
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
    text += "<p><b>Name : </b>" + d.data.name + "<br/></p>";
    text += "<p><b>Type : </b>" + d.data.level_label + "</p>";
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
    var formatDate = d3.timeFormat("%d %b %Y");
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
      let newdMax = new Date(
        dMax.getFullYear(),
        dMax.getMonth(),
        dMax.getDate() + 1
      );
      var x = d3
        .scaleTime()
        .domain([dMin, newdMax])
        .nice()
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
          .filter((v) => v.date <= date)
          .slice(-1)[0];

        // current_values[exp_unit] = bestValue;
        current_values[exp_unit] = bestLowerDateValue;
      }
    }
  }

  //animation zoom
  function AnimationZoom(id, data) {
    svgAnimation.selectAll(".tspan").remove();
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

  //Séléction couleurs
  function createColorSelect() {
    d3.select("#sliderColor").remove();

    var margin = { top: 0, right: 50, bottom: 0, left: 50 };
    var width = 200;
    var height = 100;

    var svgSlider = d3
      .select("#menuSetting")
      .append("svg")
      .attr("id", "sliderColor")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height);

    svgSlider
      .append("rect")
      .attr("id", "colorIndicator")
      .attr("x", 40)
      .attr("y", 20)
      .attr("width", 20)
      .attr("height", 20)
      .style("opacity", 0)
      .attr("rx", 15)
      .attr("ry", 15);

    svgSlider
      .append("text")
      .text("Couleurs")
      .attr("x", 25)
      .attr("y", 20)
      .style("font-weight", "bold")
      .style("fill", "grey");

    var x = d3.scaleTime().domain([0, 1275]).range([0, width]).clamp(true);

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
          .on("start drag", function () {
            d3.select("#colorIndicator").style("opacity", 1);
            update(x.invert(d3.event.x));
            getLightnessColor();
          })
          .on("end", function () {
            //fin du drag
            d3.select("#colorIndicator")
              .transition()
              .duration(1000)
              .style("opacity", 0);
            // getLightnessColor()
          })
      );

    slider
      .insert("g", ".track-overlay")
      .attr("class", "ticks")
      .attr("transform", "translate(0," + 18 + ")");

    var handle = slider
      .insert("circle", ".track-overlay")
      .attr("fill", "red")
      .attr("class", "handle")
      .attr("r", 9);

    function update(h) {
      // update position and text of label according to slider scale
      // conversion des valeurs

      var color = setColorSlider(Math.round(x(h) * (1275 / 200)));
      //console.log(color)
      handle.attr("cx", x(h)); //x(h) taille du slider
      drawChildren(current_element.children);
      d3.select("#colorIndicator")
        .attr("x", x(h) + 40)
        .attr("fill", setColorSlider());
    }

    function setColorSlider(x) {
      //f(x) = RGB
      // couleur de départ rouge
      //var R = 255, G=0, B=0;
      //Rouge ==> Mauve
      let seuil = 256;
      if (x < seuil) {
        R = 255;
        G = x % seuil;
        B = 0;
      }
      if (seuil < x && x < 2 * seuil) {
        R = seuil - (x % seuil);
        G = 255;
        B = 0;
      }
      if (2 * seuil < x && x < 3 * seuil) {
        R = 0;
        G = 255;
        B = x % seuil;
      }
      if (3 * seuil < x && x < 4 * seuil) {
        R = 0;
        G = 255 - (x % seuil);
        B = 255;
      }
      if (4 * seuil < x && x < 5 * seuil) {
        R = x % seuil;
        G = 0;
        B = 255;
      }
      //console.log("rgb = ",R,G,B);
      return "rgb(" + R + "," + G + "," + B + ")";
    }

    function getLightnessColor() {
      //établie la teinte de la couleur choisie pour les valeur min et max
      //Modifier la luminosité des couleurs
      //couleur claire = val MIN
      //couleur Sombre =  val MAX

      //Fixer la teinte..., Ne fonction pas avec toute la pallete de couleurs

      // MAX_COLOR = setColorSlider();
      var brigthness = 0.4; //diminue la lumiere
      var red = R * brigthness;
      var green = G * brigthness;
      var blue = B * brigthness;
      var coef = 0.3;

      if (R >= B && R >= G) {
        R = 255;
        G = G * coef;
        B = B * coef;
        if (G > 128 || B > 128) {
          R = R * 0.7;
        }
      }

      if (G >= B && G >= R) {
        G = 255;
        R = R * coef;
        B = B * coef;
        if (R > 128 || B > 128) {
          G = G * 0.7;
        }
      }

      if (B >= R && B >= G) {
        B = 255;
        G = G * coef;
        R = R * coef;
        if (G > 128 || R > 128) {
          B = B * 0.7;
        }
      }

      MAX_COLOR = "rgb(" + red + "," + green + "," + blue + ")";
      MIN_COLOR =
        "rgb(" +
        Math.round(R) +
        "," +
        Math.round(G) +
        "," +
        Math.round(B) +
        ")";

      // Indicateur des couleurs
      d3.select("#colorIndicator").attr("fill", MIN_COLOR);

      //couleur complémentaire pour la couleur du fond
      BACKGROUND_COLOR =
        "rgb(" +
        Math.round(255 - red) +
        "," +
        Math.round(255 - green) +
        "," +
        Math.round(255 - blue) +
        ")";

      svgAnimation.style("background-color", BACKGROUND_COLOR);
      drawChildren(current_element.children);
    }
  }
})(d3);
