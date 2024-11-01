let wpvgw_advanced_custom_fields_plugin_extension;

(function ($) {

	wpvgw_advanced_custom_fields_plugin_extension = {

		/**
		 * Initializes this object.
		 */
		init: function () {
			// hook into AJAX character count calculation
			if (typeof wp !== "undefined" && typeof wp.hooks !== "undefined") {
				wp.hooks.addAction('wpvgw_add_fields_for_character_count_calculation', 'wpvgw', this.add_fields_for_character_count_calculation);
			}
		},

		/**
		 * Add field texts for additional AJAX character count calculation.
		 */
		add_fields_for_character_count_calculation: function (additionalTexts) {
			// set of ACF field keys
			const keySet = new Set();

			// get ACF field information divs
			const acfFieldInformationDivs = $('div[data-wpvgw_character_count_calculation="true"]');

			// add ACF field texts to keySet
			acfFieldInformationDivs.each(function (index, fieldInfo) {
				// get ACF field key
				const fieldKey = $(fieldInfo).attr('data-wpvgw-afc-field-id');
				keySet.add(fieldKey);
			});

			// get all ACF fields
			// noinspection JSUnresolvedVariable,JSUnresolvedFunction
			const fields = acf.getFields({});

			// iterate ACF fields
			for (let i = 0; i < fields.length; i++) {
				// add ACF field texts to additionalTexts array
				if (keySet.has(fields[i].data.key) && (fields[i].data.type === 'text' || fields[i].data.type === 'textarea' || fields[i].data.type === 'wysiwyg')) {
					additionalTexts.push(fields[i].val());
				}
			}
		}
	};

	// bind to document ready function
	$(document).ready(function () {
		wpvgw_advanced_custom_fields_plugin_extension.init();
	});

}(jQuery));