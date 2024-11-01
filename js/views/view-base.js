let wpvgw_view_base;

(function ($) {

	wpvgw_view_base = {

		/**
		 * Initializes this object.
		 */
		init: function () {

			// get all description spans
			const descriptions = $("span.wpvgw-description, span.wpvgw-warning-description");

			// hide descriptions
			descriptions.hide();

			// add description icon to each description
			descriptions.each(function (index, element) {
				const spanDescription = $(element);
				// add description icon (by icon font)
				let button;
				if ($(this).attr('class').split(' ')[1] === 'wpvgw-description')
					button = $('<a class="wpvgw-description-button" href="#">&#xE901;</a>');
				else
					button = $('<a class="wpvgw-warning-description-button" href="#">&#xE902;</a>');
				// noinspection JSCheckFunctionSignatures
				button.insertBefore(spanDescription);

				// add toggle function to description icon
				button.click(function (e) {
					e.preventDefault();
					// hide message
					spanDescription.toggle();
				});

			});
		}
	};

	// bind to document ready function
	$(document).ready(function () {
		wpvgw_view_base.init();
	});

}(jQuery));