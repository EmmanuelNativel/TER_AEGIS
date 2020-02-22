/**
 * Initialisation du selecteur du champ "select_partners" (voir Selectize.js)
 */
$(document).ready(function() {
	var $select_partners = $('#select_partners').selectize({
		sortField: {
      delimiter: ";",
			direction: 'asc'
		}
	});
});
