<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_AboutAdminView extends WPVGW_AdminViewBase
 {
     public static function get_slug_static()
     {
         return 'about';
     }
     public static function get_long_name_static()
     {
         return __('Impressum Prosodia VGW OS', WPVGW_TEXT_DOMAIN);
     }
     public static function get_short_name_static()
     {
         return __('Impressum', WPVGW_TEXT_DOMAIN);
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
			<?php _e('Hier erfahren Sie, wer hinter dem Plugin steht. Es werden rechtliche Hinweise zum Plugin gegeben.', WPVGW_TEXT_DOMAIN); ?>
		</p>
		<table class="form-table wpvgw-form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e('Autor', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<a href="https://prosodia.de/">
								<img class="wpvgw-about-logo" src="<?php echo(WPVGW_PLUGIN_URL . '/images/prosodia-logo.png?v=2') ?>" alt="Prosodia – Verlag für Musik und Literatur"/>
							</a>
						</p>
						<p>
							<?php _e('Max Heckel, Ronny Harbich – Prosodia GbR<br/>Max Heckel z. Hd. Ronny Harbich<br/>Arneburger Straße 37T<br/>39590 Tangermünde', WPVGW_TEXT_DOMAIN); ?>
						</p>
						<p>
							<?php _e('E-Mail: <a href="mailto:info@prosodia.de">info@prosodia.de</a><br/>Website: <a href="https://prosodia.de/">prosodia.de</a>', WPVGW_TEXT_DOMAIN); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Lizenz', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Prosodia VGW OS wird von der Max Heckel, Ronny Harbich – Prosodia GbR unter der GPLv2-Lizenz vertrieben, die unter <a href="http://www.gnu.org/licenses/gpl-2.0.html">http://www.gnu.org/licenses/gpl-2.0.html</a> nachzulesen ist. Einschränkend gilt jedoch: Prosodia VGW OS darf nicht auf Websites verwendet werden, deren Inhalt – auch teilweise – Formen von Rechtspopulismus, Rechtsextremismus, Antisemitismus, fundamentalistischer Religiosität, Desinformation (u. a. „Fake News“), Kinderpornografie, Sexismus, Rassismus, Diskriminierung, verfassungsgemäß/gesetzlich Verbotenem einschließt.', WPVGW_TEXT_DOMAIN); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Haftungsausschluss', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Die Max Heckel, Ronny Harbich – Prosodia GbR übernimmt für Prosodia VGW OS keine Haftung außer die vom Bürgerlichen Gesetzbuch (BGB) zwingend erforderliche. Der Haftungsausschluss soll – soweit wie mit dem BGB vereinbar – der GPLv2-Lizenz entsprechen.', WPVGW_TEXT_DOMAIN); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Hinweis', WPVGW_TEXT_DOMAIN); ?></th>
					<td>
						<p>
							<?php _e('Prosodia VGW OS wird von der VG WORT weder unterstützt noch von ihr vertrieben.', WPVGW_TEXT_DOMAIN); ?>
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
