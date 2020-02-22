$('#select-table').selectize({
  sortField: {
    field: 'text',
    direction: 'asc'
  }
});

$('#config_elements').hide();

$('#config').click(function(){
  $('#config_elements').fadeToggle( "slow", "linear" );
});
