/**
* Application pour la création d'echantillons dans DAPHNE.
*/
$(document).ready(function() {
  $('.selectpicker').selectpicker(); //initalisation des selectpickers
  id_essai =$("#essai_name").val();
  set_field_exp_unit(id_essai, false);
  set_field_sample_stage(id_essai, false);
  set_field_for_modals(id_essai);

  //affichage d'un des modals en fonction des erreurs
  //complète automatiquement les champs déjà saisis
  if($(".error_add_sample_stage").length > 0) {
    essai_name_before = $('#essai_name_add_sample_stage').attr('data-essai_name');
    $('#essai_name_add_sample_stage').selectpicker('val', essai_name_before);
    $('#addSampleStageModal').modal('show');
  }
  if($(".error_update_sample_stage").length > 0) {
    essai_name_before = $('#essai_name_update_sample_stage').attr('data-essai_name');
    $('#essai_name_update_sample_stage').selectpicker('val', essai_name_before);
    set_field_update_sample_stage(essai_name_before);
    sample_stage_before = $('#update_sample_stage').attr('data-sample_stage');
    $('#update_sample_stage').selectpicker('val', sample_stage_before);

    $('#updateSampleStageModal').modal('show');
  }
  if($(".error_delete_sample_stage").length > 0) {
    essai_name_before = $('#essai_name_delete_sample_stage').attr('data-essai_name');
    $('#essai_name_delete_sample_stage').selectpicker('val', essai_name_before);
    set_field_delete_sample_stage(essai_name_before);
    sample_stage_before = $('#delete_sample_stage').attr('data-sample_stage');
    $('#delete_sample_stage').selectpicker('val', sample_stage_before);

    $('#deleteSampleStageModal').modal('show');
  }

  //change les valeurs des selectpicker 'Sample_stage' et 'exp_unit' en fonction de l'essai choisi
  $("#essai_name").on('change', function()
  {
    id_essai = $(this).val();
    set_field_exp_unit(id_essai);
    set_field_sample_stage(id_essai);
    set_field_for_modals(id_essai);
  });

  //change les valeurs des selectpicker 'Sample_stage' dans les modals en fonction du selectpicker de l'échantillon
  $("#sample_stage").on('change', function()
  {
    id_sample_stage = $(this).val();
    $('.selectpicker_sample_stage').selectpicker('val', id_sample_stage);
  });

  //UPDATE SAMPLE STAGE
  //change les valeurs du selectpicker 'Sample_stage' en fonction de l'essai choisi
  $("#essai_name_update_sample_stage").on('change', function()
  {
    id_essai = $(this).val();
    set_field_update_sample_stage(id_essai);
  });

  $("#update_sample_stage").on('change', function()
  {
    id_sample_stage = $(this).val();
    sample_stage_name = $(this).find(':selected').attr('data-st_name');
    sample_stage_physio = $(this).find(':selected').attr('data-st_physio_stage');
    $("#update_sample_stage_name").val(sample_stage_name);
    $("#update_sample_stage_physio_stage").val(sample_stage_physio);
  });

  //DELETE SAMPLE STAGE
  //change les valeurs du selectpicker 'Sample_stage' en fonction de l'essai choisi
  $("#essai_name_delete_sample_stage").on('change', function()
  {
    id_essai = $(this).val();
    set_field_delete_sample_stage(id_essai);
  });

  $("#deselect_exp_unit").on('click', function()
  {
    $('select[name="exp_unit[]"]').selectpicker('deselectAll');
    $('select[name="exp_unit[]"]').selectpicker('refresh');
  });

  $("#select_all_exp_unit").on('click', function()
  {
    array_to_select = [];
    $('select[name="exp_unit[]"] option:visible').each(function() {
      if ($(this).val() != -2) {
        array_to_select.push($(this).val());
      }
    });
    $('select[name="exp_unit[]"]').selectpicker('val', array_to_select);
    $('select[name="exp_unit[]"]').selectpicker('refresh');
  });

  // Met à jour le selectPicker correspondant aux unitées experimentales de l'essai choisi
  function set_field_exp_unit(id_essai, bool_deselect=true){
    $('select[name="exp_unit[]"]').children('option').hide();
    $('select[name="exp_unit[]"]').children('option[data-id_trial="'+id_essai+'"]').show();

    new_val_select_exp_unit = $('select[name="exp_unit[]"]').find('[data-id_trial="'+id_essai+'"]').first().val();
    if (new_val_select_exp_unit == null) {                           //si aucune exp_unit present sur cet essai
      $('select[name="exp_unit[]"]').children('option[value=-2]').show();   //option unselectable 'aucun ex_unit'
    }else{
      $('select[name="exp_unit[]"]').children('option[value=-1]').show();
    }
    if (bool_deselect) {
      $('select[name="exp_unit[]"]').val('');
      $('select[name="exp_unit[]"]').selectpicker('deselectAll');
    }
    $('select[name="exp_unit[]"]').selectpicker('refresh');
  }

  // Met à jour tous les selectPickers correspondant aux sample stage de l'essai choisi
  function set_field_sample_stage(id_essai, bool_deselect=true){
    $('.selectpicker_sample_stage').children('option').hide();
    $('.selectpicker_sample_stage').children('option[data-id_trial="'+id_essai+'"]').show();

    new_val_select_sample_stage = $('.selectpicker_sample_stage').find('[data-id_trial="'+id_essai+'"]').first().val();
    if (new_val_select_sample_stage == null) {                               //si aucune sample_stage present sur cet essai
      $('.selectpicker_sample_stage').children('option[value=-2]').show();   //option unselectable 'aucun sample_stage'
    }

    if (bool_deselect) {
      $('.selectpicker_sample_stage').val('');
      $('.selectpicker_sample_stage').selectpicker('deselectAll');
    }
    $('.selectpicker_sample_stage').selectpicker('refresh');
  }

  // Met à jour le selectPicker correspondant aux sample stage de l'essai choisi pour la modification d'un sample stage
  function set_field_update_sample_stage(id_essai, bool_deselect=true){
    $('select[name=update_sample_stage]').children('option').hide();
    $('select[name=update_sample_stage]').children('option[data-id_trial="'+id_essai+'"]').show();

    new_val_select_sample_stage = $('select[name=update_sample_stage]').find('[data-id_trial="'+id_essai+'"]').first().val();
    if (new_val_select_sample_stage == null) {                               //si aucune sample_stage present sur cet essai
      $('select[name=update_sample_stage]').children('option[value=-2]').show();   //option unselectable 'aucun sample_stage'
    }

    if (bool_deselect) {
      $('select[name=update_sample_stage]').val('');
      $('select[name=update_sample_stage]').selectpicker('deselectAll');
    }
    $('select[name=update_sample_stage]').selectpicker('refresh');
  }

  // Met à jour le selectPicker correspondant aux sample stage de l'essai choisi pour la suppression d'un sample stage
  function set_field_delete_sample_stage(id_essai, bool_deselect=true){
    $('select[name=delete_sample_stage]').children('option').hide();
    $('select[name=delete_sample_stage]').children('option[data-id_trial="'+id_essai+'"]').show();

    new_val_select_sample_stage = $('select[name=delete_sample_stage]').find('[data-id_trial="'+id_essai+'"]').first().val();
    if (new_val_select_sample_stage == null) {                               //si aucune sample_stage present sur cet essai
      $('select[name=delete_sample_stage]').children('option[value=-2]').show();   //option unselectable 'aucun sample_stage'
    }

    if (bool_deselect) {
      $('select[name=delete_sample_stage]').val('');
      $('select[name=delete_sample_stage]').selectpicker('deselectAll');
    }
    $('select[name=delete_sample_stage]').selectpicker('refresh');
  }

  // Met à jour tous les selectPickers correspondant à l'essai choisi pour la création d'un echantillon
  function set_field_for_modals(id_essai){
    $('.selectpicker_essai').selectpicker('val', id_essai);
  }
});
