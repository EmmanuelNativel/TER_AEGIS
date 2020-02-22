/**
 * Initialisation du selecteur du champ "Organisation" (voir Selectize.js)
 */
$(document).ready(function() {
	var $select_country = $('#select_country').selectize({
		sortField: {
			field: 'text',
			direction: 'asc'
		}
	});

	// $select_country[0].selectize.setValue($('#select_country').data('initial-value'));
});
