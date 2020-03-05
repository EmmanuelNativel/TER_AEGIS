(function(d3) {
  /*============================================================================
                     Code d3js pour le multi graph des unités expérimentales
      =============================================================================*/
  //var selectedGraphHeight = $("#size_selectPicker :selected").val();
  var trialCode = $("#dataviz").attr("trial_code"); // Récupère le code de l'essai courant
  var data = { name: trialCode, children: [] }; //variable locale à ce fichier qui va contenir les données
  let selectedFactors = []; //Liste des facteurs
  let selectedVariable = null; // Valeur à observer

  /**
   *  Handler pour la selection des facteurs
   */
  $("#factor_selectPicker").on("changed.bs.select", function(
    e,
    clickedIndex,
    isSelected,
    previousValue
  ) {
    var selected = $(this)
      .find("option")
      .eq(clickedIndex);
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
  $("#variable_selectPicker").on("changed.bs.select", function(
    e,
    clickedIndex,
    isSelected,
    previousValue
  ) {
    var selectedValue = $(this)
      .find("option")
      .eq(clickedIndex)
      .val();
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
        obs_value: JSON.stringify(selectedVariable)
      },
      type: "POST",
      dataType: "json",
      success: function(response) {
        data.children = response.expData; // data est une variable globale
        prepareData(response.expData, response.expValues);
        //parseData();
        onSuccessCallback();
      }
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
    const dataWithValues = dataExp.map(d => {
      const valuesChildren = values.filter(
        child => child.exp_unit_id === d.exp_unit_id
      );
      const valuesNeeded = valuesChildren.map(v => ({
        date: v.date,
        value: v.value
      }));
      return { ...d, values: valuesNeeded };
    });

    // A chaque bloc on y ajoute ses parcelles
    const hierarchy = parents.map(p => {
      return {
        name: p,
        children: dataWithValues.filter(d => d.parent_unit_code === p)
      };
    });

    // On ajoute l'essai en racine de la hiérarchie
    data = {
      //name: "Matrice_Andrano_0304",
      name: trialCode,
      children: hierarchy
    };
    console.log("data", data);
  }

  /**
   * TODO:
   *
   * Faire le Zoom => Modifier la structure de code ? >>> Plus simple a visualiser
   * Améliorer l'affichage des données
   * Afficher le chemin actuelle dans le pass => creer une fct click pour les parcelles
   *
   */

  function redraw() {
    //on supprime les svg avant de réafficher
    var globalDivEl = document.getElementById("expUnitGraph");
    var globalDiv = d3.select(globalDivEl);
    globalDiv.html("");

    var divBlocs = document.createElement("div");
    divBlocs.id = "BlocAnimation";

    globalDivEl.appendChild(divBlocs);

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

    //On ajoute un svg a la div
    var svg = d3
      .select("#" + divBlocs.id)
      .append("svg")
      .attr("width", width + margin.left + margin.right + 5)
      .attr("height", height + margin.bottom + margin.top + 50)
      .style("border", "1px solid red")
      .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var svg1 = d3
      .select("#" + divBlocs.id)
      .append("svg")
      .attr("x", 100)
      .attr("width", cote_info)
      .attr("height", height + 50)
      .style("border", "1px solid blue");

    //Modification des données pour être plus simple a les traité
    const pack = d =>
      d3
        .pack()
        .size([width, height])
        .padding(2)(
        d3.hierarchy(d)
        //.sum(d => d.value)
        //.sort((a, b) => b.value - a.value)
      );

    const root = pack(data);

    var title_essais = root.data.name;
    var ex_path = root;
    console.log("\n\n ROOT = ", root, "\n\n");

    display(root.children);

    function display(currentSelection) {
      const nb = currentSelection.length;
      const maxRectInLine = Math.ceil(Math.sqrt(nb));
      const rectWidth = width / maxRectInLine;
      const rectHeight = height / maxRectInLine;
      const rectPadding = 2;

      //var qui contient la svg qui gères l'affichage des parcelles
      var square = svg
        .selectAll("rect")
        .data(currentSelection)
        .enter()
        .append("rect")
        //.text(function(d) {return d.data.name})
        .attr("x", (d, i) => {
          return (i % maxRectInLine) * rectWidth + 1;
        })
        .attr("y", (d, i) => {
          return Math.trunc(i / maxRectInLine) * rectHeight + 45;
        })
        .attr("width", rectWidth - rectPadding)
        .attr("height", rectHeight - rectPadding)
        .attr("fill", "green");

      //fct click sur les rectangles
      square.on("click", (d, i) => {
        console.log(`click on `, d.data.name);
        //condition pour éviter de descendre plus bas que la feuille
        if (d.depth != 2) {
          ex_path = d.parent; // -> On récuperer les parents de "d" qu'on stock dans une var global
          //printPass(d)// Ne fonctionne pas dans cette fonction
          resetDisplay();
          display(d.children); //affiche les enfants
        } else {
          //console.log("append text");
          //svg1.selectAll("text").remove()
          Value(d);
        }
      });

      //function hoverClick sur les parcelles
      square.on("mouseover", handleMouseOver);

      //SVG qui permet de faire le retour
      var pass = svg.append("g").attr("class", "ClassforText");

      //ajoute un rectangle
      pass
        .append("rect")
        .attr("width", width + margin.left + margin.right)
        .attr("height", 40)
        .attr("fill", "lightgrey");

      pass
        .append("text")
        .attr("x", 6)
        .attr("y", 6 - margin.top)
        .attr("dy", ".75em")
        //.text("Back <==")
        .text(() => {
          return "<== ";
        });

      //On ajoute une fct Onclick sur le le svg pass
      pass.on("click", () => {
        if (ex_path.depth != 1) {
          resetDisplay();
          pass.select("text").text("<==");
          resetDisplay();
          display(ex_path.children); // retourne au parent
        }
      });

      //reset de l'affichage pour afficher les éléments
      function resetDisplay() {
        svg.selectAll("rect").remove();
        svg1.selectAll("text").remove();
        //pass.select("text").text("data")
      }

      //Affiche les données sur dans le svg info
      function Value(d) {
        if (d.depth == 2) {
          svg1.selectAll("text").remove();
          console.log("data =>", Object.keys(d.data).length);
          var i = 1;
          $.each(d.data, function(key, val) {
            //console.log(key + " : " + val);
            svg1
              .append("text")
              .attr("x", 20)
              .attr("y", i * 30)
              .text(val);
            i++;
          });
        }
      }

      //affiche le nom de la données dans le svg pass
      function printPass(d) {
        pass.select("text").text(d.data.name);
      }

      //function hoverclick
      function handleMouseOver(d, i) {
        //d et i pour des futures modifications
        /**Idée de function :
         * affiche les donneés de la parcelles (+horizons)
         * affiche les logos liées au données
         */
        printPass(d);
      }
    }
  }
})(d3); //end of this file
