<?php
/**
 * Product: Prosodia VGW OS
 * URL: https://prosodia.de/
 * Author: Dr. Ronny Harbich
 * Copyright: Dr. Ronny Harbich
 * License: GPLv2 or later
 */
 class WPVGW_DataPrivacyAdminView extends WPVGW_AdminViewBase
 {
     protected $options;
     protected $userOptions;
     public static function get_slug_static()
     {
         return 'data-privacy';
     }
     public static function get_long_name_static()
     {
         return __('Datenschutz', WPVGW_TEXT_DOMAIN);
     }
     public static function get_short_name_static()
     {
         return __('Datenschutz', WPVGW_TEXT_DOMAIN);
     }
     public function __construct(WPVGW_Options $options, WPVGW_UserOptions $user_options)
     {
         parent::__construct(self::get_slug_static(), self::get_long_name_static(), self::get_short_name_static());
         $this->options = $options;
         $this->userOptions = $user_options;
     }
     public function init()
     {
         $this->init_base(array());
     }
     public function render()
     {
         $this->begin_render_base(); ?>
		<p class="wpvgw-admin-page-description">
			<?php _e('Hier können die Belange des Datenschutzes eingesehen und bestätigt werden.', WPVGW_TEXT_DOMAIN); ?>
		</p>
		<form method="post">
		<?php echo($this->get_wp_number_once_field()) ?>
			<table class="form-table wpvgw-form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e('VG-WORT-Datenschutz-Hinweis für Datenschutzerklärung', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<p class="wpvgw-admin-page-description">
								<?php _e('Sobald Zählmarken der VG WORT durch dieses Plugin verwendet werden, sollte der nachstehenden Datenschutz-Hinweis der VG WORT auf der Website eingefügt werden.', WPVGW_TEXT_DOMAIN); ?>
							</p>
							<div class="wpvgw-quote-text">
								<h2>Cookies und Meldungen zu Zugriffszahlen</h2>
								<p>
									Wir setzen „Session-Cookies“ der VG Wort, München, zur Messung von Zugriffen auf
									Texten ein, um die Kopierwahrscheinlichkeit zu erfassen. Session-Cookies sind kleine
									Informationseinheiten, die ein Anbieter im Arbeitsspeicher des Computers des
									Besuchers speichert. In einem Session-Cookie wird eine zufällig erzeugte eindeutige
									Identifikationsnummer abgelegt, eine sogenannte Session-ID. Außerdem enthält ein
									Cookie die Angabe über seine Herkunft und die Speicherfrist. Session-Cookies können
									keine anderen Daten speichern. Diese Messungen werden von der Kantar Germany GmbH
									nach dem Skalierbaren Zentralen Messverfahren (SZM) durchgeführt. Sie helfen dabei,
									die Kopierwahrscheinlichkeit einzelner Texte zur Vergütung von gesetzlichen
									Ansprüchen von Autoren und Verlagen zu ermitteln. Wir erfassen keine
									personenbezogenen Daten über Cookies.
								</p>
								<p>
									Viele unserer Seiten sind mit JavaScript-Aufrufen versehen, über die wir die
									Zugriffe an die Verwertungsgesellschaft Wort (VG Wort) melden.
									<span style="color: red;">[BITTE ÜBERPRÜFEN, ob dies bei Ihrem Verlag der Fall ist!]</span>
									Wir ermöglichen damit, dass unsere Autoren an den Ausschüttungen der VG Wort
									partizipieren, die die gesetzliche Vergütung für die Nutzungen urheberrechtlich
									geschützter Werke gem. § 53 UrhG sicherstellen.
								</p>
								<p>
									Eine Nutzung unserer Angebote ist auch ohne Cookies möglich. Die meisten Browser
									sind so eingestellt, dass sie Cookies automatisch akzeptieren. Sie können das
									Speichern von Cookies jedoch deaktivieren oder Ihren Browser so einstellen, dass er
									Sie benachrichtigt, sobald Cookies gesendet werden.
								</p>
								<h2>Datenschutzerklärung zur Nutzung des Skalierbaren Zentralen Messverfahrens</h2>
								<p>
									Unsere Website und unser mobiles Webangebot nutzen das „Skalierbare Zentrale
									Messverfahren“ (SZM) der Kantar Germany GmbH für die Ermittlung statistischer
									Kennwerte zur Ermittlung der Kopierwahrscheinlichkeit von Texten.
								</p>
								<p>
									Dabei werden anonyme Messwerte erhoben. Die Zugriffszahlenmessung verwendet zur
									Wiedererkennung von Computersystemen alternativ ein Session-Cookie oder eine
									Signatur, die aus verschiedenen automatisch übertragenen Informationen Ihres
									Browsers erstellt wird. IP-Adressen werden nur in anonymisierter Form verarbeitet.
								</p>
								<p>
									Das Verfahren wurde unter der Beachtung des Datenschutzes entwickelt. Einziges Ziel
									des Verfahrens ist es, die Kopierwahrscheinlichkeit einzelner Texte zu ermitteln.
								</p>
								<p>
									Zu keinem Zeitpunkt werden einzelne Nutzer identifiziert. Ihre Identität bleibt
									immer geschützt. Sie erhalten über das System keine Werbung.
								</p>
							</div>
							<p>
								<?php echo(sprintf(__('Quelle: %s', WPVGW_TEXT_DOMAIN), sprintf('<a href="https://tom.vgwort.de/portal/showParticipationCondition">%s</a>', __('Teilnahmebedingungen für T.O.M. dem Online Meldesystem der VG WORT (Stand Januar 2021)', WPVGW_TEXT_DOMAIN)))) ?>
							</p>
							<p>
								<?php _e('Die Autoren dieses Plugins übernehmen keine Haftung für die Korrektheit und Aktualität des zitierten Datenschutz-Hinweises.', WPVGW_TEXT_DOMAIN); ?>
							</p>
							<p>
								<input type="checkbox" name="wpvgw_privacy_hide_warning" id="wpvgw_privacy_hide_warning" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->options->get_privacy_hide_warning())) ?>/>
								<label for="wpvgw_privacy_hide_warning"><?php _e('Datenschutz-Hinweis zur Kenntnis genommen und ggf. auf der Website eingefügt', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Es wird keine Warnung mehr im Administrationsbereich angezeigt, wenn aktiviert.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Datenschutzerklärung des Plugins', WPVGW_TEXT_DOMAIN); ?></th>
						<td>
							<div class="wpvgw-quote-text">
								<h1 id="datenschutz">Datenschutzerklärung</h1>
								<h2>Begriffsbestimmungen</h2>
								<p>Die im Folgenden verwendeten Begriffe sind unter anderem die aus Art. 4 <a href="https://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016R0679&amp;from=EN">Datenschutz-Grundverordnung (DSGVO)</a>.</p>
								<h2>Name und Anschrift des Verantwortlichen</h2>
								<p>
									Der Verantwortliche im Sinne der Datenschutz-Grundverordnung (DSGVO) und anderer nationaler Datenschutzgesetze der Mitgliedsstaaten sowie sonstiger datenschutzrechtlicher Bestimmungen ist der im
									<?php
 echo(sprintf('<a href="%s">%s</a>', esc_attr(WPVGW_AdminViewsManger::create_admin_view_url(WPVGW_AboutAdminView::get_slug_static())), __('Impressum angegeben Hersteller', WPVGW_TEXT_DOMAIN))); ?>
									dieses Plugins.
								</p>
								<h2>Allgemeines zur Datenverarbeitung</h2>
								<h3>Umfang der Verarbeitung personenbezogener Daten</h3>
								<p>Wir verarbeiten personenbezogene Daten unserer Nutzer grundsätzlich nur, soweit dies zur Bereitstellung eines funktionsfähigen Plugins erforderlich ist. Die Verarbeitung personenbezogener Daten unserer Nutzer erfolgt regelmäßig nur nach Einwilligung des Nutzers. Eine Ausnahme gilt in solchen Fällen, in denen eine vorherige Einholung einer Einwilligung aus tatsächlichen Gründen nicht möglich ist und die Verarbeitung der Daten durch gesetzliche Vorschriften gestattet ist.</p>
								<h3>Rechtsgrundlage für die Verarbeitung personenbezogener Daten</h3>
								<p>Soweit wir für Verarbeitungsvorgänge personenbezogener Daten eine Einwilligung der betroffenen Person einholen, dient Art. 6 Abs. 1 lit. a <a href="https://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016R0679&amp;from=EN">EU-Datenschutzgrundverordnung (DSGVO)</a> als Rechtsgrundlage.</p>
								<p>Bei der Verarbeitung von personenbezogenen Daten, die zur Erfüllung eines Vertrages, dessen Vertragspartei die betroffene Person ist, erforderlich ist, dient Art. 6 Abs. 1 lit. b DSGVO als Rechtsgrundlage. Dies gilt auch für Verarbeitungsvorgänge, die zur Durchführung vorvertraglicher Maßnahmen erforderlich sind.</p>
								<p>Soweit eine Verarbeitung personenbezogener Daten zur Erfüllung einer rechtlichen Verpflichtung erforderlich ist, der unser Unternehmen unterliegt, dient Art. 6 Abs. 1 lit. c DSGVO als Rechtsgrundlage.</p>
								<p>Für den Fall, dass lebenswichtige Interessen der betroffenen Person oder einer anderen natürlichen Person eine Verarbeitung personenbezogener Daten erforderlich machen, dient Art. 6 Abs. 1 lit. d DSGVO als Rechtsgrundlage.</p>
								<p>Ist die Verarbeitung zur Wahrung eines berechtigten Interesses unseres Unternehmens oder eines Dritten erforderlich und überwiegen die Interessen, Grundrechte und Grundfreiheiten des Betroffenen das erstgenannte Interesse nicht, so dient Art. 6 Abs. 1 lit. f DSGVO als Rechtsgrundlage für die Verarbeitung.</p>
								<h3>Datenlöschung und Speicherdauer</h3>
								<p>Die personenbezogenen Daten der betroffenen Person werden gelöscht oder gesperrt, sobald der Zweck der Speicherung entfällt. Eine Speicherung kann darüber hinaus erfolgen, wenn dies durch den europäischen oder nationalen Gesetzgeber in unionsrechtlichen Verordnungen, Gesetzen oder sonstigen Vorschriften, denen der Verantwortliche unterliegt, vorgesehen wurde. Eine Sperrung oder Löschung der Daten erfolgt auch dann, wenn eine durch die genannten Normen vorgeschriebene Speicherfrist abläuft, es sei denn, dass eine Erforderlichkeit zur weiteren Speicherung der Daten für einen Vertragsabschluss oder eine Vertragserfüllung besteht.</p>
								<h3>Datenübertragung</h3>
								<p>Die Datenübertragung im Internet zu unserem Server erfolgt über HTTPS- und TLS-Protokoll in verschlüsselter Art und Weise. Eine unverschlüsselte Übertragung ist seitens unseres Servers nicht möglich. Die Daten werden von unserem Plugin oder Ihrem Webbrowser verschlüsselt, dann übertragen und schließlich von unserem Server entschlüsselt.</p>
								<h2>Bereitstellung des Plugins</h2>
								<h3>Beschreibung und Umfang der Datenverarbeitung</h3>
								<p>Das Plugin sendet ohne Einwilligung des Nutzers keine Daten an den Hersteller. Daten, die an den Hersteller gesendet werden können, werden im Folgenden aufgeführt. Das Plugin erstellt und sammelt keine Nutzungsdaten.</p>
								<h2>Prüfen-Funktion für Zählmarken</h2>
								<h3>Beschreibung und Umfang der Datenverarbeitung</h3>
								<p>Sie haben die Möglichkeit die vom Plugin auf Ihrer Website eingebundenen Zählmarken von uns prüfen zu lassen. Dies geschieht über den Link „Prüfen“, der an mehreren Stellen im Plugin zu finden ist. Klicken Sie auf diesen Prüf-Link, werden an unseren Server folgende Daten gesendet:</p>
								<ul>
									<li>durch Ihren Zugriff mit einen Webbrowser:
										<ul>
											<li>Informationen über den Webbrowsertyp und die verwendete Version,</li>
											<li>das Betriebssystem des Nutzers,</li>
											<li>die IP-Adresse des Nutzers,</li>
											<li>Datum und Uhrzeit des Zugriffs,</li>
											<li>die Webseite, von denen das System des Nutzers auf unseren Server gelangt,</li>
										</ul>
									</li>
									<li>im Prüf-Link enthaltene Daten:
										<ul>
											<li>die öffentliche Zählmarke,</li>
											<li>der Link zur Webseite (WordPress-Seite), auf dem sich die Zählmarke befinden sollte.</li>
										</ul>
									</li>
								</ul>
								<p>Die Daten, die durch Ihren Zugriff mit einen Webbrowser an gesendet werden, werden nur in den Logfiles unseres Systems gespeichert, wenn bei dem entsprechenden Seitenaufruf ein Fehler auftritt. Nicht hiervon betroffen ist die IP-Adresse des Nutzers oder andere Daten, die die Zuordnung der Daten zu einem Nutzer ermöglichen.</p>
								<p>Die im Prüf-Link enthaltenen Daten werden von unserem Server verwendet, um zu testen, ob sich die öffentliche Zählmarke auf der im Prüf-Link enthaltenen Webseite (WordPress-Seite) befindet.</p>
								<p>Die IP-Adresse des Nutzers speichern wir temporär ausschließlich dafür, um festzustellen, wie oft der Nutzer die Prüfen-Funktion innerhalb eines Zeitintervalls nutzt.</p>
								<p>Eine Speicherung dieser Daten zusammen mit anderen personenbezogenen Daten des Nutzers findet nicht statt.</p>
								<h3>Rechtsgrundlage für die Datenverarbeitung</h3>
								<p>Rechtsgrundlage für die vorübergehende Speicherung der Daten ist Art. 6 Abs. 1 lit. f bzw. a DSGVO.</p>
								<h3>Zweck der Datenverarbeitung</h3>
								<p>Die vorübergehende Speicherung der IP-Adresse durch den Server ist notwendig, um eine Auslieferung der aufgerufenen Webseite an den Rechner des Nutzers zu ermöglichen. Hierfür muss die IP-Adresse des Nutzers für die Dauer der Sitzung gespeichert bleiben.</p>
								<p>Die vorübergehende Speicherung der Daten im Prüf-Link ist erforderlich, um die Prüfung durchzuführen.</p>
								<p>Die IP-Adresse des Nutzers speichern wir temporär ausschließlich zum Schutz unserer Prüfen-Funktion vor Missbrauch (übermäßige Nutzung).</p>
								<h3>Dauer der Speicherung</h3>
								<p>Die Daten werden gelöscht, sobald sie für die Erreichung des Zweckes ihrer Erhebung nicht mehr erforderlich sind. Im Falle der Erfassung der Daten zur Bereitstellung der Prüfen-Funktion für Zählmarken ist dies der Fall, wenn die jeweilige Sitzung beendet ist. Es findet keine dauerhafte Speicherung statt.</p>
								<p>Die IP-Adresse des Nutzers speichern wir des Schutzes unseres Systems wegen maximal einen Tag.</p>
								<h3>Widerspruchs- und Beseitigungsmöglichkeit</h3>
								<p>Die Speicherung der Daten in Logfiles im Falle eines Fehlers ist für den Betrieb der Prüfen-Funktion für Zählmarken zwingend erforderlich. Es besteht folglich seitens des Nutzers keine Widerspruchsmöglichkeit.</p>
								<p>Die Speicherung der IP-Adresse des Nutzers ist zum Schutzes unseres System zwingend erforderlich. Es besteht folglich seitens des Nutzers keine Widerspruchsmöglichkeit.</p>
								<p>Der Nutzer kann der Nutzung der Prüfen-Funktion für Zählmarken über ein Kontrollkästchen im Bereich/Menü „Datenschutz“ aktivieren, um sie nutzen zu können, oder deaktivieren, um der Nutzung zu widersprechen. Standardmäßig ist dieses Kontrollkästchen deaktiviert.</p>
								<h2>Rechte der betroffenen Person</h2>
								<p>Werden personenbezogene Daten von Ihnen verarbeitet, sind Sie Betroffener im Sinne der DSGVO und es stehen Ihnen folgende Rechte gegenüber dem Verantwortlichen zu:</p>
								<h3>Auskunftsrecht</h3>
								<p>Sie können von dem Verantwortlichen eine Bestätigung darüber verlangen, ob personenbezogene Daten, die Sie betreffen, von uns verarbeitet werden.  Liegt eine solche Verarbeitung vor, können Sie von dem Verantwortlichen über folgende Informationen Auskunft verlangen:</p>
								<ul>
									<li>die Zwecke, zu denen die personenbezogenen Daten verarbeitet werden,</li>
									<li>die Kategorien von personenbezogenen Daten, welche verarbeitet werden,</li>
									<li>die Empfänger bzw. die Kategorien von Empfängern, gegenüber denen die Sie betreffenden personenbezogenen Daten offengelegt wurden oder noch offengelegt werden,</li>
									<li>die geplante Dauer der Speicherung der Sie betreffenden personenbezogenen Daten oder, falls konkrete Angaben hierzu nicht möglich sind, Kriterien für die Festlegung der Speicherdauer,</li>
									<li>das Bestehen eines Rechts auf Berichtigung oder Löschung der Sie betreffenden personenbezogenen Daten, eines Rechts auf Einschränkung der Verarbeitung durch den Verantwortlichen oder eines Widerspruchsrechts gegen diese Verarbeitung,</li>
									<li>das Bestehen eines Beschwerderechts bei einer Aufsichtsbehörde,</li>
									<li>alle verfügbaren Informationen über die Herkunft der Daten, wenn die personenbezogenen Daten nicht bei der betroffenen Person erhoben werden,</li>
									<li>das Bestehen einer automatisierten Entscheidungsfindung einschließlich Profiling gemäß Art. 22 Abs. 1 und 4 DSGVO und – zumindest in diesen Fällen – aussagekräftige Informationen über die involvierte Logik sowie die Tragweite und die angestrebten Auswirkungen einer derartigen Verarbeitung für die betroffene Person.</li>
								</ul>
								<p>Ihnen steht das Recht zu, Auskunft darüber zu verlangen, ob die Sie betreffenden personenbezogenen Daten in ein Drittland oder an eine internationale Organisation übermittelt werden. In diesem Zusammenhang können Sie verlangen, über die geeigneten Garantien gem. Art. 46 DSGVO im Zusammenhang mit der Übermittlung unterrichtet zu werden.</p>
								<h3>Recht auf Berichtigung</h3>
								<p>Sie haben ein Recht auf Berichtigung und/oder Vervollständigung gegenüber dem Verantwortlichen, sofern die verarbeiteten personenbezogenen Daten, die Sie betreffen, unrichtig oder unvollständig sind. Der Verantwortliche hat die Berichtigung unverzüglich vorzunehmen.</p>
								<h3>Recht auf Einschränkung der Verarbeitung</h3>
								<p>Unter den folgenden Voraussetzungen können Sie die Einschränkung der Verarbeitung der Sie betreffenden personenbezogenen Daten verlangen:</p>
								<ul>
									<li>wenn Sie die Richtigkeit der Sie betreffenden personenbezogenen für eine Dauer bestreiten, die es dem Verantwortlichen ermöglicht, die Richtigkeit der personenbezogenen Daten zu überprüfen,</li>
									<li>die Verarbeitung unrechtmäßig ist und Sie die Löschung der personenbezogenen Daten ablehnen und stattdessen die Einschränkung der Nutzung der personenbezogenen Daten verlangen,</li>
									<li>der Verantwortliche die personenbezogenen Daten für die Zwecke der Verarbeitung nicht länger benötigt, Sie diese jedoch zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen benötigen, oder</li>
									<li>wenn Sie Widerspruch gegen die Verarbeitung gemäß Art. 21 Abs. 1 DSGVO eingelegt haben und noch nicht feststeht, ob die berechtigten Gründe des Verantwortlichen gegenüber Ihren Gründen überwiegen.</li>
								</ul>
								<p>Wurde die Verarbeitung der Sie betreffenden personenbezogenen Daten eingeschränkt, dürfen diese Daten – von ihrer Speicherung abgesehen – nur mit Ihrer Einwilligung oder zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen oder zum Schutz der Rechte einer anderen natürlichen oder juristischen Person oder aus Gründen eines wichtigen öffentlichen Interesses der Union oder eines Mitgliedstaats verarbeitet werden.</p>
								<p>Wurde die Einschränkung der Verarbeitung nach den oben genannten Voraussetzungen eingeschränkt, werden Sie von dem Verantwortlichen unterrichtet bevor die Einschränkung aufgehoben wird.</p>
								<h3>Recht auf Löschung</h3>
								<p>Löschungspflicht:</p>
								<p>Sie können von dem Verantwortlichen verlangen, dass die Sie betreffenden personenbezogenen Daten unverzüglich gelöscht werden, und der Verantwortliche ist verpflichtet, diese Daten unverzüglich zu löschen, sofern einer der folgenden Gründe zutrifft:</p>
								<ul>
									<li>die Sie betreffenden personenbezogenen Daten sind für die Zwecke, für die sie erhoben oder auf sonstige Weise verarbeitet wurden, nicht mehr notwendig,</li>
									<li>Sie widerrufen Ihre Einwilligung, auf die sich die Verarbeitung gem. Art. 6 Abs. 1 lit. a oder Art. 9 Abs. 2 lit. a DSGVO stützte, und es fehlt an einer anderweitigen Rechtsgrundlage für die Verarbeitung,</li>
									<li>Sie legen gem. Art. 21 Abs. 1 DSGVO Widerspruch gegen die Verarbeitung ein und es liegen keine vorrangigen berechtigten Gründe für die Verarbeitung vor, oder Sie legen gem. Art. 21 Abs. 2 DSGVO Widerspruch gegen die Verarbeitung ein,</li>
									<li>die Sie betreffenden personenbezogenen Daten wurden unrechtmäßig verarbeitet,</li>
									<li>die Löschung der Sie betreffenden personenbezogenen Daten ist zur Erfüllung einer rechtlichen Verpflichtung nach dem Unionsrecht oder dem Recht der Mitgliedstaaten erforderlich, dem der Verantwortliche unterliegt,</li>
									<li>die Sie betreffenden personenbezogenen Daten wurden in Bezug auf angebotene Dienste der Informationsgesellschaft gemäß Art. 8 Abs. 1 DSGVO erhoben.</li>
								</ul>
								<p>Information an Dritte:</p>
								<p>Hat der Verantwortliche die Sie betreffenden personenbezogenen Daten öffentlich gemacht und ist er gem. Art. 17 Abs. 1 DSGVO zu deren Löschung verpflichtet, so trifft er unter Berücksichtigung der verfügbaren Technologie und der Implementierungskosten angemessene Maßnahmen, auch technischer Art, um für die Datenverarbeitung Verantwortliche, die die personenbezogenen Daten verarbeiten, darüber zu informieren, dass Sie als betroffene Person von ihnen die Löschung aller Links zu diesen personenbezogenen Daten oder von Kopien oder Replikationen dieser personenbezogenen Daten verlangt haben.</p>
								<p>Ausnahmen:</p>
								<p>Das Recht auf Löschung besteht nicht, soweit die Verarbeitung erforderlich ist:</p>
								<ul>
									<li>zur Ausübung des Rechts auf freie Meinungsäußerung und Information,</li>
									<li>zur Erfüllung einer rechtlichen Verpflichtung, die die Verarbeitung nach dem Recht der Union oder der Mitgliedstaaten, dem der Verantwortliche unterliegt, erfordert, oder zur Wahrnehmung einer Aufgabe, die im öffentlichen Interesse liegt oder in Ausübung öffentlicher Gewalt erfolgt, die dem Verantwortlichen übertragen wurde,</li>
									<li>aus Gründen des öffentlichen Interesses im Bereich der öffentlichen Gesundheit gemäß Art. 9 Abs. 2 lit. h und i sowie Art. 9 Abs. 3 DSGVO,</li>
									<li>für im öffentlichen Interesse liegende Archivzwecke, wissenschaftliche oder historische Forschungszwecke oder für statistische Zwecke gem. Art. 89 Abs. 1 DSGVO, soweit das unter Abschnitt a) genannte Recht voraussichtlich die Verwirklichung der Ziele dieser Verarbeitung unmöglich macht oder ernsthaft beeinträchtigt, oder</li>
									<li>zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen.</li>
								</ul>
								<h3>Recht auf Unterrichtung</h3>
								<p>Haben Sie das Recht auf Berichtigung, Löschung oder Einschränkung der Verarbeitung gegenüber dem Verantwortlichen geltend gemacht, ist dieser verpflichtet, allen Empfängern, denen die Sie betreffenden personenbezogenen Daten offengelegt wurden, diese Berichtigung oder Löschung der Daten oder Einschränkung der Verarbeitung mitzuteilen, es sei denn, dies erweist sich als unmöglich oder ist mit einem unverhältnismäßigen Aufwand verbunden.</p>
								<p>Ihnen steht gegenüber dem Verantwortlichen das Recht zu, über diese Empfänger unterrichtet zu werden.</p>
								<h3>Recht auf Datenübertragbarkeit</h3>
								<p>Sie haben das Recht, die Sie betreffenden personenbezogenen Daten, die Sie dem Verantwortlichen bereitgestellt haben, in einem strukturierten, gängigen und maschinenlesbaren Format zu erhalten. Außerdem haben Sie das Recht diese Daten einem anderen Verantwortlichen ohne Behinderung durch den Verantwortlichen, dem die personenbezogenen Daten bereitgestellt wurden, zu übermitteln, sofern</p>
								<ul>
									<li>die Verarbeitung auf einer Einwilligung gem. Art. 6 Abs. 1 lit. a DSGVO oder Art. 9 Abs. 2 lit. a DSGVO oder auf einem Vertrag gem. Art. 6 Abs. 1 lit. b DSGVO beruht und</li>
									<li>die Verarbeitung mithilfe automatisierter Verfahren erfolgt.</li>
								</ul>
								<p>In Ausübung dieses Rechts haben Sie ferner das Recht, zu erwirken, dass die Sie betreffenden personenbezogenen Daten direkt von einem Verantwortlichen einem anderen Verantwortlichen übermittelt werden, soweit dies technisch machbar ist. Freiheiten und Rechte anderer Personen dürfen hierdurch nicht beeinträchtigt werden.</p>
								<p>Das Recht auf Datenübertragbarkeit gilt nicht für eine Verarbeitung personenbezogener Daten, die für die Wahrnehmung einer Aufgabe erforderlich ist, die im öffentlichen Interesse liegt oder in Ausübung öffentlicher Gewalt erfolgt, die dem Verantwortlichen übertragen wurde.</p>
								<h3>Widerspruchsrecht</h3>
								<p>Sie haben das Recht, aus Gründen, die sich aus ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung der Sie betreffenden personenbezogenen Daten, die aufgrund von Art. 6 Abs. 1 lit. e oder f DSGVO erfolgt, Widerspruch einzulegen; dies gilt auch für ein auf diese Bestimmungen gestütztes Profiling.</p>
								<p>Der Verantwortliche verarbeitet die Sie betreffenden personenbezogenen Daten nicht mehr, es sei denn, er kann zwingende schutzwürdige Gründe für die Verarbeitung nachweisen, die Ihre Interessen, Rechte und Freiheiten überwiegen, oder die Verarbeitung dient der Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen.</p>
								<p>Werden die Sie betreffenden personenbezogenen Daten verarbeitet, um Direktwerbung zu betreiben, haben Sie das Recht, jederzeit Widerspruch gegen die Verarbeitung der Sie betreffenden personenbezogenen Daten zum Zwecke derartiger Werbung einzulegen; dies gilt auch für das Profiling, soweit es mit solcher Direktwerbung in Verbindung steht.</p>
								<p>Widersprechen Sie der Verarbeitung für Zwecke der Direktwerbung, so werden die Sie betreffenden personenbezogenen Daten nicht mehr für diese Zwecke verarbeitet.</p>
								<p>Sie haben die Möglichkeit, im Zusammenhang mit der Nutzung von Diensten der Informationsgesellschaft – ungeachtet der Richtlinie 2002/58/EG – Ihr Widerspruchsrecht mittels automatisierter Verfahren auszuüben, bei denen technische Spezifikationen verwendet werden.</p>
								<h3>Recht auf Widerruf der datenschutzrechtlichen Einwilligungserklärung</h3>
								<p>Sie haben das Recht, Ihre datenschutzrechtliche Einwilligungserklärung jederzeit zu widerrufen. Durch den Widerruf der Einwilligung wird die Rechtmäßigkeit der aufgrund der Einwilligung bis zum Widerruf erfolgten Verarbeitung nicht berührt.</p>
								<h3>Automatisierte Entscheidung im Einzelfall einschließlich Profiling</h3>
								<p>Sie haben das Recht, nicht einer ausschließlich auf einer automatisierten Verarbeitung – einschließlich Profiling – beruhenden Entscheidung unterworfen zu werden, die Ihnen gegenüber rechtliche Wirkung entfaltet oder Sie in ähnlicher Weise erheblich beeinträchtigt. Dies gilt nicht, wenn die Entscheidung</p>
								<ul>
									<li>für den Abschluss oder die Erfüllung eines Vertrags zwischen Ihnen und dem Verantwortlichen erforderlich ist,</li>
									<li>aufgrund von Rechtsvorschriften der Union oder der Mitgliedstaaten, denen der Verantwortliche unterliegt, zulässig ist und diese Rechtsvorschriften angemessene Maßnahmen zur Wahrung Ihrer Rechte und Freiheiten sowie Ihrer berechtigten Interessen enthalten oder</li>
									<li>mit Ihrer ausdrücklichen Einwilligung erfolgt.</li>
								</ul>
								<p>Allerdings dürfen diese Entscheidungen nicht auf besonderen Kategorien personenbezogener Daten nach Art. 9 Abs. 1 DSGVO beruhen, sofern nicht Art. 9 Abs. 2 lit. a oder g DSGVO gilt und angemessene Maßnahmen zum Schutz der Rechte und Freiheiten sowie Ihrer berechtigten Interessen getroffen wurden.</p>
								<p>Hinsichtlich der zuvor genannten Fälle trifft der Verantwortliche angemessene Maßnahmen, um die Rechte und Freiheiten sowie Ihre berechtigten Interessen zu wahren, wozu mindestens das Recht auf Erwirkung des Eingreifens einer Person seitens des Verantwortlichen, auf Darlegung des eigenen Standpunkts und auf Anfechtung der Entscheidung gehört.</p>
								<h3>Recht auf Beschwerde bei einer Aufsichtsbehörde</h3>
								<p>Unbeschadet eines anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs steht Ihnen das Recht auf Beschwerde bei einer Aufsichtsbehörde, insbesondere in dem Mitgliedstaat ihres Aufenthaltsorts, ihres Arbeitsplatzes oder des Orts des mutmaßlichen Verstoßes, zu, wenn Sie der Ansicht sind, dass die Verarbeitung der Sie betreffenden personenbezogenen Daten gegen die DSGVO verstößt.</p>
								<p>Die Aufsichtsbehörde, bei der die Beschwerde eingereicht wurde, unterrichtet den Beschwerdeführer über den Stand und die Ergebnisse der Beschwerde einschließlich der Möglichkeit eines gerichtlichen Rechtsbehelfs nach Art. 78 DSGVO.</p>
							</div>
							<p>
								<input type="checkbox" name="wpvgw_privacy_allow_test_marker" id="wpvgw_privacy_allow_test_marker" value="1" class="checkbox" <?php echo(WPVGW_Helper::get_html_checkbox_checked($this->userOptions->get_privacy_allow_test_marker())) ?>/>
								<label for="wpvgw_privacy_allow_test_marker"><?php _e('Ich habe die Datenschutzerklärung gelesen und willige ein, dass die genannten Daten für die Prüfen-Funktion für Zählmarken verarbeitet werden dürfen', WPVGW_TEXT_DOMAIN); ?></label>
								<span class="description wpvgw-description">
									<?php _e('Wenn Sie die Prüfen-Funktion für Zählmarken verwenden wollen, müssen Sie die Datenschutzerklärung lesen und einwilligen, dass die dort genannten Daten für die Prüfen-Funktion für Zählmarken an unseren Server übertragen werden dürfen.', WPVGW_TEXT_DOMAIN) ?>
								</span>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="wpvgw_privacy" value="<?php _e('Einstellung speichern', WPVGW_TEXT_DOMAIN); ?>" class="button-primary"/>
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
         $hidePrivacyWarning = isset($_POST['wpvgw_privacy_hide_warning']);
         $this->options->set_privacy_hide_warning($hidePrivacyWarning);
         $privacyAllowTestMarker = isset($_POST['wpvgw_privacy_allow_test_marker']);
         $this->userOptions->set_privacy_allow_test_marker($privacyAllowTestMarker);
         $this->add_admin_message(__('Einstellungen erfolgreich übernommen.', WPVGW_TEXT_DOMAIN), WPVGW_ErrorType::Update);
     }
 }
