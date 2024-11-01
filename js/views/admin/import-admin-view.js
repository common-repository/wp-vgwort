let wpvgw_admin_view_import;

(function ($) {

	wpvgw_admin_view_import = {

		checkBoxIsOrderDateToday: null,
		linkOrderDateToday: null,
		textBoxOrderDate: null,


		init: function () {

			const myThis = this;

			this.checkBoxIsOrderDateToday = $('#wpvgw_is_order_date_today');
			this.linkOrderDateToday = $('#wpvgw_order_date_today');
			this.textBoxOrderDate = $('#wpvgw_order_date');

			myThis.set_order_date_ui_state();

			this.checkBoxIsOrderDateToday.click(function () {
						myThis.set_order_date_ui_state();
					}
			);

			this.linkOrderDateToday.click(function (e) {
				e.preventDefault();

				if (myThis.checkBoxIsOrderDateToday.prop('checked'))
					return;

				if (!myThis.linkOrderDateToday.hasClass('wpvgw-disabled')) {
					myThis.linkOrderDateToday.addClass('wpvgw-disabled');

					// collect AJAX post data
					// noinspection JSUnresolvedVariable
					const ajaxPostData = {
						_ajax_nonce: wpvgw_ajax_object.nonce, //nonce
						action: 'wpvgw_get_today_date'
					};

					// post AJAX data to WordPress AJAX url
					// noinspection JSUnresolvedVariable
					$.post(wpvgw_ajax_object.ajax_url, ajaxPostData,
							/**
							 * @param response AJAX response as JSON string.
							 */
							function (response) {
								if (myThis.checkBoxIsOrderDateToday.prop('checked'))
									return;

								// noinspection JSUnresolvedVariable
								myThis.textBoxOrderDate.val(response.today_date);
								myThis.linkOrderDateToday.removeClass('wpvgw-disabled');
							}
					);
				}
			});

		},

		set_order_date_ui_state: function () {
			if (this.checkBoxIsOrderDateToday.prop('checked')) {
				this.textBoxOrderDate.prop('disabled', true);
				this.linkOrderDateToday.addClass('wpvgw-disabled');
			} else {
				this.textBoxOrderDate.prop('disabled', false);
				this.linkOrderDateToday.removeClass('wpvgw-disabled');
			}
		}

	};

	$(document).ready(function () {
		wpvgw_admin_view_import.init();
	});

}(jQuery));