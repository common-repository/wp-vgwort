let wpvgw_post_view;

(function ($) {

	wpvgw_post_view = {

		// is Gutenberg editor enabled?
		isGutenbergEditor: false,


		/**
		 * Initializes this object.
		 */
		init: function () {
			this.isGutenbergEditor = document.body.classList.contains("block-editor-page");
					/*typeof wp !== "undefined" &&
					typeof wp.data !== "undefined" &&
					typeof wp.data.select("core/editor") !== "undefined" &&
					wp.data.select("core/editor") !== null;*/

			this.initControls();

			// Gutenberg editor saving event
			if (this.isGutenbergEditor) {
				const editPost = wp.data.select("core/edit-post");
				let lastIsSaving = false;

				wp.data.subscribe(function () {
					const isSaving = editPost.isSavingMetaBoxes();
					if (isSaving !== lastIsSaving && !isSaving) {
						lastIsSaving = isSaving;
						// Assume saving has finished
						// render updated meta box
						wpvgw_post_view.renderMetaBox();
					}

					lastIsSaving = isSaving;
				});
			}

			if (!wpvgw_ajax_object.isMedia) {
				// start timer after 2 s
				//this.printCharacterCount(true);
				//this.printCharacterCountTimer();
				// TODO: hack, 2 s delay to initialize tiny mce or textarea
				setTimeout(function () {
					wpvgw_post_view.printCharacterCount(true)
				}, 2000);
			}
		},

		initControls: function () {
			this.hideShowControls();

			if (!wpvgw_ajax_object.isMedia) {
				this.refreshCharacterCountLinkDisabled = false;

				// refresh character count link (button like)
				this.spinnerRefreshCharacterCount = $('#wpvgw_refresh_character_count_spinner');
				this.linkRefreshCharacterCount = $('#wpvgw_refresh_character_count');
				this.linkRefreshCharacterCount.click(function (e) {
					e.preventDefault();

					if (!wpvgw_post_view.linkRefreshCharacterCount.hasClass('wpvgw-disabled')) {
						// disable refresh character count link
						wpvgw_post_view.linkRefreshCharacterCount.addClass('wpvgw-disabled');
						// show spinner
						wpvgw_post_view.spinnerRefreshCharacterCount.show();
					}

					// update link disabled?
					if (wpvgw_post_view.refreshCharacterCountLinkDisabled)
						return;

					// reset timer
					wpvgw_post_view.printCharacterCount(false);
				});

				// show spinner
				this.spinnerRefreshCharacterCount.show();
				this.spinnerRefreshCharacterCount.css('visibility', 'visible');
				// disable refresh character count link
				this.linkRefreshCharacterCount.addClass('wpvgw-disabled');
			}
		},

		/**
		 * Hides or shows controls in the VG WORT meta box depend on marker check box.
		 */
		hideShowControls: function () {
			// find marker check box
			const checkBoxSetMarker = $('#wpvgw_set_marker');
			const checkBoxAutoMarker = $('#wpvgw_auto_marker');

			// test if marker check box was found
			if (checkBoxSetMarker.length === 0)
				return;

			// find controls to hide by CSS class
			const trAddMarkerToPost = $('#wpvgw_add_marker_to_post');

			// hide controls if marker check box is not checked
			if (!checkBoxSetMarker.prop('checked'))
				trAddMarkerToPost.hide();

			// bind show and hide controls to marker check box click event
			checkBoxSetMarker.click(function () {
				if ($(this).prop('checked')) {
					trAddMarkerToPost.show();
				} else {
					trAddMarkerToPost.hide();
				}
			});


			// show hide manual marker
			const divManualMarker = $('#wpvgw_manual_marker');
			const hideManualMarkerFunction = function () {
				if (checkBoxAutoMarker.prop('checked')) {
					divManualMarker.hide();
				} else {
					divManualMarker.show();
				}
			};

			// initial hide
			hideManualMarkerFunction();

			// bind show and hide controls to auto marker check box click event
			checkBoxAutoMarker.click(hideManualMarkerFunction);
		},

		/**
		 * Sets a timer to refresh the character count for the current post.
		 *
		 * @param timeout Number of milliseconds to wait.
		 */
		printCharacterCountTimer: function (timeout) {
			setTimeout(function () {
				wpvgw_post_view.printCharacterCount(true)
			}, timeout);
		},

		/**
		 * Outputs the character count for the current post.
		 */
		printCharacterCount: function (resetTimer) {
			// disable update link
			this.refreshCharacterCountLinkDisabled = true;

			let postTitle = this.isGutenbergEditor ? wp.data.select('core/editor').getEditedPostAttribute('title') : $('#title').val();
			if (postTitle == null)
				postTitle = "";

			// get post content from tiny mce
			if (!this.isGutenbergEditor)
				if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
					// save tiny mce content to textarea
					tinyMCE.triggerSave();
				}

			// get post content
			//var postContent = this.isGutenbergEditor ? $('textarea[id^=post-content-]').val() : $('#content').val();
			let postContent = this.isGutenbergEditor ? wp.data.select('core/editor').getEditedPostAttribute('content') : $('#content').val();
			if (postContent == null)
				postContent = "";

			// get post excerpt
			let postExcerpt = this.isGutenbergEditor ? wp.data.select('core/editor').getEditedPostAttribute('excerpt') : $('#excerpt').val();
			if (postExcerpt == null)
				postExcerpt = "";

			// provide hook for AJAX character count calculation
			const additionalTexts = [];
			if (typeof wp !== "undefined" && typeof wp.hooks !== "undefined") {
				wp.hooks.doAction('wpvgw_add_fields_for_character_count_calculation', additionalTexts);
			}

			// collect AJAX post data
			// noinspection JSUnresolvedVariable
			const ajaxPostData = {
				_ajax_nonce: wpvgw_ajax_object.nonce, //nonce
				action: 'wpvgw_get_character_count',
				wpvgw_post_title: postTitle,
				wpvgw_post_content: postContent,
				wpvgw_post_excerpt: postExcerpt,
				/**
				 * Get additional data related to the current post. This data will be submitted by the character count calculation ajax call.
				 *
				 * @returns object An custom object.
				 */
				/*function wpvgw_post_get_custom_object () {
				 	return {'custom_field1' : 'Jahr um Jahr musste ich', 'custom_field2' : 'zum Geigenunterricht.'};
				 }*/
				wpvgw_post_custom_object: (typeof wpvgw_post_get_custom_object === "function" ? wpvgw_post_get_custom_object() : null),
				wpvgw_additional_texts: additionalTexts
			};

			// noinspection JSUnresolvedVariable
			const timout = parseInt(wpvgw_ajax_object.refresh_character_count_timeout);

			// post AJAX data to WordPress AJAX url
			// noinspection JSUnresolvedVariable
			$.post(wpvgw_ajax_object.ajax_url, ajaxPostData,
					/**
					 * @param response AJAX response as JSON string.
					 */
					function (response) {
						const data = response;

						// output post character count data
						// noinspection JSUnresolvedVariable
						$('#wpvgw_character_count').html(data.character_count);
						// noinspection JSUnresolvedVariable
						$('#wpvgw_character_count_sufficient').html(data.character_count_sufficient);
						// noinspection JSUnresolvedVariable
						$('#wpvgw_missing_character_count').html(data.missing_character_count);
						// noinspection JSUnresolvedVariable
						$('#wpvgw_minimum_character_count').html(data.minimum_character_count);

						// reset timer
						if (resetTimer && timout !== -1)
							wpvgw_post_view.printCharacterCountTimer(timout);

						// enable update link
						wpvgw_post_view.refreshCharacterCountLinkDisabled = false;
						// enable refresh character count link
						wpvgw_post_view.linkRefreshCharacterCount.removeClass('wpvgw-disabled');
						// hide spinner
						wpvgw_post_view.spinnerRefreshCharacterCount.hide();
					}
			);
		},

		/**
		 * Renders the meta box via AJAX.
		 */
		renderMetaBox: function () {
			// get current post ID
			const currentPostId = wp.data.select("core/editor").getCurrentPostId();

			// collect AJAX post data
			// noinspection JSUnresolvedVariable
			const ajaxPostData = {
				_ajax_nonce: wpvgw_ajax_object.nonce, //nonce
				action: 'wpvgw_get_meta_box',
				wpvgw_post_id: currentPostId
			};

			// post AJAX data to WordPress AJAX url
			// noinspection JSUnresolvedVariable
			$.post(wpvgw_ajax_object.ajax_url, ajaxPostData,
					/**
					 * @param response AJAX response as JSON string.
					 */
					function (response) {
						const data = response;

						// get notices object
						const noticesObject = wp.data.dispatch("core/notices");

						// remove old notices shown in block editor
						for (let j = 0; j < data.notices_slugs.length; j++) {
							noticesObject.removeNotice("wpvgw-" + data.notices_slugs[j]);
						}

						// replace meta box
						$('#wpvgw-postview-meta-box').replaceWith(data.meta_box_html);

						// reinit controls
						wpvgw_post_view.initControls();
						// reinit view base
						wpvgw_view_base.init();


						// iterate and show notices
						for (let i = 0; i < data.notices.length; i++) {
							const noticeOptions = {
								id: data.notices[i]["code"],
								isDismissible: true
							};
							noticeOptions.actions = [];

							let message = data.notices[i]["message"];

							// TODO: hack, links are matched and extracted from notice
							const linkMatch = message.match(/\s<a href=.+?<\/a>/, "");

							if (linkMatch != null) {
								// remove link from message
								message = message.substr(0, linkMatch.index) + message.substr(linkMatch.index + linkMatch[0].length);

								// get label/text and url from link
								let label = linkMatch[0].match(/>(.+?)</);
								label = label[1];
								let url = linkMatch[0].match(/href="(.+?)"/);
								url = url[1];

								// set notice link options
								noticeOptions.actions = [
									{
										label: label,
										url: url
									}
								];
							}

							// create notice
							noticesObject.createNotice(data.notices[i]["type"], message, noticeOptions);
						}

						if (!wpvgw_ajax_object.isMedia)
								// print character count
							wpvgw_post_view.printCharacterCount(false);
					}
			);
		}

	};

	// bind to document ready function
	$(document).ready(function () {
		wpvgw_post_view.init();
	});

}(jQuery));
