let wpvgw_admin_view_operations;

(function ($) {

	wpvgw_admin_view_operations = {

		init: function () {
			const textBoxManualMarkersRegex = $('#wpvgw_operation_import_old_manual_markers_regex');
			const checkBoxDeleteManualMarkers = $('#wpvgw_operation_import_old_manual_markers_delete');
			const checkBoxUnlockImportManualMarkers = $('#wpvgw_operation_import_old_manual_markers_unlock');
			const buttonImportManualMarkers = $('#wpvgw_operation_import_old_manual_markers');

			checkBoxUnlockImportManualMarkers.prop('checked', false);
			textBoxManualMarkersRegex.prop('disabled', true);
			checkBoxDeleteManualMarkers.prop('disabled', true);
			buttonImportManualMarkers.prop('disabled', true);

			checkBoxUnlockImportManualMarkers.click(function () {
				if ($(this).prop('checked')) {
					textBoxManualMarkersRegex.prop('disabled', false);
					checkBoxDeleteManualMarkers.prop('disabled', false);
					buttonImportManualMarkers.prop('disabled', false);
				} else {
					textBoxManualMarkersRegex.prop('disabled', true);
					checkBoxDeleteManualMarkers.prop('disabled', true);
					buttonImportManualMarkers.prop('disabled', true);
				}
			});


			$('#wpvgw_operation_recalculate_character_count').click(function (e) {
				e.preventDefault();
				wpvgw_task_window.init();
				wpvgw_task_window.run_task('recalculate_post_character_count');
			});

			$('#wpvgw_operation_import_old_worthy_plugin_markers').click(function (e) {
				e.preventDefault();
				wpvgw_task_window.init();
				wpvgw_task_window.run_task('import_old_worthy_plugin_markers');
			});

			$('#wpvgw_operation_import_old_tl_vgwort_plugin_markers').click(function (e) {
				e.preventDefault();
				wpvgw_task_window.init();
				wpvgw_task_window.run_task('import_old_tl_vgwort_plugin_markers');
			});

			$('#wpvgw_operation_import_old_vgw_plugin_markers').click(function (e) {
				e.preventDefault();
				wpvgw_task_window.init();
				wpvgw_task_window.run_task('import_old_vgw_plugin_markers');
			});

			$('#wpvgw_operation_import_prosodia_vgw_markers').click(function (e) {
				e.preventDefault();
				wpvgw_task_window.init();
				wpvgw_task_window.run_task('import_prosodia_vgw_markers');
			});
		}

	};

	$(document).ready(function () {
		wpvgw_admin_view_operations.init();
	});

}(jQuery));