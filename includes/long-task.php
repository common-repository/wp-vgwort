<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_LongTask
 {
     const AJAX_NONCE_STRING = WPVGW . '-ajax-nonce-task-window';
     private $taskId;
     private $iterationsPerStep;
     private $runFunction;
     private $endFunction;
     private $statsClassName;
     private $statusTextTemplate;
     private $autoRun = false;
     private static $maxExecutionTime;
     public function is_auto_run()
     {
         return $this->autoRun;
     }
     public function set_auto_run($auto_run)
     {
         $this->autoRun = $auto_run;
     }
     public static function set_max_execution_time($max_execution_time)
     {
         self::$maxExecutionTime = $max_execution_time;
     }
     public function __construct($task_id, $iterations_per_step, $status_text_template, callable $run_function, callable $end_function, $stats_class_name)
     {
         $this->taskId = $task_id;
         $this->iterationsPerStep = $iterations_per_step;
         $this->statusTextTemplate = $status_text_template;
         $this->runFunction = $run_function;
         $this->endFunction = $end_function;
         $this->statsClassName = $stats_class_name;
         if (!is_subclass_of(new $this->statsClassName(), WPVGW_LongTaskStats::class, false)) {
             throw new TypeError(sprintf('$stats_class_name with type "%s" has to implement WPVGW_LongTaskStats.', $stats_class_name));
         }
         add_action('wp_ajax_' . WPVGW . '_task_' . $this->taskId, array( $this, 'ajax_run' ));
     }
     public function ajax_run()
     {
         check_ajax_referer(self::AJAX_NONCE_STRING);
         @ini_set('max_execution_time', self::$maxExecutionTime);
         $stats = $_POST['wpvgw_task_stats'] ?? null;
         if ($stats === '') {
             $stats = null;
         } else {
             $stats = json_decode(stripslashes($stats), true);
             if ($stats === false || !is_array($stats)) {
                 wp_send_json(static::create_result(false, null, 0, '', array( array( __('Das Status-Objekt konnte nicht gelesen werden.', WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Error ) )));
             }
             $stats = new $this->statsClassName($stats);
         }
         $offset = $_POST['wpvgw_task_offset'] ?? null;
         if (!is_numeric($offset) || intval($offset) <= -1) {
             wp_send_json(static::create_result(false, null, 0, '', array( array( __('Das Offset hat einen ungültigen Wert.', WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Error ) )));
         }
         $offset = (int)$offset;
         $hasMoreSteps = true;
         $messages = array();
         $error_messages = null;
         try {
             for ($i = 1; $i <= $this->iterationsPerStep; $i++) {
                 $numberOfIterations = call_user_func_array($this->runFunction, array( $offset, &$stats, &$error_messages ));
                 $offset += $numberOfIterations;
                 if (is_array($error_messages)) {
                     $hasMoreSteps = false;
                     $messages = $error_messages;
                     break;
                 }
                 if ($numberOfIterations === 0) {
                     $hasMoreSteps = false;
                     $messages = call_user_func($this->endFunction, $stats);
                     break;
                 }
             }
         } catch (Throwable $t) {
             if (WPVGW_Helper::show_debug_info()) {
                 $message = sprintf(__('Es ist ein interner Fehler beim Bearbeiten der Aufgabe aufgetreten: %s', WPVGW_TEXT_DOMAIN), esc_html($t->getMessage()));
             } else {
                 $message = __('Es ist ein interner Fehler beim Bearbeiten der Aufgabe aufgetreten. Fehlerdetails dürfen nur im Debug-Modus angezeigt werden. Bitte kontaktieren Sie ihren Administrator oder die VG-WORT-Plugin-Entwickler.', WPVGW_TEXT_DOMAIN);
             }
             try {
                 if ($stats === null) {
                     $stats = new $this->statsClassName();
                 }
                 $messages = call_user_func($this->endFunction, $stats);
             } catch (Throwable $t2) {
                 $message .= '<br>';
                 if (WPVGW_Helper::show_debug_info()) {
                     $message .= sprintf(__('Es ist ein interner Fehler beim Beenden der Aufgabe aufgetreten: %s', WPVGW_TEXT_DOMAIN), esc_html($t2->getMessage()));
                 } else {
                     $message .= __('Es ist ein interner Fehler beim Beenden der Aufgabe aufgetreten. Fehlerdetails dürfen nur im Debug-Modus angezeigt werden. Bitte kontaktieren Sie ihren Administrator oder die VG-WORT-Plugin-Entwickler.', WPVGW_TEXT_DOMAIN);
                 }
             }
             wp_send_json(static::create_result(false, null, 0, '', array( array( $message, WPVGW_ErrorType::Error ) )));
         }
         wp_send_json(self::create_result($hasMoreSteps, $stats, $offset, sprintf($this->statusTextTemplate, number_format_i18n($offset)), $messages));
     }
     private static function create_result($has_more_steps, WPVGW_LongTaskStats $stats = null, $offset = 1, $status_text = '', $messages = array())
     {
         if ($stats !== null && !is_subclass_of($stats, WPVGW_LongTaskStats::class, false)) {
             throw new TypeError('Type of $stats has to be WPVGW_LongTaskStats.');
         }
         return array( 'has_more_steps' => $has_more_steps, 'stats' => json_encode($stats === null ? null : $stats->to_array_data()), 'offset' => $offset, 'status_text' => $status_text, 'messages' => $messages, );
     }
     public static function get_javascript()
     {
         return array( 'file' => 'views/admin/task-window.js', 'slug' => 'admin-view-task-window', 'dependencies' => array( 'jquery' ), 'localize' => array( 'object_name' => 'ajax_object', 'data' => array( 'nonce' => wp_create_nonce(self::AJAX_NONCE_STRING), 'ajax_url' => admin_url('admin-ajax.php'), 'start_message' => __('Vorgang wird gestartet …', WPVGW_TEXT_DOMAIN), 'aborting_message' => __('Vorgang wird abgebrochen …', WPVGW_TEXT_DOMAIN), 'button_close_text' => __('Schließen', WPVGW_TEXT_DOMAIN), 'button_abort_text' => __('Abbrechen', WPVGW_TEXT_DOMAIN), ) ) );
     }
     public static function render_task_window_html() { ?>
		<div id="wpvgw-task-window-background">
			<div id="wpvgw-task-window">
				<div id="wpvgw-task-window-spinner" class="spinner"></div>
				<div id="wpvgw-task-window-title"><?php _e('Aufwendige Aufgabe', WPVGW_TEXT_DOMAIN) ?></div>
				<div id="wpvgw-task-window-content"></div>
				<div id="wpvgw-task-window-button"></div>
			</div>
		</div>
		<?php
 }
     public function render_auto_run_js() { ?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function () {
			wpvgw_task_window.init(function () {
				wpvgw_task_window.run_task('<?php echo($this->taskId) ?>');
			});
		});
		//]]>
		</script>
		<?php
 }
 } interface WPVGW_LongTaskStats
 {
     public function __construct(array $array_data = null);
     public function to_array_data();
 }
