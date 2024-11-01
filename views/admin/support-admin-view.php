<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_SupportAdminView extends WPVGW_AdminViewBase
 {
     public static function get_slug_static()
     {
         return 'support';
     }
     public static function get_long_name_static()
     {
         return __('Hilfe und Anleitungen', WPVGW_TEXT_DOMAIN);
     }
     public static function get_short_name_static()
     {
         return __('Hilfe/Anleitungen', WPVGW_TEXT_DOMAIN);
     }
     public function __construct()
     {
         parent::__construct(self::get_slug_static(), self::get_long_name_static(), self::get_short_name_static());
     }
     public function init()
     {
         $this->init_base(array());
     }
     public function render()
     {
         $this->begin_render_base(); ?>
		<p class="wpvgw-admin-page-description">
			<?php _e('Hier können Sie Hilfe und Anleitungen zum Plugin erhalten.', WPVGW_TEXT_DOMAIN); ?>
		</p>
		<table class="form-table wpvgw-form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e('Anleitungen', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Unter <a href="https://prosodia.de/prosodia-vgw-os/">https://prosodia.de/prosodia-vgw-os/</a> gibt es bebilderte Anleitungen zum Plugin.', WPVGW_TEXT_DOMAIN); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Anleitungen VG WORT', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Unter <a href="https://tom.vgwort.de/Documents/pdfs/dokumentation/metis/DOC_Urhebermeldung.pdf">https://tom.vgwort.de/Documents/pdfs/dokumentation/metis/DOC_Urhebermeldung.pdf</a> gibt es die „Systembeschreibung für Urheber“.', WPVGW_TEXT_DOMAIN); ?>
						</p>
						<p>
							<?php _e('Unter <a href="https://tom.vgwort.de/Documents/pdfs/dokumentation/metis/DOC_Verlagsmeldung.pdf">https://tom.vgwort.de/Documents/pdfs/dokumentation/metis/DOC_Verlagsmeldung.pdf</a> gibt es die „Integrationsbeschreibung für Verlage“.', WPVGW_TEXT_DOMAIN); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Neue Funktionen und Hilfe', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Wenn Sie einen Wunsch für eine neue Funktion haben oder einfach nur Hilfe benötigen, treten Sie bitte mit uns in Kontakt:', WPVGW_TEXT_DOMAIN); ?>
						</p>
						<p>
							<?php echo(sprintf(__('Websites: %s und %s (WordPress.org)', WPVGW_TEXT_DOMAIN), sprintf('<a href="https://wordpress.org/plugins/wp-vgwort/faq/" target="_blank">%s</a>', __('FAQ', WPVGW_TEXT_DOMAIN)), sprintf('<a href="https://wordpress.org/support/plugin/wp-vgwort" target="_blank">%s</a>', __('Support', WPVGW_TEXT_DOMAIN)))) ?>
						</p>
						<p>
							<?php echo(sprintf(__('E-Mail: %s', WPVGW_TEXT_DOMAIN), '<a href="mailto:developer@prosodia.de">developer@prosodia.de</a>')) ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Fehler melden', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Wenn Sie einen kritischen Fehler im Plugin entdeckt haben, können Sie sich auch direkt an die Entwickler wenden:', WPVGW_TEXT_DOMAIN); ?>
						</p>
						<p>
							<?php echo(sprintf(__('E-Mail: %s', WPVGW_TEXT_DOMAIN), '<a href="mailto:developer@prosodia.de">developer@prosodia.de</a>')) ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
 $this->end_render_base();
     }
     public function do_action()
     {
         if (!$this->do_action_base()) {
             return;
         }
     }
 }
