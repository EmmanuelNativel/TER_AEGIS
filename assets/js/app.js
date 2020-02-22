(function($, document, window){

	$(document).ready(function(){

		// Cloning main navigation for mobile menu
		$(".mobile-navigation").append($(".main-navigation .menu").clone());

		// Mobile menu toggle
		$(".menu-toggle").click(function(){
			$(".mobile-navigation").slideToggle();
		});



		$(".hero").flexslider({
			directionNav: false,
			controlNav: true,
			after: function(){
				var active_rel = $(this).find('.flex-active-slide').attr('rel');
				var active_id = $(".flex-active-slide").attr('id');
				$(".parallax-mirror[is-slide='true']").fadeOut(800);
				$('#' + active_id).fadeIn(800);
			},

		});
		/*
		var map = $(".map");
		var latitude = map.data("latitude");
		var longitude = map.data("longitude");
		if( map.length ){

			map.gmap3({
				map:{
					options:{
						center: [latitude,longitude],
						zoom: 15,
						scrollwheel: false
					}
				},
				marker:{
					latLng: [latitude,longitude],
				}
			});

		}
		*/
	});

	$(window).load(function(){
		$(".parallax-mirror[is-slide='true']").hide();
		$("#slide-1").show();
	});

})(jQuery, document, window);
