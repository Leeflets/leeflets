(function($) {

	$(document).keyup(function(e) {
		if ( e.keyCode == 27 ) { // Escape key
			if ( window.parent.leeflets ) {
				window.parent.leeflets.hide_all();
			}
		}
	});

})(jQuery);
