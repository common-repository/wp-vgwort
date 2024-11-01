<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_Options extends WPVGW_OptionsBase
 {
     private static $allowedPostTypes = 'allowed_post_types';
     private static $removedPostTypes = 'removed_post_types';
     private static $outputFormat = 'output_format';
     private static $lazyLoadMarker = 'lazy_load_marker';
     private static $isFeedOutput = 'is_feed_output';
     private static $tlsOutputFormat = 'tls_output_format';
     private static $isTopOutput = 'is_top_output';
     private static $ampOutputFormat = 'amp_output_format';
     private static $ampTlsOutputFormat = 'amp_tls_output_format';
     private static $defaultServer = 'default_server';
     private static $useTls = 'use_tls';
     private static $showUseTlsWarning = 'show_use_tls_warning';
     private static $apiKey = 'api_key';
     private static $metaName = 'meta_name';
     private static $vgWortMinimumCharacterCount = 'vg_wort_minimum_character_count';
     private static $numberOfMarkersPerPage = 'number_of_markers_per_page';
     private static $removeDataOnUninstall = 'remove_data_on_uninstall';
     private static $exportCsvOutputHeadlines = 'export_csv_output_headlines';
     private static $exportCsvDelimiter = 'export_csv_delimiter';
     private static $exportCsvEnclosure = 'export_csv_enclosure';
     private static $importFromPostDelete = 'import_from_post_delete';
     private static $importFromPostRegex = 'import_from_post_regex';
     private static $importIsAuthorCsv = 'import_is_author_csv';
     private static $importIsOrderDateToday = 'import_is_order_date_today';
     private static $privacyHideWarning = 'privacy_hide_warning';
     private static $showOtherActiveVgWortPluginsWarning = 'show_other_active_vg_wort_plugins_warning';
     private static $showChangedOutputFormatWarning = 'show_changed_output_format_warning';
     private static $operationPostCharacterCountRecalculationsNecessary = 'operations_post_character_count_recalculations_necessary';
     private static $operationMaxExecutionTime = 'operations_max_execution_time';
     private static $operationOldPluginImportNecessary = 'operation_old_plugin_import_necessary';
     private static $doShortcodesForCharacterCountCalculation = 'do_shortcodes_for_character_count_calculation';
     private static $considerExcerptForCharacterCountCalculation = 'consider_excerpt_for_character_count_calculation';
     private static $postViewAutoMarker = 'post_view_auto_marker';
     private static $postViewSetMarkerByDefault = 'post_view_set_marker_by_default';
     private static $postViewSetMarkerForPublishedOnly = 'post_view_set_marker_for_published_only';
     private static $postViewRefreshCharacterCountTimeout = 'post_view_refresh_character_count_timeout';
     private static $postTableViewUseColors = 'post_table_view_use_colors';
     private static $shortcodePostStatsTemplate = 'shortcode_post_stats_template';
     private static $instance;
     private $allowedUserRoles;
     public static function get_instance()
     {
         if (self::$instance === null) {
             self::$instance = new WPVGW_Options();
         }
         return self::$instance;
     }
     private function __construct()
     {
         $this->allowedUserRoles = apply_filters(WPVGW . '_allowed_user_roles', array( 'contributor', 'author', 'editor', 'administrator' ));
     }
     public function init($option_db_slug)
     {
         if ($this->defaultOptions !== null) {
             return;
         }
         $outputFormat = '<img src="%s//%%1$s/%%2$s" width="1" height="1" alt="" class="%s-marker-image" loading="eager" data-no-lazy="1" referrerpolicy="no-referrer-when-downgrade" style="display:none;" />';
         $ampOutputFormat = '<amp-pixel src="%s//%%1$s/%%2$s"></amp-pixel>';
         $this->defaultOptions = array( self::$allowedPostTypes => array( 'post', 'page' ), self::$removedPostTypes => array(), self::$outputFormat => sprintf($outputFormat, 'http:', WPVGW), self::$lazyLoadMarker => false, self::$isFeedOutput => false, self::$tlsOutputFormat => sprintf($outputFormat, 'https:', WPVGW), self::$isTopOutput => false, self::$ampOutputFormat => sprintf($ampOutputFormat, ''), self::$ampTlsOutputFormat => sprintf($ampOutputFormat, 'https:'), self::$defaultServer => 'vg02.met.vgwort.de/na', self::$useTls => false, self::$apiKey => null, self::$showUseTlsWarning => true, self::$metaName => 'wp_vgwortmarke', self::$vgWortMinimumCharacterCount => 1800, self::$numberOfMarkersPerPage => 10, self::$removeDataOnUninstall => false, self::$exportCsvOutputHeadlines => true, self::$exportCsvDelimiter => ';', self::$exportCsvEnclosure => '"', self::$importFromPostDelete => false, self::$importFromPostRegex => '%<img\s[^<>]*?src\s*=\s*"https?://(?:ssl-vg03|vg[0-9]+)\.met\.vgwort\.de/na/[a-z0-9]+"[^<>]*?>%im', self::$importIsAuthorCsv => true, self::$importIsOrderDateToday => true, self::$privacyHideWarning => false, self::$showOtherActiveVgWortPluginsWarning => true, self::$showChangedOutputFormatWarning => true, self::$operationPostCharacterCountRecalculationsNecessary => false, self::$operationOldPluginImportNecessary => false, self::$operationMaxExecutionTime => 300, self::$doShortcodesForCharacterCountCalculation => false, self::$considerExcerptForCharacterCountCalculation => false, self::$postViewAutoMarker => true, self::$postViewSetMarkerByDefault => false, self::$postViewSetMarkerForPublishedOnly => true, self::$postViewRefreshCharacterCountTimeout => 10, self::$postTableViewUseColors => true, self::$shortcodePostStatsTemplate => __('Dieser Text besteht aus %1$s Zeichen und entspricht damit etwa %3$s A4-Seiten oder %2$s Normseiten.', WPVGW_TEXT_DOMAIN), );
         parent::init($option_db_slug);
     }
     protected function load_from_db($option_db_slug)
     {
         return get_option($option_db_slug, array());
     }
     public function store_in_db()
     {
         if ($this->optionsChanged) {
             update_option($this->get_option_db_slug(), $this->options);
         }
     }
     public function get_allowed_user_roles()
     {
         return $this->allowedUserRoles;
     }
     public function set_allowed_post_types(array $value)
     {
         if ($this->options[self::$allowedPostTypes] !== $value) {
             $this->options[self::$allowedPostTypes] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_allowed_post_types()
     {
         return $this->options[self::$allowedPostTypes];
     }
     public function default_allowed_post_types()
     {
         return $this->defaultOptions[self::$allowedPostTypes];
     }
     public function set_removed_post_types(array $value)
     {
         if ($this->options[self::$removedPostTypes] !== $value) {
             $this->options[self::$removedPostTypes] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_removed_post_types()
     {
         return $this->options[self::$removedPostTypes];
     }
     public function default_removed_post_types()
     {
         return $this->defaultOptions[self::$removedPostTypes];
     }
     public function set_default_server($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if ($this->options[self::$defaultServer] !== $value) {
             $this->options[self::$defaultServer] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_default_server()
     {
         return $this->options[self::$defaultServer];
     }
     public function default_default_server()
     {
         return $this->defaultOptions[self::$defaultServer];
     }
     public function set_use_tls($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$useTls] !== $value) {
             $this->options[self::$useTls] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_use_tls()
     {
         return $this->options[self::$useTls];
     }
     public function default_use_tls()
     {
         return $this->defaultOptions[self::$useTls];
     }
     public function set_show_use_tls_warning($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$showUseTlsWarning] !== $value) {
             $this->options[self::$showUseTlsWarning] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_show_use_tls_warning()
     {
         return $this->options[self::$showUseTlsWarning];
     }
     public function default_show_use_tls_warning()
     {
         return $this->defaultOptions[self::$showUseTlsWarning];
     }
     public function set_api_key($value)
     {
         if (!is_string($value) && $value !== null) {
             throw new Exception('Value is not a string or null.');
         }
         if (!WPVGW_MarkersRestRoute::api_key_validator($value)) {
             throw new Exception('Value has not a valid REST API key format.');
         }
         if ($this->options[self::$apiKey] !== $value) {
             $this->options[self::$apiKey] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_api_key()
     {
         return $this->options[self::$apiKey];
     }
     public function default_api_key()
     {
         return $this->defaultOptions[self::$apiKey];
     }
     public function set_meta_name($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         $value = trim($value);
         if ($value === '') {
             throw new Exception('Value must not be empty or whitespaces only.');
         }
         if ($this->options[self::$metaName] !== $value) {
             $this->options[self::$metaName] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_meta_name()
     {
         return $this->options[self::$metaName];
     }
     public function default_meta_name()
     {
         return $this->defaultOptions[self::$metaName];
     }
     public function set_output_format($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if ($this->options[self::$outputFormat] !== $value) {
             $this->options[self::$outputFormat] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_output_format()
     {
         return $this->options[self::$outputFormat];
     }
     public function default_output_format()
     {
         return $this->defaultOptions[self::$outputFormat];
     }
     public function is_changed_output_format()
     {
         return $this->get_output_format() !== $this->default_output_format();
     }
     public function get_lazy_load_output_format()
     {
         return '<div id="wpvgw-marker" data-src="http://%1$s/%2$s" style="display:none;"></div>';
     }
     public function set_lazy_load_marker($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$lazyLoadMarker] !== $value) {
             $this->options[self::$lazyLoadMarker] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_lazy_load_marker()
     {
         return $this->options[self::$lazyLoadMarker];
     }
     public function default_lazy_load_marker()
     {
         return $this->defaultOptions[self::$lazyLoadMarker];
     }
     public function set_is_feed_output($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$isFeedOutput] !== $value) {
             $this->options[self::$isFeedOutput] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_is_feed_output()
     {
         return $this->options[self::$isFeedOutput];
     }
     public function default_is_feed_output()
     {
         return $this->defaultOptions[self::$isFeedOutput];
     }
     public function set_tls_output_format($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if ($this->options[self::$tlsOutputFormat] !== $value) {
             $this->options[self::$tlsOutputFormat] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_tls_output_format()
     {
         return $this->options[self::$tlsOutputFormat];
     }
     public function default_tls_output_format()
     {
         return $this->defaultOptions[self::$tlsOutputFormat];
     }
     public function is_changed_tls_output_format()
     {
         return $this->get_tls_output_format() !== $this->default_tls_output_format();
     }
     public function get_tls_lazy_load_output_format()
     {
         return '<div id="wpvgw-marker" data-src="https://%1$s/%2$s" style="display:none;"></div>';
     }
     public function set_is_top_output($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$isTopOutput] !== $value) {
             $this->options[self::$isTopOutput] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_is_top_output()
     {
         return $this->options[self::$isTopOutput];
     }
     public function default_is_top_output()
     {
         return $this->defaultOptions[self::$isTopOutput];
     }
     public function set_amp_output_format($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if ($this->options[self::$ampOutputFormat] !== $value) {
             $this->options[self::$ampOutputFormat] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_amp_output_format()
     {
         return $this->options[self::$ampOutputFormat];
     }
     public function default_amp_output_format()
     {
         return $this->defaultOptions[self::$ampOutputFormat];
     }
     public function set_amp_tls_output_format($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if ($this->options[self::$ampTlsOutputFormat] !== $value) {
             $this->options[self::$ampTlsOutputFormat] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_amp_tls_output_format()
     {
         return $this->options[self::$ampTlsOutputFormat];
     }
     public function default_amp_tls_output_format()
     {
         return $this->defaultOptions[self::$ampTlsOutputFormat];
     }
     public function set_vg_wort_minimum_character_count($value)
     {
         if (!is_int($value) && $value < 0) {
             throw new Exception('Value is not a non-negative integer.');
         }
         if ($this->options[self::$vgWortMinimumCharacterCount] !== $value) {
             $this->options[self::$vgWortMinimumCharacterCount] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_vg_wort_minimum_character_count()
     {
         return $this->options[self::$vgWortMinimumCharacterCount];
     }
     public function default_vg_wort_minimum_character_count()
     {
         return $this->defaultOptions[self::$vgWortMinimumCharacterCount];
     }
     public function set_number_of_markers_per_page($value)
     {
         if (!is_int($value) && $value < 1) {
             throw new Exception('Value is not an integer greater than 0.');
         }
         if ($this->options[self::$numberOfMarkersPerPage] !== $value) {
             $this->options[self::$numberOfMarkersPerPage] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_number_of_markers_per_page()
     {
         return $this->options[self::$numberOfMarkersPerPage];
     }
     public function default_number_of_markers_per_page()
     {
         return $this->defaultOptions[self::$numberOfMarkersPerPage];
     }
     public function set_remove_data_on_uninstall($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$removeDataOnUninstall] !== $value) {
             $this->options[self::$removeDataOnUninstall] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_remove_data_on_uninstall()
     {
         return $this->options[self::$removeDataOnUninstall];
     }
     public function default_remove_data_on_uninstall()
     {
         return $this->defaultOptions[self::$removeDataOnUninstall];
     }
     public function set_export_csv_output_headlines($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$exportCsvOutputHeadlines] !== $value) {
             $this->options[self::$exportCsvOutputHeadlines] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_export_csv_output_headlines()
     {
         return $this->options[self::$exportCsvOutputHeadlines];
     }
     public function default_export_csv_output_headlines()
     {
         return $this->defaultOptions[self::$exportCsvOutputHeadlines];
     }
     public function get_export_csv_delimiter()
     {
         return $this->default_export_csv_delimiter();
     }
     public function default_export_csv_delimiter()
     {
         return $this->defaultOptions[self::$exportCsvDelimiter];
     }
     public function get_export_csv_enclosure()
     {
         return $this->default_export_csv_enclosure();
     }
     public function default_export_csv_enclosure()
     {
         return $this->defaultOptions[self::$exportCsvEnclosure];
     }
     public function set_import_from_post_delete($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$importFromPostDelete] !== $value) {
             $this->options[self::$importFromPostDelete] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_import_from_post_delete()
     {
         return $this->options[self::$importFromPostDelete];
     }
     public function default_import_from_post_delete()
     {
         return $this->defaultOptions[self::$importFromPostDelete];
     }
     public function set_import_from_post_regex($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if (@preg_match($value, '') === false) {
             throw new Exception('Value has to be a valid Regular Expression.');
         }
         if ($this->options[self::$importFromPostRegex] !== $value) {
             $this->options[self::$importFromPostRegex] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_import_from_post_regex()
     {
         return $this->options[self::$importFromPostRegex];
     }
     public function default_import_from_post_regex()
     {
         return $this->defaultOptions[self::$importFromPostRegex];
     }
     public function set_is_author_csv($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$importIsAuthorCsv] !== $value) {
             $this->options[self::$importIsAuthorCsv] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_is_author_csv()
     {
         return $this->options[self::$importIsAuthorCsv];
     }
     public function default_is_author_csv()
     {
         return $this->defaultOptions[self::$importIsAuthorCsv];
     }
     public function set_import_is_order_date_today($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$importIsOrderDateToday] !== $value) {
             $this->options[self::$importIsOrderDateToday] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_import_is_order_date_today()
     {
         return $this->options[self::$importIsOrderDateToday];
     }
     public function default_import_is_order_date_today()
     {
         return $this->defaultOptions[self::$importIsOrderDateToday];
     }
     public function set_privacy_hide_warning($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$privacyHideWarning] !== $value) {
             $this->options[self::$privacyHideWarning] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_privacy_hide_warning()
     {
         return $this->options[self::$privacyHideWarning];
     }
     public function default_privacy_hide_warning()
     {
         return $this->defaultOptions[self::$privacyHideWarning];
     }
     public function set_show_other_active_vg_wort_plugins_warning($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$showOtherActiveVgWortPluginsWarning] !== $value) {
             $this->options[self::$showOtherActiveVgWortPluginsWarning] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_show_other_active_vg_wort_plugins_warning()
     {
         return $this->options[self::$showOtherActiveVgWortPluginsWarning];
     }
     public function default_show_other_active_vg_wort_plugins_warning()
     {
         return $this->defaultOptions[self::$showOtherActiveVgWortPluginsWarning];
     }
     public function set_show_changed_output_format_warning($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$showChangedOutputFormatWarning] !== $value) {
             $this->options[self::$showChangedOutputFormatWarning] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_show_changed_output_format_warning()
     {
         return $this->options[self::$showChangedOutputFormatWarning];
     }
     public function default_show_changed_output_format_warning()
     {
         return $this->defaultOptions[self::$showChangedOutputFormatWarning];
     }
     public function set_operations_post_character_count_recalculations_necessary($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$operationPostCharacterCountRecalculationsNecessary] !== $value) {
             $this->options[self::$operationPostCharacterCountRecalculationsNecessary] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_operations_post_character_count_recalculations_necessary()
     {
         return $this->options[self::$operationPostCharacterCountRecalculationsNecessary];
     }
     public function default_operations_post_character_count_recalculations_necessary()
     {
         return $this->defaultOptions[self::$operationPostCharacterCountRecalculationsNecessary];
     }
     public function set_operation_old_plugin_import_necessary($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$operationOldPluginImportNecessary] !== $value) {
             $this->options[self::$operationOldPluginImportNecessary] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_operation_old_plugin_import_necessary()
     {
         return $this->options[self::$operationOldPluginImportNecessary];
     }
     public function default_operation_old_plugin_import_necessary()
     {
         return $this->defaultOptions[self::$operationOldPluginImportNecessary];
     }
     public function set_operation_max_execution_time($value)
     {
         if (!is_int($value) && $value < 1) {
             throw new Exception('Value is not a positive integer.');
         }
         if ($this->options[self::$operationMaxExecutionTime] !== $value) {
             $this->options[self::$operationMaxExecutionTime] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_operation_max_execution_time()
     {
         return $this->options[self::$operationMaxExecutionTime];
     }
     public function default_operation_max_execution_time()
     {
         return $this->defaultOptions[self::$operationMaxExecutionTime];
     }
     public function set_do_shortcodes_for_character_count_calculation($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$doShortcodesForCharacterCountCalculation] !== $value) {
             $this->options[self::$doShortcodesForCharacterCountCalculation] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_do_shortcodes_for_character_count_calculation()
     {
         return $this->options[self::$doShortcodesForCharacterCountCalculation];
     }
     public function default_do_shortcodes_for_character_count_calculation()
     {
         return $this->defaultOptions[self::$doShortcodesForCharacterCountCalculation];
     }
     public function set_consider_excerpt_for_character_count_calculation($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$considerExcerptForCharacterCountCalculation] !== $value) {
             $this->options[self::$considerExcerptForCharacterCountCalculation] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_consider_excerpt_for_character_count_calculation()
     {
         return $this->options[self::$considerExcerptForCharacterCountCalculation];
     }
     public function default_consider_excerpt_for_character_count_calculation()
     {
         return $this->defaultOptions[self::$considerExcerptForCharacterCountCalculation];
     }
     public function set_post_view_auto_marker($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$postViewAutoMarker] !== $value) {
             $this->options[self::$postViewAutoMarker] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_post_view_auto_marker()
     {
         return $this->options[self::$postViewAutoMarker];
     }
     public function default_post_view_auto_marker()
     {
         return $this->defaultOptions[self::$postViewAutoMarker];
     }
     public function set_post_view_set_marker_by_default($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$postViewSetMarkerByDefault] !== $value) {
             $this->options[self::$postViewSetMarkerByDefault] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_post_view_set_marker_by_default()
     {
         return $this->options[self::$postViewSetMarkerByDefault];
     }
     public function default_post_view_set_marker_by_default()
     {
         return $this->defaultOptions[self::$postViewSetMarkerByDefault];
     }
     public function set_post_view_set_marker_for_published_only($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$postViewSetMarkerForPublishedOnly] !== $value) {
             $this->options[self::$postViewSetMarkerForPublishedOnly] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_post_view_set_marker_for_published_only()
     {
         return $this->options[self::$postViewSetMarkerForPublishedOnly];
     }
     public function default_post_view_set_marker_for_published_only()
     {
         return $this->defaultOptions[self::$postViewSetMarkerForPublishedOnly];
     }
     public function set_post_view_refresh_character_count_timeout($value)
     {
         if (!is_int($value)) {
             throw new Exception('Value is not an integer.');
         }
         if (!($value === -1 || ($value >= 4 && $value <= 3600))) {
             throw new Exception('Value is out of range. Valid range is from 4 to 3600 or -1.');
         }
         if ($this->options[self::$postViewRefreshCharacterCountTimeout] !== $value) {
             $this->options[self::$postViewRefreshCharacterCountTimeout] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_post_view_refresh_character_count_timeout()
     {
         return $this->options[self::$postViewRefreshCharacterCountTimeout];
     }
     public function default_post_view_refresh_character_count_timeout()
     {
         return $this->defaultOptions[self::$postViewRefreshCharacterCountTimeout];
     }
     public function set_post_table_view_use_colors($value)
     {
         if (!is_bool($value)) {
             throw new Exception('Value is not a bool.');
         }
         if ($this->options[self::$postTableViewUseColors] !== $value) {
             $this->options[self::$postTableViewUseColors] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_post_table_view_use_colors()
     {
         return $this->options[self::$postTableViewUseColors];
     }
     public function default_post_table_view_use_colors()
     {
         return $this->defaultOptions[self::$postTableViewUseColors];
     }
     public function set_shortcode_post_stats_template($value)
     {
         if (!is_string($value)) {
             throw new Exception('Value is not a string.');
         }
         if ($this->options[self::$shortcodePostStatsTemplate] !== $value) {
             $this->options[self::$shortcodePostStatsTemplate] = $value;
             $this->optionsChanged = true;
         }
     }
     public function get_shortcode_post_stats_template()
     {
         return $this->options[self::$shortcodePostStatsTemplate];
     }
     public function default_shortcode_post_stats_template()
     {
         return $this->defaultOptions[self::$shortcodePostStatsTemplate];
     }
 }
