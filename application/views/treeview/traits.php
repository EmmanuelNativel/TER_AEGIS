<div class="fullwidth-block">
    <div class="container">
        <div class="contact-form">
            <h2 class="section-title"><span class="glyphicon glyphicon-file"></span>Dictionnaire des variables</h2>
            <div class="row">
                <div class="col-xs-6 padding-left-2">
                    <div class="boxed-content left">
                        <div class="tree">
                            <ul class="variables">
                                <li>
                                    <span><a onclick="afficherOuCacherClasses();"><i class="glyphicon glyphicon-plus-sign" id="plusOuMoins"></i></a></span><button type="button" class="button bg-info" id="racineTree"><strong>Variables</strong></button>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="boxed-content right">
                            <div class="fullwidth-block">
                                <div id="detailContent" class="container">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--SCRIPT button recherche -->
<script type="text/javascript">
   function afficherDetailTrait(e) {
       var traitNameBDD = $(e).text();
       trait_code= traitNameBDD;

       $.ajax({
           url: '<?php echo site_url('DetailTraits/displayTrait')?>',
           type: "POST",
           data : {"trait_code": trait_code},
           success: function (data) {
               $('#detailContent').html($(data).find('table').clone());
               $('#detailContent table').addClass('table');
               $('#detailContent').removeClass('container');

           },
           error: function () {
               alert("Une erreur s'est produite");
           }
       });
   }

   function afficherDetailMethods(e) {
       var methodNameBdd = $(e).text();
       method_code= methodNameBdd;

       $.ajax({
           url: '<?php echo site_url('DetailTraits/displayMethod')?>',
           type: "POST",
           data : {"method_code": method_code},
           success: function (data) {
               $('#detailContent').html($(data).find('table').clone());
               $('#detailContent table').addClass('table');
               $('#detailContent').removeClass('container');

           },
           error: function () {
               alert("Une erreur s'est produite");
           }
       });
   }

   function afficherDetailScale(e) {
       var scaleNameBdd = $(e).text();
       scale_code= scaleNameBdd;

       $.ajax({
           url: '<?php echo site_url('DetailTraits/displayScale')?>',
           type: "POST",
           data : {"scale_code": scale_code},
           success: function (data) {
               $('#detailContent').html($(data).find('table').clone());
               $('#detailContent table').addClass('table');
               $('#detailContent').removeClass('container');

           },
           error: function () {
               alert("Une erreur s'est produite");
           }
       });
   }

   addClassesToVariable();
    function addClassesToVariable() {
        $.ajax({
            url: '<?php echo site_url('Treeview/classesNamesForTree')?>',
            type: "GET",
            dataType: "json",
            cache: false,
            async: false,
            success: function (plantsClasses) {
                var racineDiv = $("ul.variables");
                console.log(JSON.stringify(plantsClasses));
                var lengthClasses = 0;

                for (i in plantsClasses) {
                    if (plantsClasses.hasOwnProperty(i)) {
                        lengthClasses++;
                    }
                }
                if (lengthClasses > 0) {
                    for (var i = 1; i <= lengthClasses; i++) {

                        var classNameBdd = plantsClasses[i]['class'];
                        var idClasses = classNameBdd;   //recuperer id spécifique de chaque plante
                        var classNamesHtml = '<ul class="groupeClass" id="' + idClasses + '"><li><span><a onclick="afficherOuCacherSubClasses(' + idClasses + ');"><i class="glyphicon glyphicon-plus-sign"></i></a></span><a> <button type="button" class="badge badge-success" >' + classNameBdd + '</button></a>';


                        for (var b in plantsClasses[i]['sub']) {
                            //alert(plantsClasses[i]['sub']);
                            var subclassNameBdd = plantsClasses[i]['sub'][b]['subclass'];
                            var idsub = classNameBdd + subclassNameBdd;
                            var subClassesHtml = '<ul class="subClasses ' + classNameBdd + '" id="' + idsub + '"><li><span><a onclick="afficherOuCacherTraits(' + idsub + ');"><i class="glyphicon glyphicon-plus-sign"></i></a></span><a><button type="button" class="badge badge-inverse">' + subclassNameBdd + '</button></a>';
                            //alert(subClassesHtml);

                            for (var c in plantsClasses[i]['sub'][b]['Trait']) {
                                var traitNameBdd = plantsClasses[i]['sub'][b]['Trait'][c]['trait_code'];
                                var idTrait = idsub + traitNameBdd;
                                //alert(traitNameBdd);

                                var idTrait = idTrait.replace(/-/g,"_");

                               var traitNameBdd1= "PAN_offtype";
                               // alert(traitNameBdd);
                                var traitsHtml = '<ul class="traits ' + idsub + '" id="' + idTrait + '"><li><span><a onclick="afficherOuCacherMethods(' + idTrait + ');"><i class="glyphicon glyphicon-plus-sign"></i></a></span><a onclick="afficherDetailTrait(this);"><button type="button" class="btn btn-primary btn-xs">' + traitNameBdd + '</button></a>';
                                subClassesHtml += traitsHtml;
                                //alert( idTrait);


                                for (var n in plantsClasses[i]['sub'][b]['Trait'][c]['Method']) {
                                    var methodNameBdd = plantsClasses[i]['sub'][b]['Trait'][c]['Method'][n]['method_code'];
                                    var idMethod = idTrait + methodNameBdd;
                                    var methodsHtml = '<ul class="methods ' + idTrait + '" id="' + idMethod + '"><li><span><a onclick="afficherOuCacherScales(' + idMethod + ');"><i class="glyphicon glyphicon-plus-sign"></i></a></span><a onclick="afficherDetailMethods(this);"><button type="button" class="btn btn-info btn-xs">' + methodNameBdd + '</button></a>';
                                    subClassesHtml += methodsHtml;


                                    for (var m in plantsClasses[i]['sub'][b]['Trait'][c]['Method'][n]['Scale']) {
                                        var scaleNameBdd = plantsClasses[i]['sub'][b]['Trait'][c]['Method'][n]['Scale'][m]['scale_code'];
                                        var idScale = idMethod + scaleNameBdd;
                                        var scalesHtml = '<ul class="scales ' + idMethod + '" id="' + idScale + '"><li><span><i class="glyphicon glyphicon-plus-sign ' + methodNameBdd + '"></i></span><a onclick="afficherDetailScale(this);"><button type="button" class="btn btn-warning btn-xs">' + scaleNameBdd + '</button></a></li></ul>';
                                        subClassesHtml += scalesHtml;

                                    }
                                    subClassesHtml += '</li></ul>';

                                }

                                subClassesHtml += '</li></ul>';

                            }

                            subClassesHtml += '</li></ul>';
                            classNamesHtml += subClassesHtml;
                        }


                        classNamesHtml += '</li></ul>';


                        $(classNamesHtml).appendTo("ul.variables");

                    }
                }
            },
            error: function (exception) {
                // alert("j appelle pas");
                alert(JSON.stringify(exception));
            }
        });
    }


    //fonctions gestion des cliques
    function afficherOuCacherClasses() {
        var classAtr = ($("#plusOuMoins").attr('class'));
        //alert(classAtr);

        if (classAtr == "glyphicon glyphicon-plus-sign") {
            $("#plusOuMoins").removeClass('glyphicon glyphicon-plus-sign');
            $("#plusOuMoins").addClass('glyphicon glyphicon-minus-sign');
            $(".groupeClass").show();
        }
        else {
            $("#plusOuMoins").removeClass('glyphicon glyphicon-minus-sign');
            $("#plusOuMoins").addClass('glyphicon glyphicon-plus-sign');
            $(".groupeClass").hide();
        }

    }

    function afficherOuCacherSubClasses(id) {
        var idSubclass = id.getAttribute('id');
        // console.log(idSubclass);
        var classeOfClassAtr = $("#" + idSubclass + " > li > span > a > i").attr('class');

        if (classeOfClassAtr == "glyphicon glyphicon-plus-sign") {
            $("#" + idSubclass + " > li > span > a > i").removeClass('glyphicon glyphicon-plus-sign');
            //alert($("."+classeOfClassAtr).attr("class"));
            $("#" + idSubclass + " > li > span > a > i").addClass('glyphicon glyphicon-minus-sign');
            $("." + idSubclass).show();
        }
        else {
            $("#" + idSubclass + " > li > span > a > i").removeClass('glyphicon glyphicon-minus-sign');
            $("#" + idSubclass + " > li > span > a > i").addClass('glyphicon glyphicon-plus-sign');
            $("." + idSubclass).hide();
        }
    }


    function afficherOuCacherTraits(id) {
        var idTraits = id.getAttribute('id');
        //alert(idTraits);
        var classeOfTraitAtr = $("#" + idTraits + " > li > span > a > i").attr('class');


        if (classeOfTraitAtr == "glyphicon glyphicon-plus-sign") {
            //alert("1");
            $("#" + idTraits + " > li > span > a > i").removeClass('glyphicon glyphicon-plus-sign');
            //alert($("."+classeOfClassAtr).attr("class"));
            $("#" + idTraits + " > li > span > a > i").addClass('glyphicon glyphicon-minus-sign');

            //$(".agronomical_traits").show();
            $("." + idTraits).show();
        }
        else {
            $("#" + idTraits + " > li > span > a > i").removeClass('glyphicon glyphicon-minus-sign');
            $("#" + idTraits + " > li > span > a > i").addClass('glyphicon glyphicon-plus-sign');
            $("." + idTraits).hide();
        }
    }

    function afficherOuCacherMethods(id) {
        console.log('afficherOuCacherMethods');
        var idMethods = id.getAttribute('id');

        var classeOfMethodAtr = $("#" + idMethods + " > li > span > a > i").attr('class');

        if (classeOfMethodAtr == "glyphicon glyphicon-plus-sign") {

            $("#" + idMethods + " > li > span > a > i").removeClass('glyphicon glyphicon-plus-sign');
            $("#" + idMethods + " > li > span > a > i").addClass('glyphicon glyphicon-minus-sign');

            $("." + idMethods).show();
        }
        else {
            $("#" + idMethods + " > li > span > a > i").removeClass('glyphicon glyphicon-minus-sign');
            $("#" + idMethods + " > li > span > a > i").addClass('glyphicon glyphicon-plus-sign');
            $("." + idMethods).hide();
        }
    }

    function afficherOuCacherScales(id) {
        var idScales = id.getAttribute('id');

        var classeOfScaledAtr = $("#" + idScales + " > li > span > a > i").attr('class');

        if (classeOfScaledAtr == "glyphicon glyphicon-plus-sign") {

            $("#" + idScales + " > li > span > a > i").removeClass('glyphicon glyphicon-plus-sign');
            $("#" + idScales + " > li > span > a > i").addClass('glyphicon glyphicon-minus-sign');

            $("." + idScales).show();
        }
        else {
            $("#" + idScales + " > li > span > a > i").removeClass('glyphicon glyphicon-minus-sign');
            $("#" + idScales + " > li > span > a > i").addClass('glyphicon glyphicon-plus-sign');
            $("." + idScales).hide();
        }
    }

    $(".groupeClass").hide();
    $(".subClasses").hide();
    $(".traits").hide();
    $(".methods").hide();
    $(".scales").hide();

</script>






