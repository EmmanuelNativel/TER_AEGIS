/**
* Initialisation du selecteur du champ "select_users" (voir Selectize.js)
*/
$(document).ready(function() {
	var $select_users = $('#select_users').selectize({
		sortField: {
			delimiter: ";",
			direction: 'asc'
		}
	});
});
