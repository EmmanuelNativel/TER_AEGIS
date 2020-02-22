(function (d3) {
    utils = {}
    //==========================================================================
    //Tableau de couleur minutieusement choisi
    // (voir http://tools.medialab.sciences-po.fr/iwanthue/)
    utils.colorArray = ["#586fd8", "#d85b2c", "#62be4a", "#9144a6", "#d89d30",
                        "#cc90ce", "#3abbcc", "#c540a0", "#a37634", "#75681b",
                        "#6196d4", "#36815b", "#a65533", "#5cc07d", "#da4683",
                        "#e18089", "#526b25", "#5cc09f", "#db76d7", "#9e61dc",
                        "#7165ab", "#afb932", "#aa4153", "#878b4f", "#e29766",
                        "#418a37", "#82a13e", "#9d507a", "#d43e47" , "#bab365"]

    //==========================================================================

    /*
     *  Initialisation du language pour les dates de d3js
     */

     var locale = d3.timeFormatDefaultLocale({
      "dateTime": "%A, le %e %B %Y, %X",
      "date": "%d/%m/%Y",
      "time": "%H:%M:%S",
      "periods": ["AM", "PM"],
      "days": ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
      "shortDays": ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
      "months": ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
      "shortMonths": ["janv.", "févr.", "mars", "avr.", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc."]
    });

    var formatMillisecond = locale.format(".%L"),
        formatSecond = locale.format(":%S"),
        formatMinute = locale.format("%I:%M"),
        formatHour = locale.format("%I %p"),
        formatDay = locale.format("%a %d"),
        formatWeek = locale.format("%b %d"),
        formatMonth = locale.format("%B"),
        formatYear = locale.format("%Y");

    utils.multiFormat = function(date) {
        return (d3.timeSecond(date) < date ? formatMillisecond
            : d3.timeMinute(date) < date ? formatSecond
            : d3.timeHour(date) < date ? formatMinute
            : d3.timeDay(date) < date ? formatHour
            : d3.timeMonth(date) < date ? (d3.timeWeek(date) < date ? formatDay : formatWeek)
            : d3.timeYear(date) < date ? formatMonth
            : formatYear)(date);
    }

    //==========================================================================

    //Ajoute une valeur dans un tableau si elle n'existe pas ou la supprime si elle existe déjà.
    utils.arrayToggleValue = function(array, value) {
      var index = array.indexOf(value);
      if (index !== -1) array.splice(index, 1);
      else array.push(value);
    }

    //==========================================================================

    //Recherche dichotomique du point sur la courbe ayant pour coordonnée x
    utils.getPointAtX = function(linePath, x) {
      var point = null,
          beginning = 0,
          end = linePath.getTotalLength(),
          target = null;
      while (true){
        target = Math.floor((beginning + end) / 2);
        var point = linePath.getPointAtLength(target);

        if ((target === end || target === beginning) && point.x !== x) {
            //Si le dernier point trouvé est trop loin (2px) de notre x on retourne null
            if (Math.abs(point.x - x) > 2 /*px*/ ) point = null;
            break;
        }
        if (point.x > x)      end = target;
        else if (point.x < x) beginning = target;
        else break; // point.x == x
      }
      return point;
    }
    //==========================================================================


      //Permet de couper un texte D3 à une certaine taille en pixel
      utils.getWrappedText = function(d3Text, pixelWidth) {
        var text = d3Text.text()
        var currentWidth = 0;
        var currentNbChar = 0;
        while (currentWidth < pixelWidth && currentNbChar < text.length) {
          currentNbChar++;
          currentWidth = d3Text.node().getSubStringLength(0,currentNbChar);
        }

        if (currentWidth > 1.1*pixelWidth) { //Si le dernier trouvé est trop grand on prend un char de moins
          return (text.substring(0, currentNbChar - 1))
        } else {
          return (text.substring(0, currentNbChar))
        }
      }



}(d3)) //end of file
