$(document).ready(function() {

/* ------------------------------------- AJAX var in session ------------------------------------------*/
	$(document).on("click", ".btn_remove_var_session", function(e){
		var_name = $(this).attr('data-var_name');

	    //url = window.location.origin +"/daphne.cirad.fr/index.php/variables/remove_var_in_session"; //local
			url = SiteURL + "/variables/remove_var_in_session"
	    //url = window.location.origin +"/index.php/variables/remove_var_in_session";
	    $.ajax({
	      type: "POST",
	      url: url,
	      data: {
	      	var_name : var_name,
	      },
	      dataType: "json",
	      cache:false,
	      success: function(data){
	      	if (data == -1) {
	        	alert("Problème lors de la suppression de la variable du panier");

	      	}else if (data == 1) {
	      		div_name = "[id='"+var_name+"'";
	      		$(div_name).remove();
	      		if ($("#notification_nb_var").length != 0) {
	      			val = parseInt($("#notification_nb_var").text());
	      			if (val == 1) {
	      				$("#notification_nb_var").remove();
	      				$("#div_variables_session").append("<h3 class='text-center'>Aucune variable dans votre panier</h3>")
	      			}else{
	      				$("#notification_nb_var").text(val-1);
	      			}
	      		}
	      	}
	      },
	      error: function (data, status, err) {
	        console.log('Something went wrong', status, err);
	        console.log(data);
	        alert("Erreur lors de la suppression de la variable du panier");
	      }
	    });
	});
});
