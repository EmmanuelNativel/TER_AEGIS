var group_access_list = {}; // Contient les group_id des groupes ayant accés au jeu de données en cours de création

//CREATE DATASET
$('#createDatasetBtn').click(function() {
    var delay = 500;
    $('#createDatasetAlert').fadeOut('slow');
    if ($('#createDatasetAlert').is(':visible')) {
        setTimeout(createDataset, delay);
    } else {
        createDataset();
    }
});

function createDataset() {
    $.ajax({
            url: SiteURL + 'DataImport/create_dataset',
            type: 'POST',
            dataType: 'json',
            data: {
                datasetName: $('#datasetName').val(),
                datasetDescription: $('#datasetDescription').val(),
                datasetType: $('#datasetType').val(),
                visibility: $("input[name=visibility]:checked").val(),
                //groups_list: JSON.stringify(group_access_list)
            }
        })
        .done(function(result) {
            dataset_name = $('#datasetName').val();
            if (result.type == 'success') {
                $('#datasetName').val('');
                $('#datasetDescription').val('');
                $('#datasetType').val('');
                $('#radio1').prop( "checked", true );
                $('#selectGroup').val('');
                group_access_list = {};
                refresh_groups_list();
                $('#createDatasetAlert').removeClass('alert-danger')
                    .addClass('alert-success')
                    .html('<span class="glyphicon glyphicon-ok"></span>Jeu de données créé avec succés!')
                    .fadeIn('slow');

                $('#selectedDataset').append($('<option>', {
                    value: result.dataset_id,
                    text: dataset_name
                }));
                $('#selectedDataset').selectpicker('refresh');

            } else {
                $('#createDatasetAlert').removeClass('alert-success')
                    .addClass('alert-danger')
                    .html('<span class="glyphicon glyphicon-warning-sign"></span>' + result.message)
                    .fadeIn('slow');
            }
        })
        .fail(function(error) {
            console.error(error);
        });
}

//DISPLAY GROUPS ACCESS PANEL
$('input[name=visibility]').on('change', function() {
    if (!$('#radio1').is(':checked')) {
        $('#grpAccessPanel').slideDown('slow');
    } else {
        $('#grpAccessPanel').slideUp('slow');
    }
    if ($('#radio3').is(':checked')) { // Si la visibilité est Publique on passe tout les accés en mode écriture
        for (var index in group_access_list) {
            var group = group_access_list[index];
            group.access = 1;
        }
    } else {
        for (var index in group_access_list) {
            var group = group_access_list[index];
            group.access = 0;
        }
    }
    refresh_groups_list();
});

//ADD DATASET ACCESS TO GROUPS
$('#addGroupBtn').click(function() {
    var group = {};
    group.id = $('#selectGroup').val();
    group.name = $('#selectGroup').parent().children('button').attr('title');
    if ($('#radio3').is(':checked')) {
        group.access = 1;
    } else {
        group.access = 0;
    }
    if (group.id) {
        add_grp_access(group);
    }
});

function change_grp_access(group_id) {
    if (group_access_list[group_id].access === 0) {
        group_access_list[group_id].access = 1;
    }
    else {
        group_access_list[group_id].access = 0;
    }
}

function add_grp_access(group) {
    if (!(group.id in group_access_list)) // Si VRAI alors le groupe n'éxiste pas encore dans la liste "group_access_list".
    {
        group_access_list[group.id] = group;
        refresh_groups_list();
    }
}

function remove_grp_access(group_id) {
    delete group_access_list[group_id];
    refresh_groups_list();
}

function refresh_groups_list() {
    if ($('#radio3').is(':checked')) {
        disable_checkbox = true;
    }else {
        disable_checkbox = false;
    }
    $('#datasetGroups').html('');
    for (var index in group_access_list) {
        var group = group_access_list[index];
        add_el(group, disable_checkbox);
    }
}

function add_el(group, disable_checkbox) {
    $('#datasetGroups').append(group_element(group.name, group.id, group.access, disable_checkbox));
}

function group_element(label, value, checked, disable_checkbox) {
    if (disable_checkbox) {
        str_disable = 'disabled';
    }
    else {
        str_disable = '';
    }
    if (checked) {
        str_checked = 'checked';
    }else {
        str_checked = '';
    }
    return '<li class="list-group-item" value="' + value + '">' +
        '<button type="button" onclick="remove_grp_access('+ value +')" class="btn-red btn-sm grp-rm-btn"><span class="glyphicon glyphicon-remove no-padding"></span></button>' +
        '<input '+ str_disable +' id="checkbox-' + label + '" type="checkbox" '+ str_checked +' onclick="change_grp_access('+ value +')" class="access grp-access" value="TRUE" name="' + label + '"/><label for="checkbox-' + label + '"><span class="ui"></span></label>' +
        label +
        '</li>';
}
