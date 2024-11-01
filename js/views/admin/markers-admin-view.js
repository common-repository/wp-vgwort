let wpvgw_admin_view_markers;

(function ($) {

	wpvgw_admin_view_markers = {

		init: function () {
			// copy action links
			const linksCopyPostContent = $('.wpvgw-markers-view-copy-post-content');
			const linksCopyPostTitle = $('.wpvgw-markers-view-copy-post-title');
			const linksCopyPrivateMarker = $('.wpvgw-markers-view-copy-private-marker');
			const linksCopyPostLink = $('.wpvgw-markers-view-copy-post-link');

			// copy window
			const divBackgroundCopyWindow = $('#wpvgw-markers-view-copy-window-background');
			const divCopyWindow = $('#wpvgw-markers-view-copy-window');
			const linkCloseCopyWindow = $('#wpvgw-markers-view-copy-window-close');
			const divContentCopyWindow = $('#wpvgw-markers-view-copy-window-content');
			// get original post content HTML (a descriptive text)
			const divPostContentHtml = divContentCopyWindow.html();


			// prevent closing window
			divCopyWindow.click(function (e) {
				// prevent closing window
				e.stopPropagation();
			});

			// close window by button
			linkCloseCopyWindow.click(function (e) {
				e.preventDefault();

				// avoids click recursion with background click
				e.stopPropagation();

				// hide window
				divBackgroundCopyWindow.hide();

				// restore original HTML
				divContentCopyWindow.html(divPostContentHtml);
			});

			// close window by background click
			divBackgroundCopyWindow.click(function () {
				linkCloseCopyWindow.trigger('click');
			});

			// close window by ESC key
			$(document).on('keydown', function (e) {
				if (e.keyCode === 27) { // ESC key
					linkCloseCopyWindow.trigger('click');
				}
			});

			// close window if user copies (Strg + V) text
			divContentCopyWindow.on('copy', function () {
				// TODO: Seems to be a hack.
				// timeout to let the copy (Strg + V) process
				setTimeout(function () {
							// close window
							linkCloseCopyWindow.trigger('click');
						},
						100
				);
			});

			// copy post content
			linksCopyPostContent.click(function (e) {
				e.preventDefault();

				// show window
				divBackgroundCopyWindow.show();

				// collect AJAX post data
				// noinspection JSUnresolvedVariable
				const ajaxPostData = {
					_ajax_nonce: wpvgw_ajax_object.nonce, //nonce
					action: 'wpvgw_get_post_content',
					wpvgw_post_id: $(this).data('object-id')
				};

				// post AJAX data to WordPress AJAX url
				// noinspection JSUnresolvedVariable
				$.post(wpvgw_ajax_object.ajax_url, ajaxPostData,
						/**
						 * @param response AJAX response as JSON string.
						 */
						function (response) {
							// be sure the window is shown
							if (divBackgroundCopyWindow.css('display') === 'none')
								return;

							const data = response;

							let content;

							// consider post excerpt?
							// noinspection JSUnresolvedVariable
							if (data.post_consider_excerpt)
								content = data.post_excerpt + '\n' + data.post_content;
							else
								content = data.post_content;

							// fill copy window
							wpvgw_admin_view_markers.fillCopyWindow(divContentCopyWindow, data.post_title, content);
						}
				);
			});

			// copy post title
			linksCopyPostTitle.click(function (e) {
				e.preventDefault();

				// get post ID
				const postId = $(this).data('object-id');
				// show window
				divBackgroundCopyWindow.show();
				// fill copy window
				wpvgw_admin_view_markers.fillCopyWindow(divContentCopyWindow, $('#wpvgw-markers-view-post-title-link-' + postId).text(), '');
			});

			// copy private marker
			linksCopyPrivateMarker.click(function (e) {
				e.preventDefault();

				// get marker ID
				const markerId = $(this).data('object-id');
				// show window
				divBackgroundCopyWindow.show();
				// fill copy window
				wpvgw_admin_view_markers.fillCopyWindow(divContentCopyWindow, $('#wpvgw-markers-view-private-marker-' + markerId).text(), '');
			});

			// copy post link
			linksCopyPostLink.click(function (e) {
				e.preventDefault();

				// get marker ID
				const postId = $(this).data('object-id');
				// show window
				divBackgroundCopyWindow.show();
				// fill copy window
				wpvgw_admin_view_markers.fillCopyWindow(divContentCopyWindow, $('#wpvgw-markers-view-post-title-link-' + postId).attr('href'), '');
			});


			/* settings for bulk action section  */
			const checkBoxMarkerDisabled = $('#wpvgw_e_marker_disabled');
			const checkBoxMarkerDisabledSet = $('#wpvgw_e_marker_disabled_set');

			checkBoxMarkerDisabled.prop('disabled', !checkBoxMarkerDisabledSet.prop('checked'));

			checkBoxMarkerDisabledSet.click(function () {
				if ($(this).prop('checked')) {
					checkBoxMarkerDisabled.prop('disabled', false);
				} else {
					checkBoxMarkerDisabled.prop('disabled', true);
				}
			});


			/* settings for bulk action section  */
			const checkBoxMarkerBlocked = $('#wpvgw_e_marker_blocked');
			const checkBoxMarkerBlockedSet = $('#wpvgw_e_marker_blocked_set');

			checkBoxMarkerBlocked.prop('disabled', !checkBoxMarkerBlockedSet.prop('checked'));

			checkBoxMarkerBlockedSet.click(function () {
				if ($(this).prop('checked')) {
					checkBoxMarkerBlocked.prop('disabled', false);
				} else {
					checkBoxMarkerBlocked.prop('disabled', true);
				}
			});


			/* settings for bulk action section  */
			const textBoxServer = $('#wpvgw_e_server');
			const checkBoxServerSet = $('#wpvgw_e_server_set');

			textBoxServer.prop('disabled', !checkBoxServerSet.prop('checked'));

			checkBoxServerSet.click(function () {
				if ($(this).prop('checked')) {
					textBoxServer.prop('disabled', false);
				} else {
					textBoxServer.prop('disabled', true);
				}
			});

			/* settings for bulk action section  */
			const textBoxOrderDate = $('#wpvgw_e_order_date');
			const checkBoxOrderDateSet = $('#wpvgw_e_order_date_set');

			textBoxOrderDate.prop('disabled', !checkBoxOrderDateSet.prop('checked'));

			checkBoxOrderDateSet.click(function () {
				if ($(this).prop('checked')) {
					textBoxOrderDate.prop('disabled', false);
				} else {
					textBoxOrderDate.prop('disabled', true);
				}
			});

			/* manage visibility of the markers bulk edit div */
			const divMarkersBulkEdit = $('#wpvgw-markers-bulk-edit');
			divMarkersBulkEdit.addClass('wpvgw-markers-bulk-edit');
			divMarkersBulkEdit.hide();

			// markers table
			const tableMarkers = $('#wpvgw-markers').find('> table.wpvgw_markers');
			// cancel button
			const linkCancelBulkEdit = divMarkersBulkEdit.find('a.cancel');

			// move bulk edit div before markers table
			tableMarkers.before(divMarkersBulkEdit);

			// hide bulk edit div if cancel button is clicked
			linkCancelBulkEdit.click(function (e) {
						e.preventDefault();
						divMarkersBulkEdit.hide();
					}
			);

			// copied from core (wordpress/wp-admin/js/inline-edit-post.js) and modified
			$('#doaction, #doaction2').click(function (e) {
				// first or second action
				const n = $(this).attr('id').substr(2);

				// if edit action is selected
				if ('edit' === $('select[name="' + n + '"]').val()) {
					// prevent button submit
					e.preventDefault();

					let oneIdChecked = false;
					// find all checkboxes in the markers table
					tableMarkers.find('th.check-column input[type="checkbox"]').each(function () {
						if ($(this).prop('checked')) {
							// at least one checkbox is checked
							oneIdChecked = true;
						}
					});

					// at least one checkbox checked?
					if (oneIdChecked) {
						// show bulk edit div
						divMarkersBulkEdit.show();
						// scroll to window top
						$('html, body').animate({scrollTop: 0}, 'fast');

					} else {
						// hide bulk edit div
						divMarkersBulkEdit.hide();
					}
				}
			});

		},


		/**
		 * Fills a copy window with title and content. User can then copy title and content.
		 *
		 * @param copyWindow A HTML div that acts as popup window.
		 * @param title Text title of the window.
		 * @param content HTML content that is shown in the window.
		 */
		fillCopyWindow: function (copyWindow, title, content) {
			// show post content and title in window
			copyWindow.html('<div id="wpvgw-markers-view-post-title">' + title + '</div>' + content);

			// reset scroll positions of the window
			// we set it 2 times to work around a bug in FireFox
			copyWindow.scrollTop(1);
			copyWindow.scrollTop(0);
			copyWindow.scrollLeft(1);
			copyWindow.scrollLeft(0);

			// select text for copy and paste
			wpvgw_admin_view_markers.selectText(copyWindow.attr('id'));
		},


		/**
		 * Selects text for copy and paste.
		 *
		 * @param element An element ID.
		 */
		selectText: function (element) {
			const text = document.getElementById(element);
			let range, selection;

			if (document.body.createTextRange) {
				range = document.body.createTextRange();
				range.moveToElementText(text);
				range.select();
			} else if (window.getSelection) {
				selection = window.getSelection();
				range = document.createRange();
				range.selectNodeContents(text);
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
	};


	$(document).ready(function () {
		wpvgw_admin_view_markers.init();
	});

}(jQuery));