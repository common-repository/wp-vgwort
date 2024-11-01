<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_ImportAdminView extends WPVGW_AdminViewBase
 {
     const AJAX_NONCE_STRING = WPVGW . '-ajax-nonce-import-admin-view';
     protected $markersManager;
     protected $options;
     public static function get_slug_static()
     {
         return 'import';
     }
     public static function get_long_name_static()
     {
         return __('Import von Zählmarken', WPVGW_TEXT_DOMAIN);
     }
     public static function get_short_name_static()
     {
         return __('Import', WPVGW_TEXT_DOMAIN);
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_Options $options)
     {
         parent::__construct(self::get_slug_static(), self::get_long_name_static(), self::get_short_name_static());
         $this->markersManager = $markers_manager;
         $this->options = $options;
         add_action('wp_ajax_' . WPVGW . '_get_today_date', array( $this, 'ajax_get_today_date' ));
     }
     public function init()
     {
         $this->init_base(array( array( 'file' => 'views/admin/import-admin-view.js', 'slug' => 'admin-view-import', 'dependencies' => array( 'jquery' ), 'localize' => array( 'object_name' => 'ajax_object', 'data' => array( 'nonce' => wp_create_nonce(self::AJAX_NONCE_STRING), 'ajax_url' => admin_url('admin-ajax.php') ) ) ) ));
     }
     private function get_vg_wort_today_date()
     {
         return new DateTime('now', WPVGW_Helper::get_vg_wort_time_zone());
     }
     private function get_vg_wort_today_date_string()
     {
         $todayDate = $this->get_vg_wort_today_date();
         return $todayDate->format(WPVGW_Helper::get_vg_wort_order_date_format());
     }
     public function ajax_get_today_date()
     {
         check_ajax_referer(self::AJAX_NONCE_STRING);
         $data = array( 'today_date' => $this->get_vg_wort_today_date_string(), );
         wp_send_json($data);
     }
     public function render()
     {
         $this->begin_render_base(); ?>
		<p class="wpvgw-admin-page-description">
			<?php _e('Zählmarken der VG WORT können auf bis zu 3 verschiedene Arten importiert werden. Es wird empfohlen, CSV-Dateien zu importieren, die übers Online-Konto bei der VG WORT zur Verfügung gestellt werden.', WPVGW_TEXT_DOMAIN); ?>
		</p>
		<form method="post" enctype="multipart/form-data">
			<?php echo($this->get_wp_number_once_field()) ?>
			<table class="form-table wpvgw-form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e('Zählmarken aus CSV-Daten', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_import_file"><?php _e('CSV-Datei', WPVGW_TEXT_DOMAIN); ?></label>
								<input type="file" name="wpvgw_import_file" id="wpvgw_import_file"/>
								<span class="description wpvgw-description">
									<?php _e('Hier kann eine CSV-Datei mit Zählmarken der VG WORT hochgeladen werden, welche über das <a href="https://tom.vgwort.de/portal/login" target="_blank">Online-Konto der VG WORT</a> bezogen werden kann.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<label for="wpvgw_import_csv"><?php _e('Zählmarken aus CSV-Text (von VG WORT)', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<textarea name="wpvgw_import_csv" id="wpvgw_import_csv" style="overflow: auto;" wrap="off" cols="36" rows="7"></textarea>
								<span class="description wpvgw-description">
									<?php _e('Hier kann der gesamte Inhalt einer CSV-Datei mit Zählmarken der VG WORT hineinkopiert werden (falls das Hochladen der CSV-Datei nicht funktioniert).', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
							<p>
								<input type="radio" name="wpvgw_import_is_author_csv" id="wpvgw_import_is_author_csv" value="1" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_is_author_csv())) ?> />
								<label for="wpvgw_import_is_author_csv"><?php _e('CSV-Daten von Autor-Konto', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="radio" name="wpvgw_import_is_author_csv" id="wpvgw_import_is_publisher_csv" value="0" <?php echo(WPVGW_Helper::get_html_checkbox_checked(!$this->options->get_is_author_csv())) ?> />
								<label for="wpvgw_import_is_publisher_csv"><?php _e('CSV-Daten von Verlags-Konto', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Bei der VG WORT unterscheiden sich CSV-Daten von Autoren-Konten und Verlags-Konten.', WPVGW_TEXT_DOMAIN) ?>
									<br/>
									<?php _e('CSV-Daten für Autoren enthalten das Bestelldatum. Dieses wird automatisch importiert.', WPVGW_TEXT_DOMAIN) ?>
									<br/>
									<?php
 echo(sprintf(__('CSV-Daten für Verlage enthalten kein Bestelldatum und keine Server-Angaben, daher muss der %s korrekt angegeben sein.', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_ConfigurationAdminView::get_slug_static())), __('Standard-Server', WPVGW_TEXT_DOMAIN)))) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Zählmarke manuell eingeben (nicht empfohlen)', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p>
								<label for="wpvgw_import_public_marker"><?php _e('Öffentliche Zählmarke', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" name="wpvgw_import_public_marker" id="wpvgw_import_public_marker" class="regular-text"/>
							</p>
							<p>
								<label for="wpvgw_import_private_marker"><?php _e('Private Zählmarke', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" name="wpvgw_import_private_marker" id="wpvgw_import_private_marker" class="regular-text"/>
							</p>
							<p>
								<label for="wpvgw_import_server"><?php _e('Server', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="text" name="wpvgw_import_server" id="wpvgw_import_server" class="regular-text"/>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Wenn der Server nicht angegeben wird, wird der Standard-Server (%s) verwendet.<br>Der Server sollte im Format „vg&lt;ZAHL&gt;.met.vgwort.de/na“ (ohne „http://“, ohne „/“ am Ende) angegeben werden.', WPVGW_TEXT_DOMAIN), esc_html($this->options->get_default_server()))); ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Gemeinsame Angaben<br/>(CSV und manuell)</th>
						<td>
							<p>
								<label for="wpvgw_order_date"><?php _e('Bestelldatum', WPVGW_TEXT_DOMAIN); ?></label>
								<br/>
								<input type="checkbox" name="wpvgw_is_order_date_today" id="wpvgw_is_order_date_today" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_import_is_order_date_today())) ?>/>
								<label for="wpvgw_is_order_date_today"><?php echo(sprintf(__('Heute (%s)', WPVGW_TEXT_DOMAIN), esc_html($this->get_vg_wort_today_date_string()))) ?></label>
								<br/>
								<input type="text" id="wpvgw_order_date" name="wpvgw_order_date" value="" class="regular-text"/>
								<a id="wpvgw_order_date_today" href="#"><?php _e('heute', WPVGW_TEXT_DOMAIN); ?></a>
								<span class="description wpvgw-description">
									<?php _e('Das Bestelldatum der zu importierenden Zählmarken im Format TT.MM.JJJJ (z. B. 06.03.2015).', WPVGW_TEXT_DOMAIN) ?>
									<br/>
									<?php _e('Beim Import über CSV-Daten von Autoren-Konten wird es automatisch ermittelt und das eingegebene Datum ignoriert.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="wpvgw_import" value="<?php _e('Zählmarken importieren', WPVGW_TEXT_DOMAIN); ?>" class="button-primary"/>
			</p>
		</form>
		<?php
 $this->end_render_base();
     }
     private function add_no_csv_markers_found_admin_message(WPVGW_ImportMarkersStats $import_markers_stats)
     {
         if ($import_markers_stats->numberOfMarkers === 0) {
             $this->add_admin_message(__('Es wurden keine Zählmarken für den Import in den CSV-Daten gefunden. Stellen Sie bitte sicher, dass Sie die von der VG WORT erhaltenen CSV-Daten unverändert eingeben haben. Die Spalten der CSV-Daten müssen mit Semikolon (;) getrennt sein. Bei der VG WORT unterscheiden sich CSV-Daten von Autoren-Konten und Verlags-Konten, daher muss dies beim Import ausgewählt werden.', WPVGW_TEXT_DOMAIN));
         }
     }
     public function do_action()
     {
         if (!$this->do_action_base()) {
             return;
         }
         $csvIsAuthorCSV = isset($_POST['wpvgw_import_is_author_csv']) ? (bool)$_POST['wpvgw_import_is_author_csv'] : true;
         $csvText = isset($_POST['wpvgw_import_csv']) ? stripslashes($_POST['wpvgw_import_csv']) : '';
         $publicMarker = isset($_POST['wpvgw_import_public_marker']) ? stripslashes($_POST['wpvgw_import_public_marker']) : '';
         $privateMarker = isset($_POST['wpvgw_import_private_marker']) ? stripslashes($_POST['wpvgw_import_private_marker']) : null;
         $server = isset($_POST['wpvgw_import_server']) ? stripslashes($_POST['wpvgw_import_server']) : null;
         if ($privateMarker === '') {
             $privateMarker = null;
         }
         if ($server === '') {
             $server = null;
         }
         $this->options->set_is_author_csv($csvIsAuthorCSV);
         $isOrderDateToday = isset($_POST['wpvgw_is_order_date_today']);
         $this->options->set_import_is_order_date_today($isOrderDateToday);
         $orderDate = isset($_POST['wpvgw_order_date']) ? stripslashes($_POST['wpvgw_order_date']) : null;
         $orderDate = $orderDate === '' ? null : $orderDate;
         $orderDateObject = null;
         if ($isOrderDateToday) {
             $orderDateObject = $this->get_vg_wort_today_date();
         } elseif ($orderDate !== null) {
             $orderDateObject = $this->markersManager->parse_vg_wort_order_date_from_string($orderDate);
             if ($orderDateObject === false) {
                 $this->add_admin_message(__('Das Bestelldatum hat ein ungültiges Format. Es muss der Form TT.MM.JJJJ entsprechen (z. B. 06.03.2015).', WPVGW_TEXT_DOMAIN));
                 return;
             }
         }
         if (array_key_exists('wpvgw_import_file', $_FILES)) {
             $uploadedFile = $_FILES['wpvgw_import_file'];
             if ($uploadedFile['error'] === 0) {
                 $filePath = $uploadedFile['tmp_name'];
                 $importStats = null;
                 try {
                     $importStats = $this->markersManager->import_markers_from_csv_file($this->options->get_is_author_csv(), $filePath, $this->options->get_default_server(), $orderDateObject);
                 } catch (Exception $e) {
                     $this->add_admin_message(sprintf(__('Fehler beim Importieren der CSV-Datei: %s', WPVGW_TEXT_DOMAIN), $e->getMessage()));
                 }
                 if ($importStats !== null) {
                     $this->add_admin_message(__('Zählmarken aus CSV-Datei: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($importStats), WPVGW_ErrorType::Update);
                     $this->add_no_csv_markers_found_admin_message($importStats);
                 }
                 if (!unlink($filePath)) {
                     $this->add_admin_message(__('Hochgeladene Datei konnte nicht gelöscht werden.', WPVGW_TEXT_DOMAIN));
                 }
             }
         }
         if ($csvText !== '') {
             $importStats = null;
             try {
                 $importStats = $this->markersManager->import_markers_from_csv($this->options->get_is_author_csv(), $csvText, $this->options->get_default_server(), $orderDateObject);
             } catch (Exception $e) {
                 $this->add_admin_message(sprintf(__('Fehler beim Importieren des CSV-Texts: %s', WPVGW_TEXT_DOMAIN), $e->getMessage()));
             }
             if ($importStats !== null) {
                 $this->add_admin_message(__('Zählmarken aus CSV-Text: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($importStats), WPVGW_ErrorType::Update);
                 $this->add_no_csv_markers_found_admin_message($importStats);
             }
         }
         if ($publicMarker !== '') {
             $importStats = null;
             try {
                 $importStats = $this->markersManager->import_marker($this->options->get_default_server(), $publicMarker, $privateMarker, $server, $orderDateObject);
             } catch (Exception $e) {
                 $this->add_admin_message(sprintf(__('Fehler beim Importieren der Zählmarke: %s', WPVGW_TEXT_DOMAIN), $e->getMessage()));
             }
             if ($importStats !== null) {
                 $this->add_admin_message(__('Zählmarke: ', WPVGW_TEXT_DOMAIN) . $this->create_import_markers_stats_message($importStats), WPVGW_ErrorType::Update);
             }
         }
     }
 }
