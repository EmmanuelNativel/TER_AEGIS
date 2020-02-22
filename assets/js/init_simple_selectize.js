/**
* Initialisation des selecteurs
*/
$(document).ready(function() {
	$('.selectize').selectize({
		sortField: {
			delimiter: ";",
			direction: 'asc'
		}
	});
});
