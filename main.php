<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_Main
 {
     private $options;
     protected $userOptions;
     private $versionOptionName;
     private $optionsName;
     private $userOptionsName;
     private $tableSlug;
     private $markersTableName;
     private $postsExtrasTableName;
     private $adminViewsManager;
     private $postView;
     private $postTableView;
     private $markersManager;
     private $postsExtras;
     private $cache;
     private static $instance;
     private $frontendDisplayFilterPriority = 1800;
     public static function get_instance()
     {
         if (null === self::$instance) {
             self::$instance = new self;
         }
         return self::$instance;
     }
     private function __construct()
     {
         $this->versionOptionName = WPVGW . '_version';
         $this->optionsName = WPVGW . '_options_v1';
         $this->userOptionsName = WPVGW . '_user_options_v1';
         $this->tableSlug = WPVGW;
         add_action('init', array( $this, 'init' ));
         register_activation_hook(WPVGW_PLUGIN_PATH . 'wp-vgwort.php', array( $this, 'on_activation' ));
         register_deactivation_hook(WPVGW_PLUGIN_PATH . 'wp-vgwort.php', array( $this, 'on_deactivation' ));
     }
     public function init()
     {
         load_plugin_textdomain(WPVGW_TEXT_DOMAIN, false, WPVGW_PLUGIN_PATH_RELATIVE . '/languages');
         add_action('wp_loaded', array( $this, 'on_wordpress_loaded' ));
         if (!is_admin()) {
             add_action('wp_enqueue_scripts', array( $this, 'on_enqueue_front_css_and_scripts' ));
         }
         if (is_admin()) {
             add_action('admin_init', array( $this, 'on_admin_init' ));
             add_action('admin_notices', array( $this, 'on_admin_notice' ));
             add_action('current_screen', array( $this, 'on_current_screen' ));
             add_action('admin_enqueue_scripts', array( $this, 'on_enqueue_admin_css_and_scripts' ));
             add_action('admin_menu', array( $this, 'on_add_plugin_admin_menu' ));
             add_action('save_post', array( $this, 'on_post_saved' ));
             add_action('delete_post', array( $this, 'on_post_deleted' ));
             add_action('edit_attachment', array( $this, 'on_post_saved' ));
             add_action('delete_attachment', array( $this, 'on_post_deleted' ));
             add_action('shutdown', array( $this, 'on_deinit' ));
             add_filter('plugin_action_links_' . WPVGW_PLUGIN_BASE_NAME, array( $this, 'on_plugin_action_links' ), 10, 4);
         }
     }
     private function create_markers_table_name()
     {
         global $wpdb;
         return $wpdb->prefix . $this->tableSlug . '_markers';
     }
     private function create_posts_extras_table_name()
     {
         global $wpdb;
         return $wpdb->prefix . $this->tableSlug . '_posts_extras';
     }
     public function on_wordpress_loaded()
     {
         $this->markersTableName = $this->create_markers_table_name();
         $this->postsExtrasTableName = $this->create_posts_extras_table_name();
         $this->options = WPVGW_Options::get_instance();
         $this->options->init($this->optionsName);
         $this->userOptions = WPVGW_UserOptions::get_instance();
         $this->userOptions->init($this->userOptionsName);
         $this->markersManager = new WPVGW_MarkersManager($this->markersTableName, $this->options->get_allowed_user_roles(), $this->options->get_allowed_post_types(), $this->options->get_removed_post_types(), $this->options->get_do_shortcodes_for_character_count_calculation(), $this->options->get_consider_excerpt_for_character_count_calculation());
         $this->postsExtras = new WPVGW_PostsExtras($this->postsExtrasTableName, $this->markersManager);
         $this->cache = new WPVGW_Cache($this->markersManager, $this->postsExtras);
         WPVGW_Shortcodes::get_instance()->init($this->markersManager, $this->cache, $this->options->get_shortcode_post_stats_template());
         if (is_admin()) {
             $this->adminViewsManager = new WPVGW_AdminViewsManger($this->markersManager, $this->postsExtras, $this->options, $this->userOptions);
             $this->postView = new WPVGW_PostView($this->markersManager, $this->postsExtras, $this->options, $this->userOptions);
             $this->postTableView = new WPVGW_PostTableView($this->markersManager, $this->postsExtras, $this->options, $this->userOptions);
             WPVGW_LongTask::set_max_execution_time($this->options->get_operation_max_execution_time());
         } else {
             if ($this->options->get_is_top_output() && !$this->is_wp_amp_plugin_active()) {
                 add_filter('the_content', array( $this, 'on_display_marker_in_content' ));
             } else {
                 add_action('wp_footer', array( $this, 'on_display_marker' ), $this->frontendDisplayFilterPriority);
                 add_action('amphtml_footer_bottom', array( $this, 'on_display_marker_amp' ), $this->frontendDisplayFilterPriority);
                 add_action('amp_post_template_footer', array( $this, 'on_display_marker_amp' ), $this->frontendDisplayFilterPriority);
             }
             if ($this->options->get_is_feed_output()) {
                 add_filter('the_content_feed', array( $this, 'on_display_marker_feed' ));
             }
         }
         add_action('rest_api_init', function () {
             ( new WPVGW_MarkersRestRoute($this->markersManager, $this->postsExtras, $this->options, $this->userOptions) )->register_routes();
         });
     }
     public function on_deinit()
     {
         if ($this->options !== null) {
             $this->options->set_allowed_post_types($this->markersManager->get_allowed_post_types());
             $this->options->set_removed_post_types($this->markersManager->get_removed_post_types());
             $this->options->store_in_db();
             $this->userOptions->store_in_db();
         }
     }
     public function on_admin_init()
     {
         $this->upgrade_plugin();
         if (WPVGW_Advanced_Custom_Fields_Plugin_Extension::is_active()) {
             new WPVGW_Advanced_Custom_Fields_Plugin_Extension();
         }
     }
     private function install_plugin()
     {
         global $wpdb;
         $sql = "CREATE TABLE IF NOT EXISTS $this->markersTableName (
					id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					post_id bigint(20) unsigned DEFAULT NULL,
					user_id bigint(20) unsigned DEFAULT NULL,
					public_marker varchar(100) NOT NULL,
					private_marker varchar(100) DEFAULT NULL,
					server varchar(255) NOT NULL,
					order_date date DEFAULT NULL,
					is_marker_disabled tinyint(1) unsigned NOT NULL DEFAULT '0',
					is_marker_blocked tinyint(1) unsigned NOT NULL DEFAULT '0',
					is_post_deleted tinyint(1) unsigned NOT NULL DEFAULT '0',
					deleted_post_title text DEFAULT NULL,
					PRIMARY KEY (id),
					UNIQUE KEY public_marker (public_marker),
					UNIQUE KEY post_id (post_id),
					UNIQUE KEY private_marker (private_marker),
					KEY user_id (user_id),
					KEY order_date (order_date)
				);";
         $wpdb->query($sql);
         $sql = "CREATE TABLE IF NOT EXISTS $this->postsExtrasTableName (
					post_id bigint(20) unsigned NOT NULL,
					character_count bigint(20) unsigned NOT NULL,
					is_auto_marker_disabled tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY (post_id),
					KEY character_count (character_count)
				);";
         $wpdb->query($sql);
     }
     private function uninstall_plugin()
     {
         global $wpdb;
         $markersTableName = $this->create_markers_table_name();
         $postsExtrasTableName = $this->create_posts_extras_table_name();
         $wpdb->query("DROP TABLE {$markersTableName}");
         $wpdb->query("DROP TABLE {$postsExtrasTableName}");
         delete_option($this->versionOptionName);
         delete_option($this->optionsName);
     }
     private function upgrade_plugin()
     {
         $oldVersion = get_option($this->versionOptionName, null);
         if ($oldVersion === WPVGW_VERSION) {
             return;
         }
         if ($oldVersion === null) {
             $isVersion100 = (get_option('wp_cpt', false) !== false || get_option('wp_vgwort_options', false) !== false || get_option('wp_vgwortmetaname', false) !== false);
             if ($isVersion100) {
                 $oldVersion = '1.0.0';
             } else {
                 $this->install_plugin();
                 $oldVersion = WPVGW_VERSION;
             }
         }
         global $wpdb;
         if (version_compare($oldVersion, '1.0.0', '<=')) {
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "CREATE TABLE IF NOT EXISTS $markersTableName (
						id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						post_id bigint(20) unsigned DEFAULT NULL,
						user_id bigint(20) unsigned DEFAULT NULL,
						public_marker varchar(100) NOT NULL,
						private_marker varchar(100) DEFAULT NULL,
						server varchar(255) NOT NULL,
						is_marker_disabled tinyint(1) unsigned NOT NULL DEFAULT '0',
						is_post_deleted tinyint(1) unsigned NOT NULL DEFAULT '0',
						deleted_post_title text DEFAULT NULL,
						PRIMARY KEY (id),
						UNIQUE KEY public_marker (public_marker),
						UNIQUE KEY post_id (post_id),
						UNIQUE KEY private_marker (private_marker),
						KEY user_id (user_id)
					);";
             $wpdb->query($sql);
             $postsExtrasTableName = $wpdb->prefix . 'wpvgw_posts_extras';
             $sql = "CREATE TABLE IF NOT EXISTS $postsExtrasTableName (
						post_id bigint(20) unsigned NOT NULL,
						character_count bigint(20) unsigned NOT NULL,
						PRIMARY KEY (post_id),
						KEY character_count (character_count)
					);";
             $wpdb->query($sql);
             $oldAllowedPostTypes = get_option('wp_cpt', array());
             $this->markersManager->set_allowed_post_types(array_unique(array_merge($oldAllowedPostTypes, array( 'post', 'page' ))));
             $metaName = get_option('wp_vgwortmetaname', 'wp_vgwortmarke');
             $this->options->set_meta_name($metaName === false || $metaName === '' ? 'wp_vgwortmarke' : $metaName);
             $this->options->set_operations_post_character_count_recalculations_necessary(true);
             $this->options->set_operation_old_plugin_import_necessary(true);
         }
         if (version_compare($oldVersion, '3.1.1', '<')) {
             try {
                 $this->options->set_meta_name($this->options->get_meta_name());
             } catch (Exception $e) {
                 $this->options->set_meta_name('wp_vgwortmarke');
             }
         }
         if (version_compare($oldVersion, '3.4.5', '<')) {
             if ($this->options->get_import_from_post_regex() === '%<img.*?src\s*=\s*"http://vg[0-9]+\.met\.vgwort.de/na/[a-z0-9]+".*?>%si') {
                 $this->options->set_import_from_post_regex('%<img\s[^<>]*?src\s*=\s*"http://vg[0-9]+\.met\.vgwort\.de/na/[a-z0-9]+"[^<>]*?>%im');
             }
         }
         if (version_compare($oldVersion, '3.9.0', '<')) {
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "ALTER TABLE $markersTableName CHANGE public_marker public_marker VARCHAR(100) NOT NULL, CHANGE private_marker private_marker VARCHAR(100) NULL DEFAULT NULL;";
             $wpdb->query($sql);
         }
         if (version_compare($oldVersion, '3.12.0', '<')) {
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "ALTER TABLE $markersTableName ADD order_date DATE NULL DEFAULT NULL AFTER server, ADD INDEX (order_date);";
             $wpdb->query($sql);
         }
         if (version_compare($oldVersion, '3.17.2', '<')) {
             if ($this->options->get_import_from_post_regex() === '%<img\s[^<>]*?src\s*=\s*"http://vg[0-9]+\.met\.vgwort\.de/na/[a-z0-9]+"[^<>]*?>%im') {
                 $this->options->set_import_from_post_regex('%<img\s[^<>]*?src\s*=\s*"https?://(?:ssl-vg03|vg[0-9]+)\.met\.vgwort\.de/na/[a-z0-9]+"[^<>]*?>%im');
             }
         }
         if (version_compare($oldVersion, '3.19.0', '<')) {
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "UPDATE $markersTableName SET user_id = NULL WHERE user_id IS NOT NULL;";
             $wpdb->query($sql);
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "UPDATE $markersTableName SET is_marker_disabled = 0 WHERE post_id IS NULL;";
             $wpdb->query($sql);
             $postsExtrasTableName = $wpdb->prefix . 'wpvgw_posts_extras';
             $sql = "ALTER TABLE $postsExtrasTableName ADD is_auto_marker_disabled tinyint(1) UNSIGNED NOT NULL DEFAULT '0'  AFTER character_count;";
             $wpdb->query($sql);
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "ALTER TABLE $markersTableName ADD is_marker_blocked tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER is_marker_disabled;";
             $wpdb->query($sql);
         }
         if (version_compare($oldVersion, '3.19.1', '<')) {
             $suppressDbErrors = $wpdb->suppress_errors(true);
             $postsExtrasTableName = $wpdb->prefix . 'wpvgw_posts_extras';
             $sql = "ALTER TABLE $postsExtrasTableName ADD is_auto_marker_disabled tinyint(1) UNSIGNED NOT NULL DEFAULT '0'  AFTER character_count;";
             $wpdb->query($sql);
             $markersTableName = $wpdb->prefix . 'wpvgw_markers';
             $sql = "ALTER TABLE $markersTableName ADD is_marker_blocked tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER is_marker_disabled;";
             $wpdb->query($sql);
             $wpdb->suppress_errors($suppressDbErrors);
         }
         if (version_compare($oldVersion, '3.22.1', '<')) {
             $this->options->set_privacy_hide_warning(false);
         }
         if (version_compare($oldVersion, '3.22.3', '<')) {
             if ($this->options->get_output_format() === '<img src="http://%1$s/%2$s" width="1" height="1" alt="" style="display:none;" />') {
                 $this->options->set_output_format('<img src="http://%1$s/%2$s" width="1" height="1" alt="" loading="eager" data-no-lazy="1" style="display:none;" />');
             }
             if ($this->options->get_tls_output_format() === '<img src="https://%1$s/%2$s" width="1" height="1" alt="" style="display:none;" />') {
                 $this->options->set_tls_output_format('<img src="https://%1$s/%2$s" width="1" height="1" alt="" loading="eager" data-no-lazy="1" style="display:none;" />');
             }
         }
         if (version_compare($oldVersion, '3.22.6', '<')) {
             if ($this->options->get_output_format() === '<img src="http://%1$s/%2$s" width="1" height="1" alt="" loading="eager" data-no-lazy="1" style="display:none;" />') {
                 $this->options->set_output_format('<img src="http://%1$s/%2$s" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" style="display:none" />');
             }
             if ($this->options->get_tls_output_format() === '<img src="https://%1$s/%2$s" width="1" height="1" alt="" loading="eager" data-no-lazy="1" style="display:none;" />') {
                 $this->options->set_tls_output_format('<img src="https://%1$s/%2$s" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" style="display:none" />');
             }
         }
         if (version_compare($oldVersion, '3.23.0', '<')) {
             $this->options->set_operations_post_character_count_recalculations_necessary(true);
         }
         if (version_compare($oldVersion, '3.25.0', '<')) {
             if ($this->options->get_output_format() === '<img src="http://%1$s/%2$s" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" style="display:none" />') {
                 $this->options->set_output_format('<img src="http://%1$s/%2$s" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" referrerpolicy="no-referrer-when-downgrade" style="display:none;" />');
             }
             if ($this->options->get_tls_output_format() === '<img src="https://%1$s/%2$s" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" style="display:none" />') {
                 $this->options->set_tls_output_format('<img src="https://%1$s/%2$s" width="1" height="1" alt="" class="wpvgw-marker-image" loading="eager" data-no-lazy="1" referrerpolicy="no-referrer-when-downgrade" style="display:none;" />');
             }
         }
         update_option($this->versionOptionName, WPVGW_VERSION);
     }
     public function on_activation($sitewide)
     {
         if (!current_user_can('activate_plugins')) {
             return;
         }
         $options = WPVGW_Options::get_instance();
         $options->init($this->optionsName);
         $options->set_operations_post_character_count_recalculations_necessary(true);
         $options->store_in_db();
     }
     public function on_deactivation($sitewide)
     {
         if (!current_user_can('activate_plugins')) {
             return;
         }
         if (!$sitewide) {
             if (get_option($this->versionOptionName, null) !== null && $this->options->get_remove_data_on_uninstall()) {
                 $this->uninstall_plugin();
             }
         }
     }
     public function on_plugin_action_links($actions, $plugin_file, $plugin_data, $context)
     {
         $link = sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url()), __('Einstellungen', WPVGW_TEXT_DOMAIN));
         if (is_array($actions)) {
             $actions[] = $link;
         } else {
             $actions = $link;
         }
         return $actions;
     }
     public function on_admin_notice()
     {
         if (!current_user_can('manage_options')) {
             return;
         }
         $this->render_other_vg_wort_plugins_enabled_notice();
         $this->render_operations_post_character_count_recalculations_notice();
         $this->render_operations_old_plugin_import_necessary_notice();
         $this->render_data_privacy_warning_notice();
         $this->render_use_tls_warning_notice();
         $this->render_changed_output_format_notice();
     }
     private function render_data_privacy_warning_notice()
     {
         if (!$this->options->get_privacy_hide_warning()) {
             WPVGW_Helper::render_admin_message(sprintf(__('Der Datenschutz-Hinweis der VG WORT sollte in die Website eingefügt werden. %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_DataPrivacyAdminView::get_slug_static())), __('Datenschutz-Hinweis hier zur Kenntnis nehmen.', WPVGW_TEXT_DOMAIN))), WPVGW_ErrorType::Error, false);
         }
     }
     private function render_use_tls_warning_notice()
     {
         if (!$this->options->get_show_use_tls_warning()) {
             return;
         }
         $configurationAdminViewUrl = sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_ConfigurationAdminView::get_slug_static())), __('HTTP(S) für Zählmarken hier einstellen.', WPVGW_TEXT_DOMAIN));
         $helpTlsLink = sprintf('<a href="https://prosodia.de/prosodia-vgw-os/transportverschluesselung-tls-ssl-aktivieren/">%s</a>', __('Anleitung', WPVGW_TEXT_DOMAIN));
         if (is_ssl() && !$this->options->get_use_tls()) {
             WPVGW_Helper::render_admin_message(sprintf(__('Die WordPress-Website scheint über HTTPS (verschlüsselt) ausgeliefert zu werden – die Zählmarken aber unverschlüsselt. Die Zählmarken müssen daher auch über HTTPS ausgeliefert werden (siehe %s). %s', WPVGW_TEXT_DOMAIN), $helpTlsLink, $configurationAdminViewUrl), WPVGW_ErrorType::Error, false);
         } elseif (!is_ssl() && $this->options->get_use_tls()) {
             WPVGW_Helper::render_admin_message(sprintf(__('Die WordPress-Website scheint über HTTP (unverschlüsselt) ausgeliefert zu werden – die Zählmarken aber verschlüsselt. Die Zählmarken sollten daher auch über HTTP ausgeliefert werden (siehe %s). %s', WPVGW_TEXT_DOMAIN), $helpTlsLink, $configurationAdminViewUrl), WPVGW_ErrorType::Error, false);
         }
     }
     private function render_operations_post_character_count_recalculations_notice()
     {
         if ($this->options->get_operations_post_character_count_recalculations_necessary()) {
             WPVGW_Helper::render_admin_message(sprintf(__('Die Zeichenanzahlen der Seiten müssen neuberechnet werden. %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_OperationsAdminView::get_slug_static())), __('Zeichenanzahl hier neuberechnen.', WPVGW_TEXT_DOMAIN))), WPVGW_ErrorType::Error, false);
         }
     }
     private function render_operations_old_plugin_import_necessary_notice()
     {
         if ($this->options->get_operation_old_plugin_import_necessary()) {
             WPVGW_Helper::render_admin_message(sprintf(__('Die Zählmarken aus einer früheren Version des Plugins sollten importiert werden, da sie sonst fehlen. %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_OperationsAdminView::get_slug_static())), __('Zählmarken hier importieren.', WPVGW_TEXT_DOMAIN))), WPVGW_ErrorType::Warning, false);
         }
     }
     private function render_other_vg_wort_plugins_enabled_notice()
     {
         if (!$this->options->get_show_other_active_vg_wort_plugins_warning()) {
             return;
         }
         $activeVgWortPlugins = WPVGW_Helper::get_other_active_vg_wort_plugins();
         if ($activeVgWortPlugins === array()) {
             return;
         }
         $otherActivePluginsText = '';
         $separator = '';
         foreach ($activeVgWortPlugins as $activeVgWortPlugin) {
             $pluginData = get_plugin_data(WP_PLUGIN_DIR . '/' . $activeVgWortPlugin, false, true);
             $otherActivePluginsText .= $separator . sprintf(__('„%s“ (%s)', WPVGW_TEXT_DOMAIN), $pluginData['Name'], $pluginData['Version']);
             $separator = __(', ', WPVGW_TEXT_DOMAIN);
         }
         WPVGW_Helper::render_admin_message(sprintf(__('Es sind folgende, andere Plugins zur Integration von Zählmarken der VG WORT aktiviert: %s. Diese sollten besser deaktiviert werden, um Zählmarken nicht mehrfach auszugeben. %s', WPVGW_TEXT_DOMAIN), esc_html($otherActivePluginsText), sprintf('<a href="%s">%s</a>', esc_attr(admin_url('plugins.php')), __('Plugins hier deaktivieren.', WPVGW_TEXT_DOMAIN))), WPVGW_ErrorType::Error, false);
     }
     private function render_changed_output_format_notice()
     {
         if (!$this->options->get_show_changed_output_format_warning()) {
             return;
         }
         if (!$this->options->is_changed_tls_output_format() && !$this->options->is_changed_output_format()) {
             return;
         }
         WPVGW_Helper::render_admin_message(sprintf(__('Das angegebene Zählmarken-Ausgabe-Format stimmt nicht mit dem Standardwert überein. Bitte prüfen Sie genau, ob dies gewollt ist. %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_ConfigurationAdminView::get_slug_static())), __('Zählmarken-Ausgabe-Format hier einstellen.', WPVGW_TEXT_DOMAIN))), WPVGW_ErrorType::Warning, false);
     }
     public function on_current_screen($current_screen)
     {
         if ($current_screen === null) {
             return;
         }
         $postType = get_post_type_object($current_screen->post_type);
         $adminViewSlug = WPVGW_Helper::remove_prefix($current_screen->id, get_plugin_page_hookname('', WPVGW . '-' . WPVGW_AdminViewsManger::get_default_view_slug()) . WPVGW . '-', $adminViewSlugFound);
         if (!$adminViewSlugFound && $current_screen->id === 'toplevel_page_' . WPVGW . '-' . WPVGW_AdminViewsManger::get_default_view_slug()) {
             $adminViewSlug = WPVGW_AdminViewsManger::get_default_view_slug();
             $adminViewSlugFound = true;
         }
         if ($adminViewSlugFound) {
             $this->adminViewsManager->init($adminViewSlug);
             $this->adminViewsManager->get_current_view()->do_action();
         }
         if ($this->markersManager !== null && $postType !== null && $this->markersManager->is_post_type_allowed($postType->name)) {
             if ($current_screen->base === 'post') {
                 $this->postView->set_post_type($postType);
                 $this->postView->init();
             }
             if ($current_screen->base === 'edit' || $current_screen->base === 'upload') {
                 $this->postTableView->set_post_type($postType);
                 $this->postTableView->set_screen_id($current_screen->id);
                 $this->postTableView->init();
             }
         }
     }
     public function on_enqueue_front_css_and_scripts()
     {
         if ($this->options !== null && $this->options->get_lazy_load_marker()) {
             wp_register_script(WPVGW . '-lazy-load-marker', WPVGW_PLUGIN_URL . '/js/views/front/lazy-load-marker.js', array( 'jquery' ), WPVGW_VERSION, true);
             wp_enqueue_script(WPVGW . '-lazy-load-marker');
         }
     }
     public function on_enqueue_admin_css_and_scripts()
     {
         $styleSlug = WPVGW . '-admin';
         wp_register_style($styleSlug, WPVGW_PLUGIN_URL . '/css/admin.css', array(), WPVGW_VERSION);
         wp_enqueue_style($styleSlug);
         $javaScripts = array( array( 'file' => 'main.js', 'slug' => 'main', 'dependencies' => array( 'jquery' ) ) );
         if ($this->adminViewsManager->is_init()) {
             $javaScripts = array_merge($javaScripts, $this->adminViewsManager->get_current_view()->get_javascripts());
         }
         if ($this->postView->is_init()) {
             $javaScripts = array_merge($javaScripts, $this->postView->get_javascripts());
         }
         if ($this->postTableView->is_init()) {
             $javaScripts = array_merge($javaScripts, $this->postTableView->get_javascripts());
         }
         foreach ($javaScripts as $javaScript) {
             $jsSlug = WPVGW . '-' . $javaScript['slug'];
             wp_register_script($jsSlug, WPVGW_PLUGIN_URL . '/js/' . $javaScript['file'], $javaScript['dependencies'], WPVGW_VERSION, true);
             wp_enqueue_script($jsSlug);
             if (array_key_exists('localize', $javaScript)) {
                 wp_localize_script($jsSlug, WPVGW . '_' . $javaScript['localize']['object_name'], $javaScript['localize']['data']);
             }
         }
     }
     public function on_add_plugin_admin_menu()
     {
         $adminViews = $this->adminViewsManager->get_views();
         $adminDefaultViewSlug = $this->adminViewsManager::get_default_view_slug();
         add_menu_page(__('Prosodia VGW OS', WPVGW_TEXT_DOMAIN), __('Prosodia VGW OS', WPVGW_TEXT_DOMAIN), 'manage_options', WPVGW . '-' . $adminDefaultViewSlug, array( $adminViews[$adminDefaultViewSlug], 'render' ), 'dashicons-admin-wpvgw', '80.00002');
         foreach ($this->adminViewsManager->get_views() as $viewSlug => $view) {
             add_submenu_page(WPVGW . '-' . $adminDefaultViewSlug, $view->get_long_name(), $view->get_short_name(), 'manage_options', WPVGW . '-' . $view->get_slug(), array( $view, 'render' ));
         }
     }
     public function on_post_saved($post_id)
     {
         if (wp_is_post_revision($post_id) !== false || wp_is_post_autosave($post_id) !== false) {
             return;
         }
         $post = get_post($post_id);
         if ($post === null) {
             return;
         }
         if ($this->postView === null || $this->markersManager === null || $this->postsExtras === null) {
             if (WPVGW_Helper::show_debug_info()) {
                 throw new Exception('postView or markersManager or postsExtras must not be null.');
             }
             return;
         }
         if ($this->postView->is_init()) {
             $this->postView->do_action($post);
         }
         if ($this->markersManager->is_post_type_allowed($post->post_type)) {
             $this->postsExtras->recalculate_post_character_count_in_db($post);
         }
     }
     public function on_post_deleted($post_id)
     {
         $this->markersManager->update_marker_in_db($post_id, 'post_id', array( 'is_post_deleted' => true, 'deleted_post_title' => get_the_title($post_id) ));
         $this->postsExtras->delete_post_extra($post_id);
     }
     private function is_amp()
     {
         return $this->is_wp_amp_plugin_active() || $this->is_amp_plugin_active();
     }
     private function is_wp_amp_plugin_active()
     {
         if (class_exists('AMPHTML') && method_exists('AMPHTML', 'instance')) {
             $ampHtml = AMPHTML::instance();
             if (method_exists($ampHtml, 'is_amp')) {
                 return $ampHtml->is_amp();
             }
         }
         return false;
     }
     private function is_amp_plugin_active()
     {
         if (function_exists('amp_is_request')) {
             return amp_is_request();
         }
         if (function_exists('is_amp_endpoint')) {
             return is_amp_endpoint();
         }
         return false;
     }
     public function on_display_marker()
     {
         echo($this->get_marker($this->is_amp()));
         echo($this->get_attachment_markers($this->is_amp()));
     }
     public function on_display_marker_in_content($content)
     {
         $marker = $this->get_marker($this->is_amp());
         $attachedMarkers = $this->get_attachment_markers($this->is_amp());
         if ($marker === '' && $attachedMarkers === '') {
             return $content;
         }
         return "{$marker}{$attachedMarkers} {$content}";
     }
     public function on_display_marker_amp()
     {
         if (!$this->is_amp()) {
             return;
         }
         echo($this->get_marker(true));
     }
     public function on_display_marker_feed($content)
     {
         return $content . $this->get_marker(false, true) . "\n";
     }
     public function get_marker($is_amp = false, $is_feed = false, WP_Post $post = null, $disable_lazy_loading = false)
     {
         $marker = $this->get_marker_data($post);
         if ($marker === false || $marker['is_marker_disabled']) {
             return '';
         }
         $useTls = $this->options->get_use_tls();
         $lazyLoadMarker = !$disable_lazy_loading && $this->options->get_lazy_load_marker();
         $defaultOutputString = $useTls ? $this->options->get_tls_output_format() : $this->options->get_output_format();
         $outputString = null;
         if ($is_amp && !$is_feed) {
             $outputString = $useTls ? $this->options->get_amp_tls_output_format() : $this->options->get_amp_output_format();
         } elseif ($lazyLoadMarker && !$is_feed) {
             $outputString = ('<noscript>' . $defaultOutputString . '</noscript>') . ($useTls ? $this->options->get_tls_lazy_load_output_format() : $this->options->get_lazy_load_output_format());
         } else {
             $outputString = $defaultOutputString;
         }
         return apply_filters('wp_vgwort_frontend_display', sprintf($outputString, esc_attr($marker['server']), esc_attr($marker['public_marker'])), $marker, $useTls, $lazyLoadMarker, $is_amp, $post);
     }
     private function get_attachment_markers($is_amp = false)
     {
         if ($is_amp) {
             return '';
         }
         global $wp_the_query;
         $post = $wp_the_query->post;
         if ($post === null || !$this->markersManager->is_post_type_allowed('attachment')) {
             return '';
         }
         $medias = get_attached_media('', $post);
         $mediaUrlsAndMarkerStrings = array();
         foreach ($medias as $media) {
             $mediaUrl = wp_get_attachment_url($media->ID);
             if (preg_match('/<a\s+?\S*?\s?href\s*?=\s*?"' . preg_quote($mediaUrl, '/') . '".*?>/i', $post->post_content) === 1) {
                 $markerString = $this->get_marker(false, false, $media, true);
                 if (!WPVGW_Helper::is_null_or_empty($markerString)) {
                     $mediaUrlsAndMarkerStrings[] = [ 'url' => $mediaUrl, 'marker-string' => $markerString ];
                 }
             }
         }
         $mediaUrlsAndMarkerStringsJs = implode(', ', array_map(static function ($mediaUrlAndMarkerString) {
             return '["' . addslashes($mediaUrlAndMarkerString['url']) . '", "' . addslashes($mediaUrlAndMarkerString['marker-string']) . '"]';
         }, $mediaUrlsAndMarkerStrings));
         return sprintf(
             <<<HTML
<script type="text/javascript">
//<![CDATA[
document.addEventListener("DOMContentLoaded", function () {
	const mediaUrlsAndMarkerStrings = [$mediaUrlsAndMarkerStringsJs];
	for (let i = 0, l = mediaUrlsAndMarkerStrings.length; i < l; i++) {
		const url = mediaUrlsAndMarkerStrings[i][0];
		const markerString = mediaUrlsAndMarkerStrings[i][1];

		const aElement = document.querySelector("a[href='" + url + "']");
		if (aElement === null)
			continue;

		aElement.setAttribute("target", "_blank");
		aElement.addEventListener("click", function c() {
			aElement.insertAdjacentHTML("afterend", markerString);
			aElement.removeEventListener("click", c);
		}, false);
	}
});
//]]>
</script>
HTML
         );
     }
     public function get_marker_data(WP_Post $post = null)
     {
         global $wp_the_query;
         $denyGetMarkerData = ($wp_the_query === null || !($wp_the_query->is_single || $wp_the_query->is_page || $wp_the_query->is_feed));
         $denyGetMarkerData = apply_filters(WPVGW . '_deny_get_marker_data', $denyGetMarkerData, $wp_the_query);
         if ($denyGetMarkerData) {
             return false;
         }
         if ($post === null) {
             $post = $wp_the_query->post;
         }
         if ($post === null) {
             return false;
         }
         if (!$this->markersManager->is_post_type_allowed($post->post_type) || !$this->markersManager->is_user_allowed((int)$post->post_author)) {
             return false;
         }
         return $this->cache->get_marker($post->ID);
     }
     public function remove_display_marker()
     {
         remove_action('wp_footer', array( $this, 'on_display_marker' ), $this->frontendDisplayFilterPriority);
         remove_action('the_content', array( $this, 'on_display_marker_in_content' ));
     }
     public function remove_display_marker_amp()
     {
         remove_action('amphtml_footer_bottom', array( $this, 'on_display_marker_amp' ), $this->frontendDisplayFilterPriority);
         remove_action('amp_post_template_footer', array( $this, 'on_display_marker_amp' ), $this->frontendDisplayFilterPriority);
     }
     public function remove_display_marker_feed()
     {
         if ($this->options->get_is_feed_output()) {
             remove_action('the_content_feed', array( $this, 'on_display_marker' ), $this->frontendDisplayFilterPriority);
         }
     }
 }
