function verifie_pseudo() {
	$('#user-availability-status').html('<p><img src="'+ ImgURL + 'hourglass.gif" id="loaderIcon" style="width: 30px"/></p>');
	var pseudo = document.getElementById("pseudo").value;

	if (pseudo.length < 3 || pseudo.length > 30) {
		$('#user-availability-status').html('');
		return;
	}
	else {
		$.ajax({ // Envoie le pseudo avec Ajax en methode GET (Call back)
			url  : SiteURL + "welcome/pseudo_exist/NULL/ajax",
			type : 'GET',
			data : 'pseudo=' + pseudo,
			dataType: 'html',
			success: function(reponse) {
				if (reponse == 'FALSE') {
					$('#user-availability-status').html('<div class="alert alert-success" role="alert">Cet identifiant est disponible</div>');
				}
				else
				{
					$('#user-availability-status').html('<div class="alert alert-danger" role="alert">Cet identifiant éxiste déja!</div>');
				}

			}
		});
	}
}
