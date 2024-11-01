<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_PostView extends WPVGW_ViewBase
 {
     const AJAX_NONCE_STRING = WPVGW . '-ajax-nonce-post-view';
     protected $markersManager;
     protected $postsExtras;
     protected $options;
     protected $userOptions;
     private $postType;
     private $singularPostTypeName;
     private $pluralPostTypeName;
     private $escapedSingularPostTypeName;
     private $escapedPluralPostTypeName;
     private $errorSetting;
     private static $noticesSlugs = array( 'MarkerNotFree' => 'marker-not-free', 'MarkerNotFound' => 'marker-not-found', 'MarkerUserNotAllowed' => 'marker-user-not-allowed', 'MarkerIsBlocked' => 'marker-is-blocked', 'TooFewCharacters' => 'too-few-characters', 'NoFreeMarker' => 'no-free-marker', 'PublicMarkerInvalidFormat' => 'public-marker-invalid-format', 'PrivateMarkerInvalidFormat' => 'private-marker-invalid-format', 'PublicAndPrivateMarkerEmpty' => 'public-and-private-marker-empty', 'CouldNotRemoveMarkerFromPost' => 'could-not-remove-marker-from-post', 'PostNotPublished' => 'post-not-published', 'MediaNotAttachedToPost' => 'media-not-attached-to-post', );
     public function set_post_type(WP_Post_Type $value)
     {
         $this->postType = $value;
     }
     public function __construct(WPVGW_MarkersManager $markers_manager, WPVGW_PostsExtras $posts_extras, WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         parent::__construct();
         $this->markersManager = $markers_manager;
         $this->postsExtras = $posts_extras;
         $this->options = $options;
         $this->userOptions = $user_options;
         $this->errorSetting = WPVGW . '-edit-post-error-setting';
         add_action('wp_ajax_' . WPVGW . '_get_character_count', array( $this, 'ajax_get_character_count' ));
         add_action('wp_ajax_' . WPVGW . '_get_meta_box', array( $this, 'ajax_get_meta_box' ));
     }
     public function init()
     {
         if ($this->postType === null) {
             throw new Exception('Post type must be set before calling init().');
         }
         $this->singularPostTypeName = WPVGW_Helper::get_post_type_name($this->postType);
         $this->pluralPostTypeName = WPVGW_Helper::get_post_type_name($this->postType, true);
         $this->escapedSingularPostTypeName = esc_html($this->singularPostTypeName);
         $this->escapedPluralPostTypeName = esc_html($this->pluralPostTypeName);
         $javascripts = array( array( 'file' => 'views/post-view.js', 'slug' => 'post-view', 'dependencies' => array( 'jquery' ), 'localize' => array( 'object_name' => 'ajax_object', 'data' => array( 'nonce' => wp_create_nonce(self::AJAX_NONCE_STRING), 'ajax_url' => admin_url('admin-ajax.php'), 'refresh_character_count_timeout' => $this->options->get_post_view_refresh_character_count_timeout() === -1 ? -1 : $this->options->get_post_view_refresh_character_count_timeout() * 1000, 'isMedia' => WPVGW_Helper::is_media($this->postType) ) ) ) );
         $returnJavascriptArray = array();
         do_action_ref_array(WPVGW . '_add_javascript_post_view', array( &$returnJavascriptArray ));
         if (!empty($returnJavascriptArray)) {
             $javascripts[] = $returnJavascriptArray;
         }
         $this->init_base($javascripts);
         add_action('admin_notices', array( $this, 'on_admin_notice' ));
         add_action('add_meta_boxes', array( $this, 'on_add_meta_box' ), 10, 2);
     }
     public function on_add_meta_box($post_type, $post)
     {
         $postTypeObject = get_post_type_object($post->post_type);
         if (!current_user_can($postTypeObject->cap->edit_post, $post->ID)) {
             WPVGW_Helper::die_cheating();
         }
         if (!$this->markersManager->is_user_allowed((int)$post->post_author)) {
             return;
         }
         add_meta_box(WPVGW . '-meta-box', __('Zählmarken für VG WORT', WPVGW_TEXT_DOMAIN), array( $this, 'render' ), $post_type, 'advanced', 'high');
     }
     public function ajax_get_meta_box()
     {
         check_ajax_referer(self::AJAX_NONCE_STRING);
         if (!isset($_POST['wpvgw_post_id'])) {
             $data = array( 'meta_box_html' => sprintf('<p>%s</p>', __('Fehler: Seite nicht gefunden.', WPVGW_TEXT_DOMAIN)), 'notices' => array(), 'notices_slugs' => array(), );
         } else {
             $postId = (int)$_POST['wpvgw_post_id'];
             ob_start();
             $this->render(get_post($postId));
             $metaBoxHtml = ob_get_clean();
             $data = array( 'meta_box_html' => $metaBoxHtml, 'notices' => $this->userOptions->get_edit_post_error_setting(), 'notices_slugs' => array_values(self::$noticesSlugs) );
         }
         wp_send_json($data);
     }
     public function ajax_get_character_count()
     {
         check_ajax_referer(self::AJAX_NONCE_STRING);
         $postTitle = isset($_POST['wpvgw_post_title']) ? stripslashes($_POST['wpvgw_post_title']) : '';
         $postContent = isset($_POST['wpvgw_post_content']) ? stripslashes($_POST['wpvgw_post_content']) : '';
         $postExcerpt = isset($_POST['wpvgw_post_excerpt']) ? stripslashes($_POST['wpvgw_post_excerpt']) : '';
         $postCustomObject = $_POST['wpvgw_post_custom_object'] ?? null;
         $additionalTexts = $_POST['wpvgw_additional_texts'] ?? array();
         if (!is_array($additionalTexts)) {
             $additionalTexts = array();
         }
         $characterCount = $this->markersManager->calculate_character_count($postTitle, $postContent, $postExcerpt, null, $postCustomObject, $additionalTexts);
         $minimumCharacterCount = $this->options->get_vg_wort_minimum_character_count();
         $data = array( 'character_count' => number_format_i18n($characterCount), 'character_count_sufficient' => $this->markersManager->is_character_count_sufficient($characterCount, $minimumCharacterCount) ? __('<strong>ja</strong>', WPVGW_TEXT_DOMAIN) : __('nein', WPVGW_TEXT_DOMAIN), 'missing_character_count' => number_format_i18n($this->markersManager->calculate_missing_character_count($characterCount, $minimumCharacterCount)), 'minimum_character_count' => number_format_i18n($minimumCharacterCount), );
         wp_send_json($data);
     }
     public function on_admin_notice()
     {
         $currentScreen = get_current_screen();
         if (method_exists($currentScreen, 'is_block_editor') && $currentScreen->is_block_editor()) {
             return;
         }
         $settingErrors = $this->userOptions->get_edit_post_error_setting();
         if ($settingErrors !== array()) {
             foreach ($settingErrors as $settingError) {
                 add_settings_error($this->errorSetting, $settingError['code'], $settingError['message'], $settingError['type']);
             }
             settings_errors($this->errorSetting);
         }
     }
     private function add_admin_message($slug, $message, $type = 'error')
     {
         add_settings_error($this->errorSetting, WPVGW . '-' . $slug, $message, $type);
     }
     private function create_error_from_update_result($updateResult, $singularPostTypeName)
     {
         switch ($updateResult) { case WPVGW_UpdateMarkerResults::PostIdNotNull: $this->add_admin_message(self::$noticesSlugs['MarkerNotFree'], sprintf(__('Die Zählmarke entsprechend Ihrer Vorgaben ist bereits einer anderen %s zugeordnet.', WPVGW_TEXT_DOMAIN), $singularPostTypeName)); break; case WPVGW_UpdateMarkerResults::MarkerNotFound: $this->add_admin_message(self::$noticesSlugs['MarkerNotFound'], sprintf(__('Es wurde keine Zählmarke entsprechend Ihrer Vorgaben gefunden. Die Zählmarke muss ggf. zunächst importiert werden. %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_ImportAdminView::get_slug_static())), __('Zählmarken hier importieren.', WPVGW_TEXT_DOMAIN)))); break; case WPVGW_UpdateMarkerResults::UserNotAllowed: $this->add_admin_message(self::$noticesSlugs['MarkerUserNotAllowed'], __('Sie dürfen die Zählmarke entsprechend Ihrer Vorgaben nicht verwenden, da sie für einen anderen Benutzer bestimmt ist.', WPVGW_TEXT_DOMAIN)); break; case WPVGW_UpdateMarkerResults::BlockedMarkerNotPossible: $this->add_admin_message(self::$noticesSlugs['MarkerIsBlocked'], __('Sie dürfen die Zählmarke entsprechend Ihrer Vorgaben nicht verwenden, da sie als nicht zuordenbar gesetzt ist.', WPVGW_TEXT_DOMAIN)); break; default: break; }
     }
     public function render(WP_Post $post)
     {
         wp_nonce_field('postview', '_wpvgwpostviewnonce');
         $marker = $this->markersManager->get_marker_from_db($post->ID, 'post_id');
         $isMediaPostType = WPVGW_Helper::is_media($this->postType);
         $isMediaAttachedToPost = WPVGW_Helper::is_media_attached_to_post($post);
         $postExtra = $this->postsExtras->get_post_extras_from_db((int)$post->ID);
         if ($postExtra === false) {
             $isAutoMarkerDisabled = false;
         } else {
             $isAutoMarkerDisabled = $postExtra['is_auto_marker_disabled'];
         }
         $currentUser = wp_get_current_user();
         $setMarkerByDefault = $this->options->get_post_view_set_marker_by_default() && (int)$post->post_author === $currentUser->ID; ?>

		<div id="wpvgw-postview-meta-box">

		<?php if ($marker === false) : ?>
			<p>
				<?php echo(sprintf(__('Dieser %s ist keine Zählmarke zugeordnet.', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
			</p>
		<?php else : ?>
			<?php if ($isMediaPostType && !$isMediaAttachedToPost) : ?>
				<p class="wpvgw-warning-notice">
					<?php _e('Die Datei ist mit keiner Seite verknüpft, sodass die Zählmarke nicht ausgegeben wird.', WPVGW_TEXT_DOMAIN) ?>
				</p>
			<?php endif; ?>

			<?php if ($marker['is_marker_disabled']) : ?>
				<p class="wpvgw-warning-notice">
					<?php echo(sprintf(__('Die Zählmarke dieser %s ist inaktiv, sodass die Zählmarke nicht ausgegeben wird.', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
				</p>
			<?php endif; ?>

			<p>
				<?php echo(sprintf(__('Dieser %s ist eine Zählmarke zugeordnet. In der Regel sollte die Zuordnung nicht mehr aufgehoben werden.', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
			</p>
		<?php endif; ?>

			<table class="form-table">
			<tbody>
				<?php if (!$isMediaPostType) : ?>
					<tr>
						<th><?php _e('Zeichenanzahl im Text', WPVGW_TEXT_DOMAIN) ?></th>
						<td>
							<p>
								<?php
 echo(sprintf(__('Genügend: %1$s, %2$s fehlen', WPVGW_TEXT_DOMAIN), '<span id="wpvgw_character_count_sufficient">–</span>', '<span id="wpvgw_missing_character_count">–</span>')); ?>
							</p>
							<p>
								<?php
 echo(sprintf(__('Vorhanden: %1$s von %2$s nötigen', WPVGW_TEXT_DOMAIN), '<span id="wpvgw_character_count">–</span>', '<span id="wpvgw_minimum_character_count">–</span>')); ?>
							</p>
							<p>
								<?php
 echo(sprintf('<a id="wpvgw_refresh_character_count" href="#">%s</a> %s <span id="wpvgw_refresh_character_count_spinner" class="spinner"></span>', __('jetzt aktualisieren', WPVGW_TEXT_DOMAIN), __('(sonst alle paar Sekunden automatisch)', WPVGW_TEXT_DOMAIN))); ?>
							</p>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ($marker === false) : ?>
					<tr>
						<th><?php _e('Aktion', WPVGW_TEXT_DOMAIN) ?></th>
						<td>
							<p>
								<input type="checkbox" name="wpvgw_set_marker" id="wpvgw_set_marker" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked(!$isMediaPostType && $setMarkerByDefault && !$isAutoMarkerDisabled)) ?>/>
								<label for="wpvgw_set_marker"><?php echo(sprintf(__('Dieser %s eine Zählmarke zuordnen%s', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName, !$isMediaPostType && $this->options->get_post_view_set_marker_for_published_only() ? __(' (nur, wenn veröffentlicht/geplant)', WPVGW_TEXT_DOMAIN) : '')) ?></label>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Weisen Sie dieser %s eine Zählmarke zu, um Ihre Leser bei der VG WORT zählen zu lassen.', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
								</span>
							</p>
							<?php if (!$isMediaPostType && $setMarkerByDefault) : ?>
								<p>
                                    <input type="checkbox" name="wpvgw_is_auto_marker_disabled" id="wpvgw_is_auto_marker_disabled" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($isAutoMarkerDisabled)) ?>/>
                                    <label for="wpvgw_is_auto_marker_disabled"><?php echo(sprintf(__('Diese %s von der standardmäßigen Zählmarken-Zuordnung ausnehmen', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?></label>
                                    <span class="description wpvgw-description">
                                        <?php echo(sprintf(__('Bei Aktivierung wird bei neuen %1$s oder %1$s, die bearbeitet werden, nicht standardmäßig die Checkbox „Dieser %2$s eine Zählmarke zuordnen“ auf aktiviert gesetzt.', WPVGW_TEXT_DOMAIN), $this->escapedPluralPostTypeName, $this->escapedSingularPostTypeName)) ?>
                                    </span>
                                </p>
							<?php endif; ?>
						</td>
					</tr>
					<tr id="wpvgw_add_marker_to_post">
						<th><?php _e('Zählmarken-Zuordnung', WPVGW_TEXT_DOMAIN) ?></th>
						<td>
							<p>
								<input type="checkbox" name="wpvgw_auto_marker" id="wpvgw_auto_marker" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_post_view_auto_marker())) ?>/>
								<label for="wpvgw_auto_marker"><?php _e('Zählmarke automatisch zuordnen') ?></label>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Aktivieren, um der %s automatisch eine unbenutzte Zählmarke zuordnen zu lassen (empfohlen), ansonsten manuell.', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
								</span>
							</p>
							<div id="wpvgw_manual_marker">
								<p>
									<label for="wpvgw_public_marker"><?php _e('Öffentliche Zählmarke manuell zuordnen', WPVGW_TEXT_DOMAIN) ?></label>
									<br/>
									<input type="text" name="wpvgw_public_marker" id="wpvgw_public_marker" class="regular-text" value=""/>
									<span class="description wpvgw-description">
										<?php _e('Nur bereits importierte Zählmarken können manuell zugeordnet werden. Die Angabe einer öffentlichen Zählmarke genügt.', WPVGW_TEXT_DOMAIN) ?>
									</span>
								</p>
								<p>
									<label for="wpvgw_private_marker"><?php _e('Private Zählmarke manuell zuordnen', WPVGW_TEXT_DOMAIN) ?></label>
									<br/>
									<input type="text" name="wpvgw_private_marker" id="wpvgw_private_marker" class="regular-text" value=""/>
									<span class="description wpvgw-description">
										<?php _e('Nur bereits importierte Zählmarken können manuell zugeordnet werden. Die Angabe einer privaten Zählmarke genügt.', WPVGW_TEXT_DOMAIN) ?>
									</span>
								</p>
							</div>
							<p>
								<input type="checkbox" name="wpvgw_marker_disabled" id="wpvgw_marker_disabled" value="1" class="checkbox"/>
								<label for="wpvgw_marker_disabled"><?php _e('Inaktiv', WPVGW_TEXT_DOMAIN) ?></label>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Inaktive Zählmarken werden für die zugeordnete %s nicht mehr ausgegeben (keine Zählung mehr bei VG WORT).', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
								</span>
							</p>
						</td>
					</tr>
				<?php else : ?>
					<tr>
						<th><?php _e('Zählmarken-Zuordnung', WPVGW_TEXT_DOMAIN) ?></th>
						<td>
							<p>
								<?php echo(__('Öffentlich: ', WPVGW_TEXT_DOMAIN) . esc_html($marker['public_marker'])) ?>
							</p>
							<p>
								<?php echo(__('Privat: ', WPVGW_TEXT_DOMAIN) . esc_html(WPVGW_Helper::null_data_text($marker['private_marker']))) ?>
							</p>
							<p>
								<?php echo(__('Server: ', WPVGW_TEXT_DOMAIN) . esc_html($marker['server'])) ?>
							</p>
							<p>
								<?php echo(__('Übertragungsverschlüsselung: ', WPVGW_TEXT_DOMAIN) . ($this->options->get_use_tls() ? __('verschlüsselt (TLS/HTTPS)', WPVGW_TEXT_DOMAIN) : __('unverschlüsselt (HTTP)', WPVGW_TEXT_DOMAIN))) ?>
							</p>
							<p>
								<?php
 $orderDate = $marker['order_date'];
         if ($orderDate === null) {
             $dateText = WPVGW_Helper::null_data_text();
         } else {
             $dateText = $orderDate->format(WPVGW_Helper::get_vg_wort_order_date_format());
         }
         echo(__('Bestelldatum: ', WPVGW_TEXT_DOMAIN) . esc_html($dateText)); ?>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_marker_disabled" id="wpvgw_marker_disabled" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($marker['is_marker_disabled'])) ?>/>
								<label for="wpvgw_marker_disabled"><?php _e('Inaktiv', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php echo(sprintf(__('Inaktive Zählmarken werden für die zugeordnete %s nicht mehr ausgegeben (keine Zählung mehr bei VG WORT).', WPVGW_TEXT_DOMAIN), $this->escapedSingularPostTypeName)) ?>
								</span>
							</p>
                            <p>
	                            <?php if (!$isMediaPostType || $isMediaAttachedToPost) : ?>
		                            <a href="<?php echo(WPVGW_Helper::get_test_marker_in_post_link(($isMediaPostType && $isMediaAttachedToPost) ? $post->post_parent : $post->ID, $marker['public_marker'])) ?>" target="_blank"><?php _e('Prüfen', WPVGW_TEXT_DOMAIN) ?></a>
		                            <span class="description wpvgw-description">
										<?php echo(__('Prüfen ob, die zugeordnete Zählmarke tatsächlich ausgegeben wird.', WPVGW_TEXT_DOMAIN)) ?>
									</span>
	                            <?php endif; ?>
							</p>
						</td>
					</tr>
					<?php
 if (current_user_can('manage_options')) : ?>
						<tr>
							<th><?php _e('Aktion', WPVGW_TEXT_DOMAIN) ?></th>
							<td>
								<p>
									<input type="checkbox" name="wpvgw_remove_post_from_marker" id="wpvgw_remove_post_from_marker" value="1" class="checkbox"/>
									<label for="wpvgw_remove_post_from_marker"><?php _e('Zählmarken-Zuordnung aufheben', WPVGW_TEXT_DOMAIN); ?></label>
									<span class="description wpvgw-description">
										<?php _e('In der Regel sollte die Zuordnung nicht aufgehoben, sondern die Zählmarke inaktiv gesetzt werden.', WPVGW_TEXT_DOMAIN) ?>
									</span>
								</p>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
			</tbody>
		</table>
		</div>
		<?php
     }
     private function check_post_character_count(WP_Post $post)
     {
         $postCharacterCount = $this->markersManager->calculate_character_count($post->post_title, $post->post_content, $post->post_excerpt, $post);
         $minimumCharacterCount = $this->options->get_vg_wort_minimum_character_count();
         if (!$this->markersManager->is_character_count_sufficient($postCharacterCount, $minimumCharacterCount)) {
             $this->add_admin_message(self::$noticesSlugs['TooFewCharacters'], sprintf(__('Diese %s enthält weniger als %s Zeichen (es fehlen noch %s) und verstößt damit gegen die von der VG WORT vorgegebene Mindestzeichenanzahl. Eine Zählmarke wurde dennoch zugeordnet.', WPVGW_TEXT_DOMAIN), esc_html($this->singularPostTypeName), number_format_i18n($minimumCharacterCount), number_format_i18n($this->markersManager->calculate_missing_character_count($postCharacterCount, $minimumCharacterCount))), 'warning');
         }
     }
     public function do_action(WP_Post $post)
     {
         if (!isset($_POST['_wpvgwpostviewnonce'])) {
             return;
         }
         if (!wp_verify_nonce($_POST['_wpvgwpostviewnonce'], 'postview')) {
             WPVGW_Helper::die_cheating();
         }
         $postTypeObject = get_post_type_object($post->post_type);
         if (!current_user_can($postTypeObject->cap->edit_post, $post->ID)) {
             WPVGW_Helper::die_cheating();
         }
         if (!$this->markersManager->is_user_allowed((int)$post->post_author)) {
             return;
         }
         $isMediaPostType = WPVGW_Helper::is_media($this->postType);
         $isMediaAttachedToPost = WPVGW_Helper::is_media_attached_to_post($post);
         $marker = $this->markersManager->get_marker_from_db($post->ID, 'post_id');
         $setMarker = isset($_POST['wpvgw_set_marker']);
         $isMarkerDisabled = isset($_POST['wpvgw_marker_disabled']);
         if (!$isMediaPostType && $marker === false && $this->options->get_post_view_set_marker_by_default()) {
             $isAutoMarkerDisabled = isset($_POST['wpvgw_is_auto_marker_disabled']);
             $this->postsExtras->insert_update_post_extras_in_db(array( 'post_id' => $post->ID, 'is_auto_marker_disabled' => $isAutoMarkerDisabled, ));
         }
         if ($setMarker) {
             $isAutoMarker = isset($_POST['wpvgw_auto_marker']);
             $publicMarker = isset($_POST['wpvgw_public_marker']) ? trim($_POST['wpvgw_public_marker']) : '';
             $privateMarker = isset($_POST['wpvgw_private_marker']) ? trim($_POST['wpvgw_private_marker']) : '';
             if (!$isMediaPostType) {
                 $this->options->set_post_view_auto_marker($isAutoMarker);
             }
             do {
                 if (!$isMediaPostType && $this->options->get_post_view_set_marker_for_published_only() && $post->post_status !== 'publish' && $post->post_status !== 'future') {
                     $this->add_admin_message(self::$noticesSlugs['PostNotPublished'], sprintf(__('Es wurde keine Zählmarke zugeordnet, da die %s noch nicht veröffentlicht wurde.', WPVGW_TEXT_DOMAIN), $this->singularPostTypeName), 'warning');
                     break;
                 }
                 if (!$isMediaPostType) {
                     $this->check_post_character_count($post);
                 }
                 if ($isAutoMarker) {
                     $marker = $this->markersManager->get_free_marker_from_db();
                     if ($marker === false) {
                         $marker = $this->markersManager->get_free_marker_from_db();
                     }
                     if ($marker === false) {
                         $this->add_admin_message(self::$noticesSlugs['NoFreeMarker'], sprintf(__('Eine Zählmarke konnte nicht automatisch zugeordnet werden, da für den Autor der %1$s keine mehr verfügbar sind. Bitte importieren Sie zunächst neue Zählmarken für den Autor der %1$s. %2$s', WPVGW_TEXT_DOMAIN), $this->singularPostTypeName, sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_ImportAdminView::get_slug_static())), __('Zählmarken hier importieren.', WPVGW_TEXT_DOMAIN))));
                     } else {
                         $this->create_error_from_update_result($this->markersManager->update_marker_in_db($marker['public_marker'], 'public_marker', array( 'post_id' => $post->ID, 'is_marker_disabled' => $isMarkerDisabled ), array( 'post_id' => null )), $this->singularPostTypeName);
                     }
                 } else {
                     if ($publicMarker !== '' && !WPVGW_MarkersManager::public_marker_validator($publicMarker)) {
                         $this->add_admin_message(self::$noticesSlugs['PublicMarkerInvalidFormat'], __('Die öffentliche Zählmarke hat ein ungültiges Format. Bitte nehmen Sie eine Korrektur vor.', WPVGW_TEXT_DOMAIN));
                         break;
                     }
                     if ($publicMarker === '') {
                         $publicMarker = null;
                     }
                     if ($privateMarker !== '' && !WPVGW_MarkersManager::private_marker_validator($privateMarker)) {
                         $this->add_admin_message(self::$noticesSlugs['PrivateMarkerInvalidFormat'], __('Die private Zählmarke hat ein ungültiges Format. Bitte nehmen Sie eine Korrektur vor.', WPVGW_TEXT_DOMAIN));
                         break;
                     }
                     if ($privateMarker === '') {
                         $privateMarker = null;
                     }
                     if ($publicMarker === null && $privateMarker === null) {
                         $this->add_admin_message(self::$noticesSlugs['PublicAndPrivateMarkerEmpty'], __('Öffentliche und private Zählmarke dürfen nicht gleichzeitig leer sein, da sonst keine Zählmarke zugeordnet werden kann.', WPVGW_TEXT_DOMAIN));
                         break;
                     }
                     $marker = array( 'post_id' => $post->ID, 'public_marker' => $publicMarker, 'private_marker' => $privateMarker, 'is_marker_disabled' => $isMarkerDisabled );
                     $updateResult = false;
                     if ($publicMarker !== null && $privateMarker !== null) {
                         $updateResult = $this->markersManager->update_marker_in_db($publicMarker, 'public_marker', $marker, array( 'private_marker' => $privateMarker ));
                     } elseif ($publicMarker !== null) {
                         unset($marker['private_marker']);
                         $updateResult = $this->markersManager->update_marker_in_db($publicMarker, 'public_marker', $marker);
                     } elseif ($privateMarker !== null) {
                         unset($marker['public_marker']);
                         $updateResult = $this->markersManager->update_marker_in_db($privateMarker, 'private_marker', $marker);
                     }
                     if ($updateResult !== false) {
                         $this->create_error_from_update_result($updateResult, $this->singularPostTypeName);
                     }
                 }
                 if ($isMediaPostType && !WPVGW_Helper::is_media_attached_to_post($post)) {
                     $this->add_admin_message(self::$noticesSlugs['MediaNotAttachedToPost'], __('Die Datei ist mit keiner Seite verknüpft, sodass die Zählmarke nicht ausgegeben wird. Es wurde möglicherweise dennoch eine Zählmarke zugeordnet.', WPVGW_TEXT_DOMAIN), 'warning');
                 }
             } while (false);
         } else {
             do {
                 $removePostFromMarker = isset($_POST['wpvgw_remove_post_from_marker']);
                 $marker = $this->markersManager->get_marker_from_db($post->ID, 'post_id');
                 if ($marker === false) {
                     break;
                 }
                 if ($removePostFromMarker) {
                     if (!current_user_can('manage_options')) {
                         WPVGW_Helper::die_cheating();
                     }
                     if (!$this->markersManager->remove_post_from_marker_in_db($post->ID)) {
                         $this->add_admin_message(self::$noticesSlugs['CouldNotRemoveMarkerFromPost'], sprintf(__('Die Zuordnung zwischen Zählmarke und %s konnte nicht aufgehoben werden.', WPVGW_TEXT_DOMAIN), $this->singularPostTypeName));
                     }
                     break;
                 }
                 if (!$isMediaPostType) {
                     $this->check_post_character_count($post);
                 }
                 $this->create_error_from_update_result($this->markersManager->update_marker_in_db($post->ID, 'post_id', array( 'is_marker_disabled' => $isMarkerDisabled )), $this->singularPostTypeName);
             } while (false);
         }
         $this->userOptions->set_edit_post_error_setting(get_settings_errors($this->errorSetting));
     }
 }
