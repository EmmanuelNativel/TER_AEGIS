/**
 * Initialisation du selecteur du champ "select_wp" (voir Selectize.js)
 */
$(document).ready(function() {
	var $select_wp = $('#select_wp').selectize({
		sortField: {
			field: 'text',
			direction: 'asc'
		}
	});
});
