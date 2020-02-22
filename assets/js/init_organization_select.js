/**
 * Initialisation du selecteur du champ "Organisation" (voir Selectize.js)
 */
$(document).ready(function() {
	var $select_org = $('#select-organization').selectize({
		sortField: {
			field: 'text',
			direction: 'asc'
		}
	});

	$select_org[0].selectize.setValue($('#select-organization').data('initial-value'));
});
