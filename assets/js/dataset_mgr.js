$(document).ready(function() {

    var table = $('#preview-table');
    var max_preview_row_nb = 10;
    var label_color = $('label[for="radio1"]').css('color');
    var href = $("#dl_link").prop('href');

    // PREVISUALISATION
    $('#preview-btn').click(function(event) {

        if ($('#userfile')[0].files[0] === undefined || $("#input_file_box").is(':hidden')) {
            return;
        }

        $('#preview-window').fadeOut('fast');

        html_table = '';
        html_header = '';
        i = 0;

        Papa.parse($('#userfile')[0].files[0], {
            worker: true,
            skipEmptyLines: true,
            header: true,
            preview: max_preview_row_nb,
            step: function(results) {

                //HEADER
                if (i === 0) {
                    html_header = html_header + '<tr>';
                    for (var field_index in results.meta.fields) {
                        field = results.meta.fields[field_index];
                        html_header = html_header + '<th>' + field + '</th>';
                    }
                    html_header = html_header + '</tr>';
                }

                //LIGNE
                html_table = html_table + '<tr>';
                for (var data_index in results.data[0]) {
                    data = results.data[0][data_index];
                    html_table = html_table + '<td>' + data + '</td>';
                }
                html_table = html_table + '</tr>';
                i++;

            },
            complete: function() {
                //HTML (VUE DE LA TABLE)
                $('#preview-table').html('<thead>' + html_header + '</thead><tbody>' + html_table + '</tbody>');
                $('#preview-window').fadeIn('slow');
                return;
            }
        });
    });

    //CHANGEMENT DE FORMULAIRE
    $("#select-form").change(function(event) {
        displayContextBlock();
    });

    //AJOUTER UNE CULTURE A LA BASE DE DONNEES
    $('#addCropBtn').click(function() {
        var delay = 500;
        $('#cropAlert').fadeOut('slow');
        if ($('#cropAlert').is(':visible')) {
            setTimeout(add_crop, delay);
        } else {
            add_crop();
        }
    });

    //ADD SAMPLE STAGE
    $('#addStageBtn').click(function() {
        var delay = 500;
        $('#stageAlert').fadeOut('slow');
        if ($('#stageAlert').is(':visible')) {
            setTimeout(add_sample_stage, delay);
        } else {
            add_sample_stage();
        }
    });

    //MAJ DE LA LISTE DES PARENTS POSSIBLES
    $('#levelTaxo').change(function(event) {
        if ($('#levelTaxo').val() != 'order') {
            $('#parentTaxo').prop('disabled', false);
            $('#parentTaxo').selectpicker('refresh');
        }
        possible_parents_for($('#levelTaxo').val());
    });

    //MAJ VISIBILITE
    $('#selectedDataset').change(function() {
        $.ajax({
            url: SiteURL + 'data_import/get_dataset',
            type: 'POST',
            dataType: 'json',
            data: {dataset_id: $('#selectedDataset').val()}
        })
        .done(function(res) {
            if (res.type != 'error') {
                switch (res.visibility) {
                    case 0:
                    $("#badgePrivate").show('fast');
                    $("#badgeGroup").hide('fast');
                    $("#badgePublic").hide('fast');
                    break;

                    case 1:
                    $("#badgePrivate").hide('fast');
                    $("#badgeGroup").show('fast');
                    $("#badgePublic").hide('fast');
                    break;

                    case 2:
                    $("#badgePrivate").hide('fast');
                    $("#badgeGroup").hide('fast');
                    $("#badgePublic").show('fast');
                    break;
                }
            }
            else {
                console.error(res.message);
            }
        });
    });

    function possible_parents_for(taxoLevel) {
        $.ajax({
                url: SiteURL + 'data_import/possible_parents',
                type: 'POST',
                dataType: 'json',
                data: {
                    taxoLevel: $('#levelTaxo').val()
                }
            })
            .done(function(list_parents) {
                options_str = '';
                for (var i = 0; i < list_parents.length; i++) {
                    parent = list_parents[i];
                    options_str = options_str + '<option value=' + parent.taxo_id + '>' + parent.taxo_code + ' ' + parent.taxo_name + '</option>';
                }
                $('#parentTaxo').html(options_str).selectpicker('refresh');

            });
    }

    function displayContextBlock() {
        var selected_form = $("#select-form")[0].value;
        console.log(selected_form);

        $("#dl_link").prop('href', href + '/' + selected_form);

        $('#preview-window').fadeOut('fast');
        $("#input_file_box").fadeOut('slow');
        $('#addCrop').fadeOut('slow');
        $('#addSampleStage').fadeOut('slow');
        $('#infoBlock').fadeOut('slow');
        $('#cropAlert').fadeOut('slow');
        $('#stageAlert').fadeOut('slow');
        $("#addTrial").fadeOut('slow');

        $('#main-form').attr('action', SiteURL + 'data_import/#');

        switch (selected_form) {
            case 'exp_unit':
                $("#input_file_box").fadeIn('slow');
                toggle_public_ui();
                break;

            case 'itk':
                $("#input_file_box").fadeIn('slow');
                toggle_private_ui();
                break;

            case 'accessions':
                $('#addCrop').fadeIn('slow');
                $("#input_file_box").fadeIn('slow');
                toggle_public_ui();
                break;

            case 'accession_unit':
                $("#input_file_box").fadeIn('slow');
                $('#infoBlock').children('.alert').html('<strong>Attention!</strong> Le format des dates est <i>YYYY-MM-DD</i>.');
                $('#infoBlock').fadeIn('slow');
                toggle_public_ui();
                break;

            case 'treatment':
                $("#input_file_box").fadeIn('slow');
                toggle_public_ui();
                break;

            case 'trial':
              $("#addTrial").fadeIn('slow');
              toggle_public_ui();
              $('#main-form').attr('action', SiteURL + 'trials/create');
              break;

            case 'sample':
                $("#input_file_box").fadeIn('slow');
                $('#infoBlock').children('.alert').html('<strong>Attention!</strong> Le format des dates est <i>YYYY-MM-DD</i>.');
                $('#infoBlock').fadeIn('slow');
                $('#addSampleStage').fadeIn('slow');
                toggle_public_ui();
                break;
        }
    }

    function add_sample_stage() {
        $.ajax({
                url: SiteURL + 'data_import/add_sample_stage',
                type: 'POST',
                dataType: 'html',
                data: {
                    stageName: $('#stageName').val(),
                    stageStartingDate: $('#stageStartingDate').val(),
                    stageEndingDate: $('#stageEndingDate').val(),
                    physioStage: $('#physioStage').val(),
                    trialCode: $('#trialCode').val()
                }
            })
            .done(function(result) {
                console.log(result);
                if (result == 'success') {
                    $('#stageName').val('');
                    $('#stageStartingDate').val('');
                    $('#stageEndingDate').val('');
                    $('#physioStage').val('');
                    $('#trialCode').val('');
                    success_alert('Stade de développement', 'stageAlert');
                } else {
                    error_alert(result, 'stageAlert');
                }
            })
            .fail(function(error) {
                console.log(error);
            })
            .always(function() {
                console.log('complete');
            });
    }

    function add_crop() {
        console.log($('#cropCode').val(),
            $('#cropName').val(),
            $('#levelTaxo').val(),
            $('#parentTaxo').val());
        $.ajax({
                url: SiteURL + 'data_import/add_taxon',
                type: 'POST',
                dataType: 'html',
                data: {
                    code: $('#cropCode').val(),
                    name: $('#cropName').val(),
                    level_taxo: $('#levelTaxo').val(),
                    parent: $('#parentTaxo').val()
                }
            })
            .done(function(result) {
                console.log(result);
                if (result == 'success') {
                    $('#cropCode').val('');
                    $('#cropName').val('');
                    $('#levelTaxo').val('');
                    $('#parentTaxo').val('');
                    success_alert('Culture', 'cropAlert');
                } else {
                    error_alert(result, 'cropAlert');
                }
            })
            .fail(function(error) {
                console.log(error);
            })
            .always(function() {
                console.log('complete');
            });
    }

    function success_alert(element, alert_component_id) {
        $('#' + alert_component_id).removeClass('alert-danger');
        $('#' + alert_component_id).addClass('alert-success');
        $('#' + alert_component_id).fadeIn('fast');
        $('#' + alert_component_id + 'Msg').html("<span class=\"glyphicon glyphicon-ok\"></span><strong>" + element + "</strong> ajouté(e) avec succés");
    }

    function error_alert(html, alert_component_id) {
        $('#' + alert_component_id).removeClass('alert-success');
        $('#' + alert_component_id).addClass('alert-danger');
        $('#' + alert_component_id).fadeIn('fast');
        $('#' + alert_component_id + 'Msg').html('<span id="stageAlertMsg"><div class="row"><div class="col-md-1"><span class="glyphicon glyphicon-warning-sign"></span></div><div class="col-md-11">' + html + '</div></div></span>');
    }

    function toggle_public_ui() {
        $('#datasetInfo').fadeOut('slow');
        $('#datasetName').prop('required', false);
        $('#datasetDescription').prop('required', false);
        $('#datasetType').prop('required', false);
    }

    function toggle_private_ui() {
        $('#datasetInfo').fadeIn('slow');
        $('#datasetName').prop('required', true);
        $('#datasetDescription').prop('required', true);
        $('#datasetType').prop('required', true);
    }

    // Start document
    $('#parentTaxo').prop('disabled', true);
    $('#preview-window').hide('fast');
    $("#input_file_box").hide('fast');
    $('#addCrop').hide('fast');
    $('#addSampleStage').hide('fast');
    $('#infoBlock').hide('fast');
    $('#datasetInfo').fadeOut('fast');
    displayContextBlock(); // Affiche les blocks correspondant au formulaire sélectionné.
    $('.input-daterange').datepicker({
        format: "yyyy-mm-dd",
        language: "fr"
    });
});
