<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_OperationsAdminView extends WPVGW_AdminViewBase
 {
     protected $markersManager;
     protected $postsExtras;
     protected $options;
     private $recalculatePostCharacterCountTask;
     private $importMarkersAndPostsFromPostsTask;
     private $importOldPluginMarkersTask;
     public static function get_slug_static()
     {
         return 'operations';
     }
     public static function get_long_name_static()
     {
         return __('Komplexe Operationen und Einstellungen', WPVGW_TEXT_DOMAIN);
     }
     public static function get_short_name_static()
     {
         return __('Operationen', WPVGW_TEXT_DOMAIN);
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options)
     {
         parent::__construct(self::get_slug_static(), self::get_long_name_static(), self::get_short_name_static());
         $this->markersManager = $markers_manager;
         $this->options = $options;
         $this->postsExtras = $posts_extras;
         $this->recalculatePostCharacterCountTask = new WPVGW_LongTask('recalculate_post_character_count', 1, __('%s Seiten, deren Zeichenanzahl neuberechnet wurde.', WPVGW_TEXT_DOMAIN), array( $this, 'recalculate_post_character_count' ), array( $this, 'recalculate_post_character_count_end' ), WPVGW_RecalculatePostCharacterCountStats::class);
         $this->importOldPluginMarkersTask = new WPVGW_LongTask('import_old_plugin_markers', 1, __('%s Seiten nach Zählmarken aus altem VG-WORT-Plugin durchsucht.', WPVGW_TEXT_DOMAIN), array( $this, 'import_old_plugin_markers' ), array( $this, 'import_old_plugin_markers_end' ), WPVGW_ImportOldMarkersAndPostsStats::class);
         $this->importMarkersAndPostsFromPostsTask = new WPVGW_LongTask('import_markers_and_posts_from_posts', 1, __('%s Seiten nach Zählmarken (manuelle) durchsucht.', WPVGW_TEXT_DOMAIN), array( $this, 'import_markers_and_posts_from_posts' ), array( $this, 'import_markers_and_posts_from_posts_end' ), WPVGW_ImportOldMarkersAndPostsStats::class);
         new WPVGW_LongTask('import_old_worthy_plugin_markers', 1, __('%s Datensätze nach Zählmarken des Plugins „Worthy“ von B. Holzmüller durchsucht.', WPVGW_TEXT_DOMAIN), array( $this, 'import_old_worthy_plugin_markers' ), array( $this, 'import_old_worthy_plugin_markers_end' ), WPVGW_ImportOldMarkersAndPostsStats::class);
         new WPVGW_LongTask('import_old_tl_vgwort_plugin_markers', 1, __('%s Seiten nach Zählmarken des Plugins „VG Wort“ von T. Leuschner durchsucht.', WPVGW_TEXT_DOMAIN), array( $this, 'import_old_tl_vgwort_plugin_markers' ), array( $this, 'import_old_tl_vgwort_plugin_markers_end' ), WPVGW_ImportOldMarkersAndPostsStats::class);
         new WPVGW_LongTask('import_old_vgw_plugin_markers', 1, __('%s Seiten nach Zählmarken des Plugins „VG-Wort Krimskram“ von H. Otterstedt durchsucht.', WPVGW_TEXT_DOMAIN), array( $this, 'import_old_vgw_plugin_markers' ), array( $this, 'import_old_vgw_plugin_markers_end' ), WPVGW_ImportOldMarkersAndPostsStats::class);
         new WPVGW_LongTask('import_prosodia_vgw_markers', 1, __('%s Datensätze nach Zählmarken des Plugins „Prosodia VGW“ (Open-Source-Version dieses Plugins) durchsucht.', WPVGW_TEXT_DOMAIN), array( $this, 'import_prosodia_vgw_markers' ), array( $this, 'import_prosodia_vgw_markers_end' ), WPVGW_ImportOldMarkersAndPostsStats::class);
     }
     public function init()
     {
         $this->init_base(array( array( 'file' => 'views/admin/operations-admin-view.js', 'slug' => 'admin-view-operations', 'dependencies' => array( 'jquery' ), ), WPVGW_LongTask::get_javascript() ));
     }
     public function render()
     {
         $this->begin_render_base();
         WPVGW_LongTask::render_task_window_html();
         if ($this->recalculatePostCharacterCountTask->is_auto_run()) {
             $this->recalculatePostCharacterCountTask->render_auto_run_js();
         }
         if ($this->importOldPluginMarkersTask->is_auto_run()) {
             $this->importOldPluginMarkersTask->render_auto_run_js();
         }
         if ($this->importMarkersAndPostsFromPostsTask->is_auto_run()) {
             $this->importMarkersAndPostsFromPostsTask->render_auto_run_js();
         } ?>
		<p class="wpvgw-admin-page-description">
			<?php _e('An dieser Stelle können aufwendigere Operationen und Einstellungen vorgenommen werden. Die Bearbeitung einzelner Operationen und Einstellungen kann mehrere Sekunden in Anspruch nehmen. <strong>Gestartete Operationen sollten nicht abgebrochen werden!</strong>', WPVGW_TEXT_DOMAIN); ?>
		</p>
		<form method="post">
			<?php echo($this->get_wp_number_once_field()) ?>
			<table class="form-table wpvgw-form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e('Zugelassene Seitentypen', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<?php _e('Die ausgewählten Seitentypen werden mit der Zählmarken-Funktion versehen:', WPVGW_TEXT_DOMAIN) ?>
							</p>
							<?php
 $postTypes = $this->markersManager->get_possible_post_types();
         echo('<ul class="wpvgw-check-list-box">');
         if (count($postTypes) < 0) { ?>
								<li>
									<?php _e('Keine Seitentypen vorhanden!', WPVGW_TEXT_DOMAIN); ?>
								</li>
								<?php
 }
         foreach ($postTypes as $type) {
             $postTypeObject = get_post_type_object($type);
             $checked = WPVGW_Helper::get_html_checkbox_checked($this->markersManager->is_post_type_allowed($type)); ?>
								<li>
									<input type="checkbox" <?php echo($checked) ?> id="wpvgw_allowed_post_types_<?php echo($type) ?>" name="wpvgw_allowed_post_types[<?php echo($type) ?>]"/>
									<label for="wpvgw_allowed_post_types_<?php echo($type) ?>"><?php echo(esc_html($postTypeObject->labels->name)) ?></label>
								</li>
								<?php
         }
         $removedPostTypes = $this->markersManager->get_removed_post_types();
         foreach ($removedPostTypes as $type) { ?>
								<li class="wpvgw-invalid">
									<input type="checkbox" id="wpvgw_removed_post_types_<?php echo($type) ?>" name="wpvgw_removed_post_types[<?php echo($type) ?>]"/>
									<label for="wpvgw_removed_post_types_<?php echo($type) ?>"><?php echo(esc_html($type)) ?></label>
								</li>
								<?php
 }
         echo('</ul>'); ?>
							<p class="submit">
								<input type="submit" name="wpvgw_operation_allowed_post_types" value="<?php _e('Seitentypen zulassen', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<input type="submit" name="wpvgw_operation_removed_post_types" value="<?php _e('Nicht verfügbare Seitetypen entfernen', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Beim Abwählen eines Seitentyps werden die Zählmarken-Zuordnungen entsprechender Seiten nicht gelöscht.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php _e('Die Zeichenanzahlen aller Seiten der ausgewählten Seitentypen werden automatisch neuberechnet.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php _e('Rotmarkierte Seitentypen (entfernbar) sind nicht verfügbar, da Plugins/Themes, die diese definieren, deaktiviert oder deinstalliert wurden.', WPVGW_TEXT_DOMAIN); ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zeichenanzahl neuberechnen', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_recalculate_character_count" name="wpvgw_operation_recalculate_character_count" value="<?php _e('Zeichenanzahl aller Seiten neuberechnen', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Die Zeichenanzahlen aller Seiten werden separat gespeichert.', WPVGW_TEXT_DOMAIN); ?>
									<br/>
									<?php _e('Wenn die Zeichenanzahl einer Seite falsch oder nicht vorhanden ist, kann sie hier neuberechnet werden.', WPVGW_TEXT_DOMAIN); ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zählmarken aus Prosodia VGW importieren', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_import_prosodia_vgw_markers" name="wpvgw_operation_import_prosodia_vgw_markers" value="<?php _e('Zählmarken aus Prosodia VGW importieren', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Wenn das Plugin „Prosodia VGW“ (die kommerzielle Version dieses Plugins) zuvor verwendet wurde, können die dort zugeordneten Zählmarken hier importiert werden. Falls es zu Konflikten mit bereits vorhandenen Zählmarken-Zuordnungen kommt, wird keine Überschreibung vorgenommen. Es werden keine Daten gelöscht.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zählmarken aus Version vor 3.0.0 importieren', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_operation_import_old_plugin_markers_meta_name"><?php _e('Meta-Name aus altem Plugin: ', WPVGW_TEXT_DOMAIN); ?></label>
								<input type="text" name="wpvgw_operation_import_old_plugin_markers_meta_name" id="wpvgw_operation_import_old_plugin_markers_meta_name" class="regular-text" value="<?php echo(esc_attr($this->options->get_meta_name())) ?>"/>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_meta_name()))); ?>
								</span>
							</p>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_import_old_plugin_markers" name="wpvgw_operation_import_old_plugin_markers" value="<?php _e('Zählmarken aus altem VG-WORT-Plugin vor Version 3.0.0 importieren', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Wenn dieses VG-WORT-Plugin bereits vor Version 3.0.0 verwendet wurde, können hier die zuvor verwendeten Zählmarken importiert werden. Falls es zu Konflikten mit bereits vorhandenen Zählmarken-Zuordnungen kommt, wird keine Überschreibung vorgenommen. Es werden keine Daten gelöscht.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Manuelle Zählmarken importieren', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<input type="checkbox" name="wpvgw_operation_import_old_manual_markers_unlock" id="wpvgw_operation_import_old_manual_markers_unlock" value="1" class="checkbox"/>
								<label for="wpvgw_operation_import_old_manual_markers_unlock"><?php _e('Ich habe die folgenden Hinweise und Funktionen verstanden – freischalten', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="checkbox" name="wpvgw_operation_import_old_manual_markers_delete" id="wpvgw_operation_import_old_manual_markers_delete" value="1" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_import_from_post_delete())) ?> class="checkbox"/>
								<label for="wpvgw_operation_import_old_manual_markers_delete"><?php _e('Gefundene Zählmarken aus Seiten (deren Inhalt) entfernen (empfohlen)', WPVGW_TEXT_DOMAIN); ?></label>
                                <span class="description wpvgw-description">
									<?php _e('Eine gefundene manuelle Zählmarke sollte aus der Seite (deren Inhalt) entfernt werden, da die Zählmarke sonst mindestens zweimal auf der entsprechenden Web-Seite ausgegeben wird. Eine (unrechtmäßige) doppelte Zählung sollte allerdings nicht erfolgen, auch wenn die manuelle Zählmarke nicht entfernt wird.', WPVGW_TEXT_DOMAIN) ?>
								</span>
								<br/>
								<label for="wpvgw_operation_import_old_manual_markers_regex"><?php _e('Regulärer Ausdruck (PHP-Stil) zur Zählmarkenerkennung: ', WPVGW_TEXT_DOMAIN); ?></label>
								<input type="text" name="wpvgw_operation_import_old_manual_markers_regex" id="wpvgw_operation_import_old_manual_markers_regex" class="regular-text" value="<?php echo(esc_attr($this->options->get_import_from_post_regex())) ?>"/>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Der Standardwert ist: <span class="wpvgw-inline-code">%s</span>', WPVGW_TEXT_DOMAIN), esc_html($this->options->default_import_from_post_regex()))); ?>
								</span>
							</p>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_import_old_manual_markers" name="wpvgw_operation_import_old_manual_markers" value="<?php _e('Zählmarken (manuelle) in Seiten erkennen und importieren', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Wenn bereits Zählmarken der Form %s manuell in Seiten (deren Inhalt) eingefügt wurden, können diese hier importiert und entfernt werden. Falls es zu Konflikten mit bereits vorhandenen Zählmarken-Zuordnungen kommt, wird keine Überschreibung vorgenommen.', WPVGW_TEXT_DOMAIN), esc_html('<img src="http://vg02.met.vgwort.de/na/abc123" … >'))) ?>
								</span>
								<span class="description wpvgw-description-important">
									<?php _e('<strong>Achtung:</strong> Aus Seiten (deren Inhalt) entfernte Zählmarken können nicht wiederhergestellt werden. Bitte sämtliche Seiten sichern (Backup), bevor diese Operation durchgeführt wird.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zählmarken anderer Plugins importieren', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_import_old_worthy_plugin_markers" name="wpvgw_operation_import_old_worthy_plugin_markers" value="<?php _e('Zählmarken aus dem Plugin „Worthy“ von B. Holzmüller importieren', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Wenn das Plugin „Worthy“ von B. Holzmüller zuvor verwendet wurde, können die dort eingefügten Zählmarken und Zählmarken-Zuordnungen hier importiert werden. Falls es zu Konflikten mit bereits vorhandenen Zählmarken-Zuordnungen kommt, wird keine Überschreibung vorgenommen. Es werden keine Daten gelöscht.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_import_old_tl_vgwort_plugin_markers" name="wpvgw_operation_import_old_tl_vgwort_plugin_markers" value="<?php _e('Zählmarken aus dem Plugin „VG Wort“ von T. Leuschner importieren', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Wenn das Plugin „VG Wort“ von T. Leuschner zuvor verwendet wurde, können die dort zugeordneten Zählmarken hier importiert werden. Falls es zu Konflikten mit bereits vorhandenen Zählmarken-Zuordnungen kommt, wird keine Überschreibung vorgenommen. Es werden keine Daten gelöscht.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p class="submit">
								<input type="submit" id="wpvgw_operation_import_old_vgw_plugin_markers" name="wpvgw_operation_import_old_vgw_plugin_markers" value="<?php _e('Zählmarken aus dem Plugin „VG-Wort Krimskram“ von H. Otterstedt importieren', WPVGW_TEXT_DOMAIN); ?>" class="button"/>
								<span class="description wpvgw-description">
									<?php _e('Wenn das Plugin „VG-Wort Krimskram“ von H. Otterstedt zuvor verwendet wurde, können die dort zugeordneten Zählmarken hier importiert werden. Falls es zu Konflikten mit bereits vorhandenen Zählmarken-Zuordnungen kommt, wird keine Überschreibung vorgenommen. Es werden keine Daten gelöscht.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
 $this->end_render_base();
     }
     public function recalculate_post_character_count($offset, WPVGW_RecalculatePostCharacterCountStats &$stats = null, array &$error_messages = null)
     {
         $postsExtrasFillStats = $this->postsExtras->recalculate_all_post_character_count_in_db($offset);
         if ($stats === null) {
             $stats = $postsExtrasFillStats;
         } else {
             $stats = $stats->add($postsExtrasFillStats);
         }
         return $postsExtrasFillStats->numberOfPostExtrasUpdates;
     }
     public function recalculate_post_character_count_end(WPVGW_RecalculatePostCharacterCountStats $stats)
     {
         $this->options->set_operations_post_character_count_recalculations_necessary(false);
         return array( array( _n('Für eine Seite wurde die Zeichenanzahl neuberechnet.', sprintf('Für %s Seiten wurden die Zeichenanzahlen neuberechnet.', number_format_i18n($stats->numberOfPostExtrasUpdates)), $stats->numberOfPostExtrasUpdates, WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Update ) );
     }
     public function import_old_plugin_markers($offset, WPVGW_ImportOldMarkersAndPostsStats &$stats = null, array &$error_messages = null)
     {
         $importOldMarkersAndPostsStats = $this->markersManager->import_markers_and_posts_from_old_version($offset, $this->options->get_meta_name(), $this->options->get_default_server());
         if ($stats === null) {
             $stats = $importOldMarkersAndPostsStats;
         } else {
             $stats = $stats->add($importOldMarkersAndPostsStats);
         }
         return $importOldMarkersAndPostsStats->numberOfIterations;
     }
     public function import_old_plugin_markers_end(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         $this->options->set_operation_old_plugin_import_necessary(false);
         return array( array( __('Zählmarken aus altem VG-WORT-Plugin importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($stats->importMarkersStats), WPVGW_ErrorType::Update ), array( __('Zählmarken-Zuordnungen aus altem VG-WORT-Plugin importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_old_markers_and_posts_stats($stats), WPVGW_ErrorType::Update ) );
     }
     public function import_markers_and_posts_from_posts($offset, WPVGW_ImportOldMarkersAndPostsStats &$stats = null, array &$error_messages = null)
     {
         $importOldMarkersAndPostsStats = $this->markersManager->import_markers_and_posts_from_posts($offset, $this->options->get_import_from_post_regex(), $this->options->get_default_server(), $this->options->get_import_from_post_delete());
         if ($stats === null) {
             $stats = $importOldMarkersAndPostsStats;
         } else {
             $stats = $stats->add($importOldMarkersAndPostsStats);
         }
         return $importOldMarkersAndPostsStats->numberOfIterations;
     }
     public function import_markers_and_posts_from_posts_end(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         return array( array( __('Zählmarken (manuelle) aus Seiten importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($stats->importMarkersStats), WPVGW_ErrorType::Update ), array( __('Zählmarken-Zuordnungen aus Seiten importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_old_markers_and_posts_stats($stats), WPVGW_ErrorType::Update ) );
     }
     public function import_old_worthy_plugin_markers($offset, WPVGW_ImportOldMarkersAndPostsStats &$stats = null, array &$error_messages = null)
     {
         $importOldMarkersAndPostsStats = $this->markersManager->import_markers_and_posts_from_wp_worthy($offset, $this->options->get_default_server());
         if ($importOldMarkersAndPostsStats === null) {
             $error_messages = array( array( __('Die Datenbank-Tabelle zum Plugin „Worthy“ von B. Holzmüller wurde nicht gefunden. Keine Zählmarken importiert.', WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Error ) );
             return 0;
         }
         if ($stats === null) {
             $stats = $importOldMarkersAndPostsStats;
         } else {
             $stats = $stats->add($importOldMarkersAndPostsStats);
         }
         return $importOldMarkersAndPostsStats->numberOfIterations;
     }
     public function import_old_worthy_plugin_markers_end(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         return array( array( __('Zählmarken aus Plugin „Worthy“ von B. Holzmüller importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($stats->importMarkersStats), WPVGW_ErrorType::Update ), array( __('Zählmarken-Zuordnungen aus Plugin „Worthy“ von B. Holzmüller importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_old_markers_and_posts_stats($stats), WPVGW_ErrorType::Update ) );
     }
     public function import_old_tl_vgwort_plugin_markers($offset, WPVGW_ImportOldMarkersAndPostsStats &$stats = null, array &$error_messages = null)
     {
         $importOldMarkersAndPostsStats = $this->markersManager->import_markers_and_posts_from_tl_vgwort_plugin($offset, $this->options->get_default_server());
         if ($stats === null) {
             $stats = $importOldMarkersAndPostsStats;
         } else {
             $stats = $stats->add($importOldMarkersAndPostsStats);
         }
         return $importOldMarkersAndPostsStats->numberOfIterations;
     }
     public function import_old_tl_vgwort_plugin_markers_end(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         return array( array( __('Zählmarken aus Plugin „VG Wort“ von T. Leuschner importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($stats->importMarkersStats), WPVGW_ErrorType::Update ), array( __('Zählmarken-Zuordnungen aus Plugin „VG Wort“ von T. Leuschner importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_old_markers_and_posts_stats($stats), WPVGW_ErrorType::Update ) );
     }
     public function import_old_vgw_plugin_markers($offset, WPVGW_ImportOldMarkersAndPostsStats &$stats = null, array &$error_messages = null)
     {
         $importOldMarkersAndPostsStats = $this->markersManager->import_markers_and_posts_from_vgw_plugin($offset, $this->options->get_default_server());
         if ($stats === null) {
             $stats = $importOldMarkersAndPostsStats;
         } else {
             $stats = $stats->add($importOldMarkersAndPostsStats);
         }
         return $importOldMarkersAndPostsStats->numberOfIterations;
     }
     public function import_old_vgw_plugin_markers_end(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         return array( array( __('Zählmarken aus Plugin „VG-Wort Krimskram“ von H. Otterstedt importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($stats->importMarkersStats), WPVGW_ErrorType::Update ), array( __('Zählmarken-Zuordnungen aus Plugin „VG-Wort Krimskram“ von H. Otterstedt importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_old_markers_and_posts_stats($stats), WPVGW_ErrorType::Update ) );
     }
     public function import_prosodia_vgw_markers($offset, WPVGW_ImportOldMarkersAndPostsStats &$stats = null, array &$error_messages = null)
     {
         $importOldMarkersAndPostsStats = $this->markersManager->import_markers_and_posts_from_prosodia_vgw($offset, $this->options->get_default_server());
         if ($importOldMarkersAndPostsStats === null) {
             $error_messages = array( array( __('Die Datenbank-Tabelle zum Plugin „Prosodia VGW“ wurde nicht gefunden. Keine Zählmarken importiert.', WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Error ) );
             return 0;
         }
         if ($stats === null) {
             $stats = $importOldMarkersAndPostsStats;
         } else {
             $stats = $stats->add($importOldMarkersAndPostsStats);
         }
         return $importOldMarkersAndPostsStats->numberOfIterations;
     }
     public function import_prosodia_vgw_markers_end(WPVGW_ImportOldMarkersAndPostsStats $stats)
     {
         return array( array( __('Zählmarken aus Plugin „Prosodia VGW“ importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($stats->importMarkersStats), WPVGW_ErrorType::Update ), array( __('Zählmarken-Zuordnungen aus Plugin „Prosodia VGW“ importiert: ', WPVGW_TEXT_DOMAIN) . $this->create_import_old_markers_and_posts_stats($stats), WPVGW_ErrorType::Update ) );
     }
     private function create_import_old_markers_and_posts_stats(WPVGW_ImportOldMarkersAndPostsStats $import_old_markers_and_posts_stats)
     {
         return sprintf(__('aktualisiert %s, bereits vorhanden %s, die Integrität verletzten %s, Seiten durchsucht %s, Datensätze durchsucht %s', WPVGW_TEXT_DOMAIN), number_format_i18n($import_old_markers_and_posts_stats->numberOfUpdates), number_format_i18n($import_old_markers_and_posts_stats->numberOfDuplicates), number_format_i18n($import_old_markers_and_posts_stats->numberOfIntegrityErrors), number_format_i18n($import_old_markers_and_posts_stats->numberOfPosts), number_format_i18n($import_old_markers_and_posts_stats->numberOfIterations));
     }
     public function do_action()
     {
         if (!$this->do_action_base()) {
             return;
         }
         @ini_set('max_execution_time', $this->options->get_operation_max_execution_time());
         if (isset($_POST['wpvgw_operation_allowed_post_types'])) {
             $allowedPostTypes = array();
             if (isset($_POST['wpvgw_allowed_post_types']) && is_array($_POST['wpvgw_allowed_post_types'])) {
                 foreach ($_POST['wpvgw_allowed_post_types'] as $key => $value) {
                     $allowedPostTypes[] = stripslashes($key);
                 }
             }
             $this->markersManager->set_allowed_post_types($allowedPostTypes);
             $allowedPostTypesCount = count($allowedPostTypes);
             $this->add_admin_message(_n('Der ausgewählte Seitentyp wurde mit der Zählmarken-Funktion versehen.', sprintf('%s ausgewählte Seitentypen wurden mit der Zählmarken-Funktion versehen.', number_format_i18n($allowedPostTypesCount)), $allowedPostTypesCount, WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Update);
             $this->recalculatePostCharacterCountTask->set_auto_run(true);
         }
         if (isset($_POST['wpvgw_operation_removed_post_types'])) {
             $removedPostTypes = array();
             if (isset($_POST['wpvgw_removed_post_types']) && is_array($_POST['wpvgw_removed_post_types'])) {
                 foreach ($_POST['wpvgw_removed_post_types'] as $key => $value) {
                     $removedPostTypes[] = stripslashes($key);
                 }
             }
             $this->markersManager->set_removed_post_types(array_diff($this->markersManager->get_removed_post_types(), $removedPostTypes));
             $removedPostTypesCount = count($removedPostTypes);
             $this->add_admin_message(_n('Der ausgewählte nicht verfügbare Seitentyp wurde entfernt.', sprintf('%s ausgewählte nicht verfügbare Seitentypen wurden entfernt.', number_format_i18n($removedPostTypesCount)), $removedPostTypesCount, WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Update);
         }
         if (isset($_POST['wpvgw_operation_import_old_plugin_markers'])) {
             $metaName = isset($_POST['wpvgw_operation_import_old_plugin_markers_meta_name']) ? stripslashes($_POST['wpvgw_operation_import_old_plugin_markers_meta_name']) : null;
             try {
                 $this->options->set_meta_name($metaName);
             } catch (Exception $e) {
                 $this->add_admin_message(__('Der Meta-Name ist ungültig und wurde zurückgesetzt. Zählmarken aus altem VG-WORT-Plugin wurden nicht importiert.', WPVGW_TEXT_DOMAIN));
                 return;
             }
             $this->importOldPluginMarkersTask->set_auto_run(true);
         }
         if (isset($_POST['wpvgw_operation_import_old_manual_markers'], $_POST['wpvgw_operation_import_old_manual_markers_unlock'])) {
             $this->options->set_import_from_post_delete(isset($_POST['wpvgw_operation_import_old_manual_markers_delete']));
             $manualMarkersRegex = isset($_POST['wpvgw_operation_import_old_manual_markers_regex']) ? stripslashes($_POST['wpvgw_operation_import_old_manual_markers_regex']) : null;
             try {
                 $this->options->set_import_from_post_regex($manualMarkersRegex);
             } catch (Exception $e) {
                 $this->add_admin_message(__('Der reguläre Ausdruck hat eine ungültige Syntax und wurde zurückgesetzt. Zählmarken (manuelle) aus Seiten wurden nicht importiert.', WPVGW_TEXT_DOMAIN));
                 return;
             }
             $this->importMarkersAndPostsFromPostsTask->set_auto_run(true);
         }
     }
 }
