// Task Window
let wpvgw_task_window;

(function ($) {

	wpvgw_task_window = {

		divBackground: null,
		divContent: null,
		divButton: null,
		spanSpinner: null,
		/**
		 * Indicates whether a task abort is requested.
		 */
		abort: false,
		/**
		 * Indicates whether a task is running.
		 */
		running: false,


		init: function (custom_function) {
			// make custom_function optional
			custom_function = typeof custom_function !== 'undefined' ? custom_function : null;

			// return if a task is already running
			if (this.running)
				return;

			const myThis = this;

			this.abort = false;

			// get UI elements
			this.divBackground = $('#wpvgw-task-window-background');
			const divTaskWindow = $('#wpvgw-task-window');
			this.divButton = $('#wpvgw-task-window-button');
			this.divContent = $('#wpvgw-task-window-content');
			this.spanSpinner = $('#wpvgw-task-window-spinner');

			// some UI sugar
			// noinspection JSUnresolvedVariable
			this.divContent.html('<p>' + wpvgw_ajax_object.start_message + '</p>');
			this.spanSpinner.show();
			// noinspection JSUnresolvedVariable
			this.divButton.html(wpvgw_ajax_object.button_abort_text);
			this.divButton.attr('class', 'button');

			// prevent closing window
			divTaskWindow.click(function (e) {
				// prevent closing window
				e.stopPropagation();
			});

			// close window by button
			this.divButton.click(function (e) {
				e.preventDefault();

				// avoids click recursion with background click
				e.stopPropagation();

				// abort or close task
				if (myThis.running) {
					myThis.abort = true;
					// noinspection JSUnresolvedVariable
					myThis.divContent.html('<p>' + wpvgw_ajax_object.aborting_message + '</p>');
				}
				else {
					myThis.close();
				}
			});

			// close window by ESC key
			$(document).on('keydown', function (e) {
				// ESC key and task not running
				if (e.keyCode === 27 && !myThis.running) {
					myThis.divButton.trigger('click');
				}
			});

			// run custom function
			if (custom_function !== null)
				custom_function();
		},

		/**
		 * Close the window.
		 */
		close: function () {
			// hide window
			this.divBackground.hide();
			// blank content
			this.divContent.html('');
			// task no longer runs
			this.running = false;
		},

		/**
		 * Run the task.
		 * @param task_id The ID of the task to run.
		 */
		run_task: function (task_id) {
			if (this.running)
				return;

			const myThis = this;

			// task is running
			this.running = true;
			this.divBackground.show();

			setTimeout(function () {
				myThis.internal_run_task(task_id, null, 0, myThis.divContent)
			}, 1);
		},

		/**
		 * Runs the task. It’s for internal  propose.
		 *
		 * @param task_id The ID of the task to run.
		 * @param stats A custom status object.
		 * @param offset The offset of the task.
		 * @param status_element The HTML element where the status text will be output.
		 */
		internal_run_task: function (task_id, stats, offset, status_element) {

			const myThis = this;

			// abort task?
			if (this.abort) {
				this.close();
				return;
			}

			// collect AJAX post data
			// noinspection JSUnresolvedVariable
			const ajaxPostData = {
				_ajax_nonce: wpvgw_ajax_object.nonce, //nonce
				action: 'wpvgw_task_' + task_id,
				wpvgw_task_stats: stats,
				wpvgw_task_offset: offset
			};

			// post AJAX data to WordPress AJAX url
			// noinspection JSUnresolvedVariable
			$.post(wpvgw_ajax_object.ajax_url, ajaxPostData,
					/**
					 * @param response AJAX response as JSON string.
					 */
					function (response) {
						// abort task?
						if (myThis.abort) {
							myThis.close();
							return;
						}

						const data = response;

						// get response data
						offset = data.offset;
						// noinspection JSUnresolvedVariable
						stats = data.stats;

						// output status text
						// noinspection JSUnresolvedVariable
						status_element.html('<p>' + data.status_text + '</p>');

						// continue task?
						// noinspection JSUnresolvedVariable
						if (data.has_more_steps) {
							// run next task step
							setTimeout(function () {
								myThis.internal_run_task(task_id, stats, offset, status_element)
							}, 1);
						}
						else {
							// task finished
							let messageType;
							let messagesHtml = '';
							const messages = data.messages;
							const messagesLength = messages.length;

							// output messages
							for (let i = 0; i < messagesLength; i++) {
								messageType = 'updated wpvgw-progress-message-update';
								if (messages[i][1] === 0)
									messageType = 'error wpvgw-progress-message-error';
								messagesHtml += "<div class=\"" + messageType + "\">" + messages[i][0] + "</div>";
							}

							// some UI sugar
							// noinspection JSUnresolvedVariable
							myThis.divButton.html(wpvgw_ajax_object.button_close_text);
							myThis.divButton.attr('class', 'button-primary');
							myThis.spanSpinner.hide();
							status_element.html("<div class=\"wpvgw-progress-messages\">" + messagesHtml + "</div>");
							// reset scrolling
							// we set it 2 times to work around a bug in FireFox
							status_element.scrollTop(1);
							status_element.scrollTop(0);

							// task no longer runs
							myThis.running = false;
						}
					}
			);

		}

	};

}(jQuery));