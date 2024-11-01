<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_ConfigurationAdminView extends WPVGW_AdminViewBase
 {
     const AJAX_NONCE_STRING = WPVGW . '-ajax-nonce-users-view';
     protected $markersManager;
     protected $options;
     public static function get_slug_static()
     {
         return 'configuration';
     }
     public static function get_long_name_static()
     {
         return __('Einstellungen', WPVGW_TEXT_DOMAIN);
     }
     public static function get_short_name_static()
     {
         return __('Einstellungen', WPVGW_TEXT_DOMAIN);
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_Options $options)
     {
         parent::__construct(self::get_slug_static(), self::get_long_name_static(), self::get_short_name_static());
         $this->markersManager = $markers_manager;
         $this->options = $options;
         add_action('wp_ajax_' . WPVGW . '_generate_api_key', array( $this, 'ajax_generate_api_key' ));
     }
     public function init()
     {
         $this->init_base(array( array( 'file' => 'views/admin/configuration-admin-view.js', 'slug' => 'admin-view-configuration', 'dependencies' => array( 'jquery' ), 'localize' => array( 'object_name' => 'ajax_object', 'data' => array( 'nonce' => wp_create_nonce(self::AJAX_NONCE_STRING), 'ajax_url' => admin_url('admin-ajax.php') ) ) ), ));
     }
     public function ajax_generate_api_key()
     {
         check_ajax_referer(self::AJAX_NONCE_STRING);
         if (!current_user_can('manage_options')) {
             WPVGW_Helper::die_cheating();
         }
         $isDelete = isset($_POST['is_delete']) ? $_POST['is_delete'] === 'true' : false;
         $apiKey = null;
         if (!$isDelete) {
             do {
                 $apiKey = WPVGW_MarkersRestRoute::generate_api_key();
             } while ($this->options->get_api_key() === $apiKey);
         }
         $this->options->set_api_key($apiKey);
         $returnData = array( 'api_key' => $apiKey, );
         wp_send_json($returnData);
     }
     public function render()
     {
         $this->begin_render_base();
         $activatedText = $this->options->get_use_tls() ? __('<strong>aktiviert (TLS)</strong>', WPVGW_TEXT_DOMAIN) : __('<strong>aktiviert</strong>', WPVGW_TEXT_DOMAIN); ?>
		<p class="wpvgw-admin-page-description">
			<?php _e('Hier können allgemeine Einstellungen vorgenommen werden.', WPVGW_TEXT_DOMAIN); ?>
		</p>
		<form method="post">
			<?php echo($this->get_wp_number_once_field()) ?>
			<table class="form-table wpvgw-form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e('Zeichenanzahl', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_minimum_character_count"><?php _e('Minimale Zeichenanzahl für Seiten', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" id="wpvgw_minimum_character_count" name="wpvgw_minimum_character_count" class="regular-text" value="<?php echo(esc_attr($this->options->get_vg_wort_minimum_character_count())); ?>"/>
								<span class="description wpvgw-description">
									<?php _e('Die Mindestanzahl an Zeichen, die eine Seite haben muss, damit eine Zählmarke zugeordnet werden darf (wird von der VG WORT vorgegeben).', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_vg_wort_minimum_character_count()))) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_do_shortcodes_for_character_count_calculation" id="wpvgw_do_shortcodes_for_character_count_calculation" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_do_shortcodes_for_character_count_calculation())) ?>/>
								<label for="wpvgw_do_shortcodes_for_character_count_calculation"><?php _e('Shortcodes bei Berechnung der Zeichenanzahl auswerten', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Bei Aktivierung werden <a href="http://codex.wordpress.org/Shortcode" target="_blank">Shortcodes</a> bei der Berechnung der Zeichenanzahl einer Seite mit ausgewertet. Die Zeichenanzahl wird genauer, aber die Berechnung dauert länger. Die Zeichenanzahlen der Seiten müssen nach Änderung neuberechnet werden.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_consider_excerpt_for_character_count_calculation" id="wpvgw_consider_excerpt_for_character_count_calculation" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_consider_excerpt_for_character_count_calculation())) ?>/>
								<label for="wpvgw_consider_excerpt_for_character_count_calculation"><?php _e('Seitenauszug bei Berechnung der Zeichenanzahl auswerten', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Bei Aktivierung wird die Zeichenanzahl der Auszüge von Seiten bei der Berechnung der Zeichenanzahl einer Seite mit einberechnet. Diese Option sollte nur aktiviert werden, wenn ein Auszug tatsächlich auf der Seiten-Webseite angezeigt wird, ansonsten verstößt dies möglicherweise gegen die Bestimmungen der VG WORT. Die Zeichenanzahlen der Seiten müssen nach Änderung neuberechnet werden.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zählmarken-Ausgabe', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<input type="radio" name="wpvgw_use_tls" id="wpvgw_use_tls_yes" value="1" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_use_tls())) ?>/>
								<label for="wpvgw_use_tls_yes"><?php _e('Verschlüsselte Verbindungen (TLS) verwenden (HTTPS)', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php echo(__('Für Websites/Blogs, die über eine verschlüsselte Verbindung (HTTPS, TLS) aufgerufen werden, sollten (öffentliche) Zählmarken ebenfalls über eine verschlüsselte Verbindung aufgerufen werden. Es wird der in den Zählmarken angegebene Server verwendet.', WPVGW_TEXT_DOMAIN)); ?>
								</span>
							</p>
							<p class="wpvgw-configuration-sub">
								<label for="wpvgw_tls_output_format"><?php _e('Ausgabe-Format WordPress:', WPVGW_TEXT_DOMAIN); ?></label> <input type="text" id="wpvgw_tls_output_format" name="wpvgw_tls_output_format" class="regular-text" value="<?php echo(esc_attr($this->options->get_tls_output_format())); ?>"/>
								<?php if ($this->options->get_use_tls() && !$this->options->get_lazy_load_marker()) {
             echo($activatedText);
         } ?>
								<span class="description wpvgw-description">
									<?php echo(__('So wie in diesem Textfeld angegeben, wird eine Zählmarke auf einer WordPress-Webseite ausgegeben. Dies ist in der Regel ein HTML-Code. %1$s wird durch den Server ersetzt (ohne https://); %2$s wird durch die öffentliche Zählmarke ersetzt. Für verschlüsselte Verbindung (TLS) muss https:// und nicht http:// angegeben werden.', WPVGW_TEXT_DOMAIN)); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_tls_output_format()))) ?>
								</span>
								<?php if ($this->options->is_changed_tls_output_format()) : ?>
									<span class="description wpvgw-warning-description">
										<?php echo(sprintf(__('Das angegebene Zählmarken-Ausgabe-Format stimmt nicht mit dem Standardwert überein. Bitte prüfen Sie genau, ob dies gewollt ist. Eine fehlerhafte Ausgabe kann die Zählung bei der VG WORT verhindern. Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_tls_output_format()))); ?>
									</span>
								<?php endif; ?>
							</p>
							<p class="wpvgw-configuration-sub">
								<label for="wpvgw_amp_tls_output_format"><?php _e('Ausgabe-Format AMP-Plugin:', WPVGW_TEXT_DOMAIN); ?></label> <input type="text" id="wpvgw_amp_tls_output_format" name="wpvgw_amp_tls_output_format" class="regular-text" value="<?php echo(esc_attr($this->options->get_amp_tls_output_format())); ?>"/>
								<?php if ($this->options->get_use_tls()) {
             echo($activatedText);
         } ?>
								<span class="description wpvgw-description">
									<?php echo(__('So wie in diesem Textfeld angegeben, wird eine Zählmarke auf einer durch das <a href="https://de.wordpress.org/plugins/amp/" target="_blank">AMP-Plugin</a> generierten Webseite ausgegeben. Dies ist in der Regel ein AMP-HTML-Code. %1$s wird durch den Server ersetzt (ohne https://); %2$s wird durch die öffentliche Zählmarke ersetzt. Für verschlüsselte Verbindung (TLS) muss https:// und nicht http:// angegeben werden.', WPVGW_TEXT_DOMAIN)); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_amp_tls_output_format()))) ?>
								</span>
							</p>
							<p>
								<input type="radio" name="wpvgw_use_tls" id="wpvgw_use_tls_no" value="0" <?php echo(WPVGW_Helper::get_html_checkbox_checked(!$this->options->get_use_tls())) ?>/>
								<label for="wpvgw_use_tls_no"><?php _e('Unverschlüsselte Verbindungen verwenden (HTTP)', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Für Websites/Blogs, die über eine unverschlüsselte Verbindung (HTTP) aufgerufen werden, können (öffentliche) Zählmarken ebenfalls über eine unverschlüsselte Verbindung aufgerufen werden. Es wird der in den Zählmarken angegebene Server verwendet.', WPVGW_TEXT_DOMAIN); ?>
								</span>
							</p>
							<p class="wpvgw-configuration-sub">
								<label for="wpvgw_output_format"><?php _e('Ausgabe-Format WordPress:', WPVGW_TEXT_DOMAIN); ?></label> <input type="text" id="wpvgw_output_format" name="wpvgw_output_format" class="regular-text" value="<?php echo(esc_attr($this->options->get_output_format())); ?>"/>
								<?php if (!$this->options->get_use_tls() && !$this->options->get_lazy_load_marker()) {
             echo($activatedText);
         } ?>
								<span class="description wpvgw-description">
									<?php _e('So wie in diesem Textfeld angegeben, wird eine Zählmarke auf einer WordPress-Webseite ausgegeben. Dies ist in der Regel ein HTML-Code. %1$s wird durch den Server ersetzt (ohne http://); %2$s wird durch die öffentliche Zählmarke ersetzt. Für unverschlüsselte Verbindung muss http:// und nicht https:// angegeben werden.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_output_format()))) ?>
								</span>
								<?php if ($this->options->is_changed_output_format()) : ?>
									<span class="description wpvgw-warning-description">
										<?php echo(sprintf(__('Das angegebene Zählmarken-Ausgabe-Format stimmt nicht mit dem Standardwert überein. Bitte prüfen Sie genau, ob dies gewollt ist. Eine fehlerhafte Ausgabe kann die Zählung bei der VG WORT verhindern. Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_output_format()))); ?>
									</span>
								<?php endif; ?>
							</p>
							<p class="wpvgw-configuration-sub">
								<label for="wpvgw_amp_output_format"><?php _e('Ausgabe-Format AMP-Plugin:', WPVGW_TEXT_DOMAIN); ?></label> <input type="text" id="wpvgw_amp_output_format" name="wpvgw_amp_output_format" class="regular-text" value="<?php echo(esc_attr($this->options->get_amp_output_format())); ?>"/>
								<?php if (!$this->options->get_use_tls()) {
             echo($activatedText);
         } ?>
								<span class="description wpvgw-description">
									<?php _e('So wie in diesem Textfeld angegeben, wird eine Zählmarke auf einer durch das <a href="https://de.wordpress.org/plugins/amp/" target="_blank">AMP-Plugin</a> generierten Webseite ausgegeben. Dies ist in der Regel ein AMP-HTML-Code. %1$s wird durch den Server ersetzt (ohne //); %2$s wird durch die öffentliche Zählmarke ersetzt. Für unverschlüsselte Verbindung muss http:// und nicht https:// angegeben werden.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_amp_output_format()))) ?>
								</span>
							</p>
                            <p>
								<input type="checkbox" name="wpvgw_lazy_load_marker" id="wpvgw_lazy_load_marker" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_lazy_load_marker())) ?>/>
								<label for="wpvgw_lazy_load_marker"><?php _e('Zählmarken verzögert ausgeben/laden (Lazy Loading)', WPVGW_TEXT_DOMAIN); ?></label>
	                            <?php if ($this->options->get_lazy_load_marker()) {
             echo($activatedText);
         } ?>
	                            <span class="description wpvgw-description">
									<?php _e('Bei Aktivierung werden Zählmarken nicht beim Webseiten-Aufbau geladen, sondern erst, nachdem die Webseite fertig geladen wurde. Derart wird der Webseiten-Aufbau nicht blockiert, falls die VG-WORT-Server nicht oder nur schlecht erreichbar sind. Diese Funktion erfordert allerdings Javascript-Unterstützung des Webbrowsers. Das Ausgabeformat der Zählmarke kann nicht verändert werden. Die Ausgabe im AMP-Plugin bleibt davon unberührt.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_is_top_output" id="wpvgw_is_top_output" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_is_top_output())) ?>/>
								<label for="wpvgw_is_top_output"><?php _e('Zählmarke vor Seiteninhalt ausgeben', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description-important">
									<?php _e('<strong>Achtung</strong>: Bei Aktivierung werden Zählmarken vor dem Seiteninhalt ausgegeben, anstatt im Fußbereich der Webseite. Diese Funktion ist experimentell und kann ggf. dazu führen, dass eine Zählmarke falsch ausgegeben wird!', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_is_feed_output" id="wpvgw_is_feed_output" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_is_feed_output())) ?>/>
								<label for="wpvgw_is_feed_output"><?php _e('Zählmarken in Feeds (RSS, Atom, RDF) ausgeben', WPVGW_TEXT_DOMAIN); ?></label>
								<?php if ($this->options->get_is_feed_output()) {
             echo($activatedText);
         } ?>
								<span class="description wpvgw-description-important">
									<?php _e('<strong>Achtung</strong>: Bei Aktivierung werden Zählmarken auch in Feeds (RSS, Atom, RDF) ausgegeben. Je nach Feedreader werden ggf. alle Seiten gleichzeitig vollständig angezeigt, sodass auch sämtliche Zählmarken geladen werden. Dies verstößt möglicherweise gegen die Vorgaben der VG WORT.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zählmarken-Einstellungen', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_default_server"><?php _e('Standard-Server', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" id="wpvgw_default_server" name="wpvgw_default_server" class="regular-text" value="<?php echo(esc_attr($this->options->get_default_server())); ?>"/>
								<span class="description wpvgw-description">
									<?php _e('Wenn für Zählmarken nicht explizit ein Server angegeben wurde (z. B. beim Importieren), wird dieser Server verwendet.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_default_server()))) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_post_view_set_marker_for_published_only" id="wpvgw_post_view_set_marker_for_published_only" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_post_view_set_marker_for_published_only())) ?>/>
								<label for="wpvgw_post_view_set_marker_for_published_only"><?php _e('Seiten nur eine Zählmarke zuordnen, wenn veröffentlicht/geplant', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Bei Aktivierung werden neuen Seiten oder Seiten, die bearbeitet werden, nur eine Zählmarke zugeordnet, wenn sie auch veröffentlicht/geplant werden. Entwürfen usw. werden keine Zählmarken zugeordnet.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_post_view_set_marker_by_default" id="wpvgw_post_view_set_marker_by_default" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_post_view_set_marker_by_default())) ?>/>
								<label for="wpvgw_post_view_set_marker_by_default"><?php _e('Seiten standardmäßig eine Zählmarke zuordnen', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Bei Aktivierung werden neuen Seiten oder Seiten, die bearbeitet werden, als Voreinstellung Zählmarken zugeordnet (abwählbar). Dies geschieht ohne Beachtung der Mindestanzahl an Zeichen.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('REST API', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_api_key"><?php _e('REST-API-Schlüssel', WPVGW_TEXT_DOMAIN) ?></label>
								<br/>
								<input type="text" name="wpvgw_api_key" id="wpvgw_api_key" readonly="readonly" value="<?php echo(esc_attr($this->options->get_api_key() ?? '')) ?>" class="regular-text"/>
								<br/>
								<a id="wpvgw_api_key_delete" href="#"><?php _e('löschen', WPVGW_TEXT_DOMAIN); ?></a> |
								<a id="wpvgw_api_key_generator" href="#"><?php _e('neu generieren', WPVGW_TEXT_DOMAIN); ?></a>
								<span class="description wpvgw-description">
									<?php _e('Ein zufällig erstellter Zugangsschlüssel, um auf die REST-API von Prosodia VGW OS zugreifen zu können.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Plugin-Shortcodes', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_shortcode_post_stats_template"><?php _e('Standard-Ausgabeformat für Post-Stats-Shortcode', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" id="wpvgw_shortcode_post_stats_template" name="wpvgw_shortcode_post_stats_template" class="regular-text" value="<?php echo(esc_attr($this->options->get_shortcode_post_stats_template())); ?>"/>
								<span class="description wpvgw-description">
									<?php _e('So wie in diesem Textfeld angegeben, wird der Text des Post-Stats-Shortcodes ausgegeben, falls dieser nicht explizit im Shortcode festgelegt wurde.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php _e('%1$s wird durch die Zeichenzahl ersetzt; %2$s wird durch die Anzahl der Normseiten ersetzt; %3$s wird durch die Anzahl der Seiten (benutzerdefinierte Größe, Standard ist A4) ersetzt.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_shortcode_post_stats_template()))) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Plugin-Warnungen', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<?php _e('Bei Aktivierung der jeweiligen Einstellung wird eine entsprechende Warnung im Administrationsbereich angezeigt.', WPVGW_TEXT_DOMAIN) ?>
							</p>
                            <p>
								<input type="checkbox" name="wpvgw_show_use_tls_warning" id="wpvgw_show_use_tls_warning" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_show_use_tls_warning())) ?>/>
								<label for="wpvgw_show_use_tls_warning"><?php _e('Warnung, falls HTTPS vom Webserver, aber nicht vom Plugin verwendet wird (und andersherum).', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Wird angezeigt, falls der Webserver HTTPS verwendet, aber das Plugin die Zählmarken nicht als HTTPS ausgibt (und andersherum).', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_show_changed_output_format_warning" id="wpvgw_show_changed_output_format_warning" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_show_changed_output_format_warning())) ?>/>
								<label for="wpvgw_show_changed_output_format_warning"><?php _e('Warnung, falls Zählmarken-Ausgabe-Format vom Standardwert abweicht', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Wird angezeigt, falls das Zählmarken-Ausgabe-Format vom Standardwert abweicht. Ein solche Abweichung kann dazu führen, dass die Zählung bei der VG WORT nicht erfolgt.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_show_other_active_vg_wort_plugins_warning" id="wpvgw_show_other_active_vg_wort_plugins_warning" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_show_other_active_vg_wort_plugins_warning())) ?>/>
								<label for="wpvgw_show_other_active_vg_wort_plugins_warning"><?php _e('Warnung, falls andere VG-WORT-Plugins aktiviert sind', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Wird angezeigt, falls andere Plugins zur Integration von Zählmarken der VG WORT aktiviert sind.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_show_old_plugin_import_warning" id="wpvgw_show_old_plugin_import_warning" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_operation_old_plugin_import_necessary())) ?>/>
								<label for="wpvgw_show_old_plugin_import_warning"><?php _e('Warnung, falls Zählmarken aus früherer Plugin-Version importiert werden sollten', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Wird angezeigt, falls Zählmarken aus einer früheren Version des Plugins importiert werden sollten. Diese Warnung wird nach dem Import automatisch deaktiviert.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
					<th scope="row"><?php _e('Verschiedenes', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<input type="checkbox" name="wpvgw_post_table_view_use_colors" id="wpvgw_post_table_view_use_colors" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_post_table_view_use_colors())) ?>/>
								<label for="wpvgw_post_table_view_use_colors"><?php _e('Farben in der Seitenübersicht verwenden', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Bei Aktivierung werden in der Seitenübersicht (Tabelle) Farben für „Zählmarke möglich“ und „Zählmarke zugeordnet“ in der Spalte „Zeichen“ verwendet.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<label for="wpvgw_number_of_markers_per_page"><?php _e('Zählmarken pro Seite in der Zählmarken-Übersicht', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" id="wpvgw_number_of_markers_per_page" name="wpvgw_number_of_markers_per_page" class="regular-text" value="<?php echo(esc_attr($this->options->get_number_of_markers_per_page())); ?>"/>
								<span class="description wpvgw-description">
									<?php _e('Die Anzahl der Zählmarken, die auf einer Seite in der Zählmarken-Übersicht (Tabelle) angezeigt werden soll.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_number_of_markers_per_page()))) ?>
								</span>
							</p>
							<p>
								<label for="wpvgw_post_view_refresh_character_count_timeout"><?php _e('Zeitspanne für automatische Berechnung der Zeichenanzahl im Seiten-Editor (in Sekunden)', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" id="wpvgw_post_view_refresh_character_count_timeout" name="wpvgw_post_view_refresh_character_count_timeout" class="regular-text" value="<?php echo(esc_attr($this->options->get_post_view_refresh_character_count_timeout())); ?>"/>
								<span class="description wpvgw-description">
									<?php _e('Legt die Zeitspanne in Sekunden fest, nach der die Berechnung der Zeichenanzahl im Text im Seiten-Editor automatisch neuberechnet werden soll.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php _e('Die Zahl -1 deaktiviert das automatische Neuberechnen.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_post_view_refresh_character_count_timeout()))) ?>
								</span>
							</p>
							<p>
								<label for="wpvgw_operations_max_execution_time"><?php _e('Maximale Ausführungszeit für Operationen (in Sekunden)', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" id="wpvgw_operations_max_execution_time" name="wpvgw_operations_max_execution_time" class="regular-text" value="<?php echo(esc_attr($this->options->get_operation_max_execution_time())); ?>"/>
								<span class="description wpvgw-description">
									<?php _e('Legt die maximale Zeitspanne in Sekunden fest, um Operationen im Bereich „Operationen“ auszuführen. Bitte erhöhen, falls Operationen abbrechen (siehe auch <a href="http://php.net/manual/de/info.configuration.php#ini.max-execution-time" target="_blank">max_execution_time</a>).', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_operation_max_execution_time()))) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Deinstallation', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<input type="checkbox" name="wpvgw_remove_data_on_uninstall" id="wpvgw_remove_data_on_uninstall" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_remove_data_on_uninstall())) ?>/>
								<label for="wpvgw_remove_data_on_uninstall"><?php _e('Daten bei Plugin-Deaktivierung löschen', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description-important">
									<?php _e('<strong>Achtung</strong>: Bei Aktivierung werden sämtlichen Daten (Zählmarken, Zuordnungen usw.) unwiderruflich gelöscht, sobald das VG-WORT-Plugin deaktiviert wird!', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="wpvgw_configuration" value="<?php _e('Einstellungen speichern', WPVGW_TEXT_DOMAIN); ?>" class="button-primary"/>
			</p>
		</form>
		<?php
 $this->end_render_base();
     }
     public function do_action()
     {
         if (!$this->do_action_base()) {
             return;
         }
         $minimumCharacterCount = $_POST['wpvgw_minimum_character_count'] ?? null;
         if ($minimumCharacterCount !== null) {
             if (is_numeric($minimumCharacterCount)) {
                 $minimumCharacterCount = (int)$minimumCharacterCount;
                 if ($minimumCharacterCount >= 0) {
                     $this->options->set_vg_wort_minimum_character_count($minimumCharacterCount);
                 } else {
                     $this->add_admin_message(__('Die nötige Zeichenanzahl muss einen Wert größer oder gleich 0 haben.', WPVGW_TEXT_DOMAIN));
                 }
             } else {
                 $this->add_admin_message(__('Die nötige Zeichenanzahl muss eine natürliche Zahl größer oder gleich 0 sein.', WPVGW_TEXT_DOMAIN));
             }
         }
         $doShortcodesForCharacterCountCalculation = isset($_POST['wpvgw_do_shortcodes_for_character_count_calculation']);
         if ($doShortcodesForCharacterCountCalculation !== $this->options->get_do_shortcodes_for_character_count_calculation()) {
             $this->options->set_do_shortcodes_for_character_count_calculation($doShortcodesForCharacterCountCalculation);
             $this->options->set_operations_post_character_count_recalculations_necessary(true);
         }
         $considerExcerptForCharacterCountCalculation = isset($_POST['wpvgw_consider_excerpt_for_character_count_calculation']);
         if ($considerExcerptForCharacterCountCalculation !== $this->options->get_consider_excerpt_for_character_count_calculation()) {
             $this->options->set_consider_excerpt_for_character_count_calculation($considerExcerptForCharacterCountCalculation);
             $this->options->set_operations_post_character_count_recalculations_necessary(true);
         }
         $outputFormat = isset($_POST['wpvgw_output_format']) ? stripslashes($_POST['wpvgw_output_format']) : null;
         $lazyLoadMarker = isset($_POST['wpvgw_lazy_load_marker']);
         $isFeedOutput = isset($_POST['wpvgw_is_feed_output']);
         $ampOutputFormat = isset($_POST['wpvgw_amp_output_format']) ? stripslashes($_POST['wpvgw_amp_output_format']) : null;
         if ($outputFormat !== null) {
             $this->options->set_output_format($outputFormat);
         }
         $this->options->set_lazy_load_marker($lazyLoadMarker);
         $this->options->set_is_feed_output($isFeedOutput);
         if ($ampOutputFormat !== null) {
             $this->options->set_amp_output_format($ampOutputFormat);
         }
         $tlsOutputFormat = isset($_POST['wpvgw_tls_output_format']) ? stripslashes($_POST['wpvgw_tls_output_format']) : null;
         $ampTlsOutputFormat = isset($_POST['wpvgw_amp_tls_output_format']) ? stripslashes($_POST['wpvgw_amp_tls_output_format']) : null;
         if ($tlsOutputFormat !== null) {
             $this->options->set_tls_output_format($tlsOutputFormat);
         }
         if ($ampTlsOutputFormat !== null) {
             $this->options->set_amp_tls_output_format($ampTlsOutputFormat);
         }
         $useTls = $_POST['wpvgw_use_tls'] ?? null;
         if ($useTls !== null) {
             $this->options->set_use_tls($useTls === '1');
         }
         $this->options->set_is_top_output(isset($_POST['wpvgw_is_top_output']));
         $defaultServer = isset($_POST['wpvgw_default_server']) ? stripslashes($_POST['wpvgw_default_server']) : null;
         $defaultServer = $this->markersManager->server_cleaner($defaultServer);
         if ($defaultServer !== null && WPVGW_MarkersManager::server_validator($defaultServer)) {
             $this->options->set_default_server($defaultServer);
         } else {
             $this->add_admin_message(__('Der eingegebene Standard-Server hat ein ungültiges Format.', WPVGW_TEXT_DOMAIN));
         }
         $this->options->set_post_view_set_marker_by_default(isset($_POST['wpvgw_post_view_set_marker_by_default']));
         $this->options->set_post_view_set_marker_for_published_only(isset($_POST['wpvgw_post_view_set_marker_for_published_only']));
         $shortcodePostStatsTemplate = isset($_POST['wpvgw_shortcode_post_stats_template']) ? stripslashes($_POST['wpvgw_shortcode_post_stats_template']) : null;
         if ($shortcodePostStatsTemplate !== null) {
             $this->options->set_shortcode_post_stats_template($shortcodePostStatsTemplate);
         }
         $numberOfMarkersPerPage = $_POST['wpvgw_number_of_markers_per_page'] ?? null;
         if ($numberOfMarkersPerPage !== null) {
             if (is_numeric($numberOfMarkersPerPage)) {
                 $numberOfMarkersPerPage = (int)$numberOfMarkersPerPage;
                 if ($numberOfMarkersPerPage >= 1) {
                     $this->options->set_number_of_markers_per_page($numberOfMarkersPerPage);
                 } else {
                     $this->add_admin_message(__('Die Anzahl der Zählmarken pro Seite muss einen Wert größer oder gleich 1 haben.', WPVGW_TEXT_DOMAIN));
                 }
             } else {
                 $this->add_admin_message(__('Die Anzahl der Zählmarken pro Seite muss eine natürliche Zahl größer oder gleich 1 sein.', WPVGW_TEXT_DOMAIN));
             }
         }
         $this->options->set_post_table_view_use_colors(isset($_POST['wpvgw_post_table_view_use_colors']));
         $operationMaxExecutionTime = $_POST['wpvgw_operations_max_execution_time'] ?? null;
         if ($operationMaxExecutionTime !== null) {
             if (is_numeric($operationMaxExecutionTime)) {
                 $operationMaxExecutionTime = (int)$operationMaxExecutionTime;
                 if ($operationMaxExecutionTime >= 1) {
                     $this->options->set_operation_max_execution_time($operationMaxExecutionTime);
                 } else {
                     $this->add_admin_message(__('Die maximale Ausführungszeit für Operationen (in Sekunden) muss einen Wert größer 0 haben.', WPVGW_TEXT_DOMAIN));
                 }
             } else {
                 $this->add_admin_message(__('Die maximale Ausführungszeit für Operationen (in Sekunden) muss eine natürliche Zahl größer als 0 sein.', WPVGW_TEXT_DOMAIN));
             }
         }
         $postViewRefreshCharacterCountTimeout = $_POST['wpvgw_post_view_refresh_character_count_timeout'] ?? null;
         if ($postViewRefreshCharacterCountTimeout !== null) {
             if (is_numeric($postViewRefreshCharacterCountTimeout)) {
                 $postViewRefreshCharacterCountTimeout = (int)$postViewRefreshCharacterCountTimeout;
                 if ($postViewRefreshCharacterCountTimeout === -1 || ($postViewRefreshCharacterCountTimeout >= 4 && $postViewRefreshCharacterCountTimeout <= 3600)) {
                     $this->options->set_post_view_refresh_character_count_timeout($postViewRefreshCharacterCountTimeout);
                 } else {
                     $this->add_admin_message(__('Die Zeitspanne für die automatische Berechnung der Zeichenanzahl im Seiten-Editor (in Sekunden) muss einen Wert zwischen 4 und 3600 (oder -1) haben.', WPVGW_TEXT_DOMAIN));
                 }
             } else {
                 $this->add_admin_message(__('Die Zeitspanne für die automatische Berechnung der Zeichenanzahl im Seiten-Editor (in Sekunden) muss eine natürliche Zahl zwischen 4 und 3600 (oder -1) sein.', WPVGW_TEXT_DOMAIN));
             }
         }
         $showUseTlsWarning = isset($_POST['wpvgw_show_use_tls_warning']);
         $this->options->set_show_use_tls_warning($showUseTlsWarning);
         $showOtherActiveVgWortPluginsWarning = isset($_POST['wpvgw_show_other_active_vg_wort_plugins_warning']);
         $this->options->set_show_other_active_vg_wort_plugins_warning($showOtherActiveVgWortPluginsWarning);
         $showChangedOutputFormatWarning = isset($_POST['wpvgw_show_changed_output_format_warning']);
         $this->options->set_show_changed_output_format_warning($showChangedOutputFormatWarning);
         $wpvgwShowOldPluginImportWarning = isset($_POST['wpvgw_show_old_plugin_import_warning']);
         $this->options->set_operation_old_plugin_import_necessary($wpvgwShowOldPluginImportWarning);
         $removeDataOnUninstall = isset($_POST['wpvgw_remove_data_on_uninstall']);
         $this->options->set_remove_data_on_uninstall($removeDataOnUninstall);
         $this->add_admin_message(__('Einstellungen erfolgreich übernommen.', WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Update);
     }
 }
