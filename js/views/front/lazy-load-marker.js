// Lazy Load Marker JS
let wpvgw_lazy_load_marker;

(function ($) {

	wpvgw_lazy_load_marker = {

		/**
		 * Initializes this object.
		 */
		init: function () {
			const divMarker = $('#wpvgw-marker');
			const imgSrc = divMarker.attr('data-src'); // get image source from data attribute
			divMarker.replaceWith('<img src="' + imgSrc + '" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" referrerpolicy="no-referrer-when-downgrade" style="display:none;" />');
		}
	};

	// bind to document ready function
	$(window).on('load', function(){
		wpvgw_lazy_load_marker.init();
	});

}(jQuery));