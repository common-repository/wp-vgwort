let wpvgw_admin_view_configuration;

(function ($) {

	wpvgw_admin_view_configuration = {

		linkGenerateApiKey: null,
		linkDeleteApiKey: null,
		textBoxApiKey: null,


		init: function () {
			const myThis = this;

			this.linkGenerateApiKey = $('#wpvgw_api_key_generator');
			this.linkDeleteApiKey = $('#wpvgw_api_key_delete');
			this.textBoxApiKey = $('#wpvgw_api_key');

			this.linkGenerateApiKey.click(myThis.linkClick);
			this.linkDeleteApiKey.click(myThis.linkClick);
		},

		linkClick: function (e) {
			e.preventDefault();

			const targetLink = e.target;
			const myThis = wpvgw_admin_view_configuration;

			if (!myThis.linkGenerateApiKey.hasClass('wpvgw-disabled') && !myThis.linkDeleteApiKey.hasClass('wpvgw-disabled')) {
				myThis.linkGenerateApiKey.addClass('wpvgw-disabled');
				myThis.linkDeleteApiKey.addClass('wpvgw-disabled');

				// collect AJAX post data
				// noinspection JSUnresolvedVariable
				const ajaxPostData = {
					_ajax_nonce: wpvgw_ajax_object.nonce, //nonce
					action: 'wpvgw_generate_api_key',
					is_delete: myThis.linkDeleteApiKey.is(targetLink),
				};

				// post AJAX data to WordPress AJAX url
				// noinspection JSUnresolvedVariable
				$.post(wpvgw_ajax_object.ajax_url, ajaxPostData,
						/**
						 * @param response AJAX response as JSON string.
						 */
						function (response) {
							// noinspection JSUnresolvedVariable
							myThis.textBoxApiKey.val(response.api_key);
							myThis.linkGenerateApiKey.removeClass('wpvgw-disabled');
							myThis.linkDeleteApiKey.removeClass('wpvgw-disabled');
						}
				);
			}
		},

	};

	$(document).ready(function () {
		wpvgw_admin_view_configuration.init();
	});

}(jQuery));