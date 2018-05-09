jQuery(function($) {
	$("#slideshow").css("overflow", "hidden");
	
	$("ul#slides").cycle({
		fx: 'fade',
		pause: 1,
		speed: 1500,
		timeout: 4000,
		prev: '#prev',
		next: '#next'
	});
	
	$("#slideshow").hover(function() {
    	$("ul#nav").fadeIn();
  	},
  		function() {
    	$("ul#nav").fadeOut();
  	});
	
});
