=== Prosodia VGW OS ===
Contributors: raubvogel, smoo1337
Donate link: https://prosodia.de/
Tags: VG WORT, Zählmarke, Geld, Beitrag, T.O.M., Zählpixel, VGW, Verwertungsgesellschaft WORT, Prosodia
Requires at least: 5.0
Tested up to: 6.3.1
Requires PHP: 7.0
Stable tag: 3.25.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Verdienen Sie mit Ihren Beiträgen/Texten Geld durch die Integration von Zählmarken der VG WORT.

== Description ==

> <strong>Anleitungen</strong><br>
> Unter [https://prosodia.de/prosodia-vgw-os/](https://prosodia.de/prosodia-vgw-os/) gibt es Anleitungen zum Plugin.


Das Plugin *Prosodia VGW OS* (open source) vom Literaturverlag [Prosodia](https://prosodia.de/) und die [Verwertungsgesellschaft WORT (VG WORT)](https://www.vgwort.de/die-vg-wort.html) ermöglichen es Ihnen, *jährlich Geld* mit Ihren in WordPress geschriebenen Beiträgen zu verdienen. Die VG WORT ist ein deutscher, rechtsfähiger Verein, in dem sich Autoren zur gemeinsamen Verwertung von Urheberrechten zusammengeschlossen haben. Derart können Sie mit Ihren [Texten im Internet (Beiträge)](https://www.vgwort.de/verguetungen/auszahlungen/texte-im-internet.html), die aus mindestens *1.800 Zeichen* bestehen, von der Verwertung (Geld, Tantiemen) Gebrauch machen. Dazu ist eine kostenlose Mitgliedschaft bei der VG WORT erforderlich: [kostenlose Anmeldung](https://tom.vgwort.de/portal/login). Jeder Beitrag, für den Sie jährlich Geld bekommen wollen, muss eine gewissen Zahl an Lesern/Zugriffen aufweisen. Die Anzahl der Zugriffe für einen Beitrag wird von der VG WORT über *Zählmarken* ermittelt. Dabei wird jedem Beitrag ein eindeutiger Code – eine Zählmarke – zugeordnet. Besucht ein Leser einen Beitrag, wird die dem Beitrag zugeordnete Zählmarke an die VG WORT übermittelt und somit die Gesamtzahl der Leser für diesen Beitrag erhöht. Einmal im Jahr wird dann *pro* Beitrag Geld an Sie ausgeschüttet, abhängig davon wie hoch die Leserzahl eines Beitrags im vorherigen Jahr gewesen ist – die notwendige Mindestzahl wird von der VG WORT festgelegt (siehe auch [Wikipedia](https://de.wikipedia.org/wiki/Meldesystem_f%C3%BCr_Texte_auf_Internetseiten)). Je nach dem wie viele Beiträge Sie verfasst haben und wie häufig diese gelesen wurden, ist ein Verdienst von jährlich mehreren hundert bis tausend Euro durchaus nicht utopisch.

Die Hauptaufgabe von Prosodia VGW OS ist, Zählmarken und deren Zuordnungen zu Beiträgen bequem für Sie im WordPress-Administrationsbereich zu verwalten und sicherzustellen, dass keine Zählmarken verloren gehen oder mehrfach vergeben werden. Besonders dann, wenn Sie im Laufe der Zeit viele Texte mit Zählmarken versehen, werden Sie feststellen, dass eine manuelle Zuordnung durch direktes Einfügen der Zählmarken in Beiträgen in hinreichend großer Unüberschaubarkeit endet – dem wirkt Prosodia VGW OS durch Formalisierung und ausgeklügelter Technik entgegen. Im Folgenden werden die Funktionen und Fähigkeiten von Prosodia VGW OS kurz aufgelistet:

* Übersicht aller Zählmarken, Zählmarken-Zuordnungen und weiterer Daten mit vielen Filter- und Sortierfunktionen
* Massenbearbeitung von Zählmarken und Zählmarken-Zuordnungen
* Integration in die Beitrags-Übersicht („Alle Beiträge“) von WordPress
* automatisches Zuordnen von Zählmarken zu Beiträgen – auch massenhaft
* Prüfen-Funktion, ob zugeordnete Zählmarke tatsächlich auf Webseite ausgegeben wird
* Anzeige der Zeichenanzahl eines Beitrags sowie der Anzahl fehlender Zeichen beim Schreiben
* Zeichenanzahl wird nach VG-WORT-Vorgabe berechnet – keine Bilder und Beschriftungen, Shortcodes, HTML-Tags usw.
* es können Beitrags-Typen (Beiträge, Seiten usw.) für die Zählmarken-Funktion ausgewählt werden
* private Zählmarken, Beitrags-Titel, -Texte und -Links können für eine Meldung schnell in die Zwischenablage kopiert werden
* importieren von Zählmarken aus CSV-Dateien, die von der VG WORT bereitgestellt werden
* importieren von Zählmarken aus CSV-Text oder manueller Eingabe
* importieren von Zählmarken und Zählmarken-Zuordnungen aus dem Plugin „Worthy“ von B. Holzmüller
* importieren von Zählmarken und Zählmarken-Zuordnungen aus dem Plugin „VG Wort“ von [Torben Leuschner](https://www.torbenleuschner.de/blog/922/vg-wort-wordpress-plugin/)
* importieren von Zählmarken und Zählmarken-Zuordnungen aus dem Plugin „VG-Wort Krimskram“ von [Heiner Otterstedt](https://wordpress.org/plugins/vgw-vg-wort-zahlpixel-plugin/)
* importieren von manuell zugeordneten Zählmarken aus Beiträgen – `<img>`-Tag wird erkannt und optional gelöscht
* nachträglicher Import von fehlenden privaten Zählmarken, falls die entsprechend öffentlichen bereits vorhanden sind
* exportieren von Zählmarken, Zählmarken-Zuordnungen und weiterer Daten als CSV-Datei mit Filter- und Sortierfunktionen
* Zählmarken (`<img>`-Tags) werden in den Beiträgen auf Ihrer Website ausgegeben
* Unterstützung des Plugins [AMP](https://wordpress.org/plugins/amp/) für [Accelerated Mobile Pages](https://www.ampproject.org/)
* Unterstützung des Plugins [AMP for WP – Accelerated Mobile Pages](https://wordpress.org/plugins/accelerated-mobile-pages/) für [Accelerated Mobile Pages](https://www.ampproject.org/)
* Unterstützung des Plugins [WP AMP](https://codecanyon.net/item/wp-amp-accelerated-mobile-pages-for-wordpress-and-woocommerce/16278608) für [Accelerated Mobile Pages](https://www.ampproject.org/)
* Unterstützung des Plugins [Advanced Custom Fields](https://de.wordpress.org/plugins/advanced-custom-fields/)
* Ausgabe von Zählmarken in Feeds (RSS, Atom, RDF) möglich
* Format der Zählmarkenausgabe kann frei angegeben werden (Platzhalter für Server und öffentliche Zählmarke)
* Unterstützung der Übertragung von Zählmarken (`<img>`-Tags) über verschlüsselte Verbindungen (TLS/SSL, https)
* Datenintegrität der Zählmarken und Zählmarken-Zuordnungen wird stets gewährleistet
* Zählmarken und Zählmarken-Zuordnungen werden in eigener Datenbanktabelle gespeichert – hohe Leistung (getestet mit über 30.000 Beiträgen)
* inaktiv setzen von Zählmarken – Zählmarken-Zuordnung wird nicht aufgehoben, Zählmarke wird nicht ausgegeben
* nicht zuordenbar setzen von Zählmarken – Zählmarke kann dann nicht mehr zugeordnet werden (ist also reserviert)
* Warnung wird ausgegeben, falls andere VG-WORT-Plugins aktiviert sind
* Datenschutz-Vorlage für die Datenschutzerklärung Ihrer Website, wenn Sie Zählmarken der VG WORT verwenden
* Plugin ist DSGVO-konform
* kompatibel mit Gutenberg-Editor
* Integration in die [WordPress-REST-API](https://developer.wordpress.org/rest-api/), um Zählmarken-Daten automatisiert abrufen zu können
* Möglichkeit der vollständigen Deinstallation und Löschung der Datenbanktabellen und Einstellungen
* läuft auf Multisite-Installationen

Schauen Sie sich bitte auch die [Bildschirm-Fotos (Screenshots)](/plugins/wp-vgwort/screenshots/) von Prosodia VGW OS an.

Das Plugin selbst wird mit stets aktuellen Software-Entwicklungswerkzeugen (für [PHP](https://php.net/)), Code-Überprüfungswerkzeugen und Code-Versionierungswerkzeugen in mehreren lokalen Entwicklungs- und Test-Umgebungen weiterentwickelt. Wir achten des Weiteren penibel darauf, dass keine PHP-Warnungen, -Hinweise und -Fehler durch unser Plugin im Normalbetrieb ausgegeben werden und dass wir uns stets an empfohlene Vorgehensweisen beim Entwickeln halten (dies scheint bei vielen Plugins leider nicht gängige Praxis zu sein). Daher sind wir – vermutlich zu recht – davon überzeugt, dass unser Plugin auch in Zukunft stabil und fehlerarm läuft.

= Eignung =

Was Prosodia VGW OS nicht leistet:

* Differenzierung der Zählmarken zwischen den Autoren eines Blogs
* keine direkte Interaktion mit [T.O.M.](https://tom.vgwort.de/portal/index), da von der VG WORT nicht unterstützt
* keine Warnung, wenn nicht zugeordnete Zählmarken knapp werden
* separate Behandlung von Lyrik (darf weniger als 1.800 Zeichen haben)
* Zuordnung von Zählmarken zu PDFs
* exklusiven Support – nur über die Community

Für wen es ungeeignet ist:

* für Blogs mit vielen Autoren
* für Unternehmen
* für Verlage

Einige Funktionen – insbesondere die Autoren-Verwaltung – sind bereits in der Verkaufsversion „Prosodia VGW“ integriert, welche demnächst günstig vertrieben wird.

= Kompatibilität =

Technische Voraussetzungen für Prosodia VGW OS sind:

* mindestens WordPress 5.0
* getestet bis PHP 8.0

= Lizenz =

Prosodia VGW OS wird von der [Max Heckel, Ronny Harbich – Prosodia GbR](https://prosodia.de/kontakt/) unter der GPLv2-Lizenz vertrieben, die unter [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html) nachzulesen ist.

= Haftung =

Die [Max Heckel, Ronny Harbich – Prosodia GbR](https://prosodia.de/kontakt/) übernimmt für Prosodia VGW OS keine Haftung außer die vom Bürgerlichen Gesetzbuch (BGB) zwingend erforderliche. Der Haftungsausschluss soll – soweit wie mit dem BGB vereinbar – der GPLv2-Lizenz entsprechen.

= Hinweis =

Prosodia VGW OS wird von der VG WORT weder unterstützt noch von ihr vertrieben.

= Hilfe und Anleitungen =

Unter [https://prosodia.de/prosodia-vgw-os/](https://prosodia.de/prosodia-vgw-os/) gibt es bebilderte Anleitungen zum Plugin.

Wenn Sie einen Wunsch für eine neue Funktion haben oder Hilfe benötigen, treten Sie bitte mit uns in Kontakt:

* [FAQ-Seite](/plugins/wp-vgwort/faq/)
* [Support-Seite](/support/plugin/wp-vgwort)
* E-Mail-Adressen befindet sich im Plugin unter „Prosodia VGW OS“ → „Hilfe“

== Installation ==

Automatische Installation über den Plugin-Bereich im Administrationsbereich von WordPress oder manuell wie folgt:

1. Laden Sie die Plugin-Zip-Datei herunter.
1. Entpacken Sie die Zip-Datei ins WordPress-Plugin-Verzeichnis (wp-content/plugins/).
1. Aktivieren Sie das Plugin im Plugin-Bereich im Administrationsbereich.

== Frequently Asked Questions ==

= Anleitungen =

Unter [https://prosodia.de/prosodia-vgw-os/](https://prosodia.de/prosodia-vgw-os/) gibt es bebilderte Anleitungen zum Plugin.

= Aktualisierung auf Version 3.0.0 oder höher =

Da das Plugin ab Version 3.0.0 vollständig neu entwickelt wurde, ist es nicht mehr direkt mit den vorherigen Versionen kompatibel. Allerdings werden sämtliche Daten und Einstellungen aus den Vorgängerversionen nach der Aktualisierung übernommen – insbesondere die Zählmarken und deren Zuordnungen zu Beiträgen. Dies funktioniert nicht vollautomatisch, sondern erst nach einigen, wenigen Mausklicks. So werden nach der Aktualisierung im Administrationsbereich einige Warnungen angezeigt. Diese Warnungen enthalten Anweisungen und Links, wie die dargelegten Sachverhalte zu lösen sind – keine Sorge, dies ist mit wenigen Mausklicks erledigt. Und selbst wenn dabei etwas schief gehen sollte – wovon wir nicht ausgehen –, werden die alten Daten und Einstellungen nicht gelöscht. Das Einsetzen einer Version vor 3.0.0 ist stets möglich: [alte Versionen](/plugins/wp-vgwort/developers/).

Sollte das Plugin die ältere Version nicht erkennen, so führen Sie bitte manuell „Prosodia VGW OS“ → „Operationen“ → „Zählmarken aus altem VG-WORT-Plugin vor Version 3.0.0 importieren“ (Haken setzen) → „Alte Zählmarken und Zählmarken-Zuordnungen importieren“ (Schaltfläche) aus.

= „Parse error“ nach Update auf Version 3.0.0 oder höher =

Es erscheint der Fehler `Parse error: syntax error, unexpected T_FUNCTION, expecting ')' in …` nach dem Update des Plugins auf Version 3.0.0 oder höher. Dieser tritt in der Regel auf, wenn eine PHP-Version kleiner als 5.3 eingesetzt wird. Das Plugin setzt allerdings mindestens Version 5.3 voraus. Um das Problem zu beheben, sollten Sie sich an Ihren Systemadministrator wenden (siehe auch [PHP auf der englischen Wikipedia](https://en.wikipedia.org/wiki/PHP#Release_history)).

= Es werden keine Zählmarken aus CSV-Dateien importiert =

Die Spalten der [CSV-Dateien](https://de.wikipedia.org/wiki/CSV_%28Dateiformat%29), die von der VG WORT heruntergeladen werden können, sind mit Semikolon (;) getrennt. Werden CSV-Dateien mit [LibreOffice Calc](https://de.libreoffice.org/) oder Microsoft Excel geöffnet und wieder abgespeichert, kann es ja nach Einstellung vorkommen, dass das Semikolon durch einen Tabulator, Komma oder anderes Zeichen ersetzt wird. In diesem Fall kann das Plugin die Zählmarken in der CSV-Datei nicht mehr erkennen. Bitte verwenden Sie ausschließlich unveränderte CSV-Dateien für den Import.

= Zählmarken lassen sich nicht beim Bearbeiten eines Beitrags einfügen =

Im Gegensatz zur alten Plugin-Versionen sind die Felder im Bereich „Zählmarke für VG WORT“ beim Bearbeiten eines Beitrags nicht dazu da, um Zählmarken in das System einzufügen / zu importieren, sondern dienen lediglich der Zuordnung. Hier dürfen nur bereits importierte Zählmarken angeben werden. Normalerweise findet die Zuordnung durch Setzen des Häkchens „Zählmarke automatisch zuordnen“ (Standardeinstellung) automatisch statt. Am besten, Sie verfahren so: Importieren Sie zunächst Zählmarken der VG WORT als CSV-Datei unter „Import“. Dann sind neue Zählmarken im System. Als nächstes gehen Sie zum Beitrag und weisen diesem automatisch eine neue Zählmarke zu. Eine manuelle Zuordnung ist ebenfalls möglich: Dazu können Sie die bereits importierte, öffentliche Zählmarke angeben (die private findet das Plugin automatisch). Öffentliche Zählmarken sind Codes wie „00a0f12e1113423cc56ff5“ und keine `<img>`-HTML-Tags wie `<img src="http://vg04.met.vgwort.de/na/00a0f12e1113423cc56ff5" width="1" height="1" alt="">`. Zusammenfassend: unbenutzte Zählmarken von der VG WORT importieren, dann automatisch zu Beiträgen zuordnen lassen.

= Zeichenanzahl wird bei Verwendung von Shortcodes falsch berechnet =

Es besteht die Möglichkeit, die Einstellung „Prosodia VGW OS“ → „Operationen“ → „Shortcodes bei Berechnung der Zeichenanzahl auswerten“ zu aktivieren. Dann werden die Shortcodes in einem Beitrag aufgelöst und die Anzahl der Zeichen der Ausgabe der Shortcodes mitberechnet. Dies funktioniert allerdings nur, wenn der jeweilige Shortcode auch im Administrationsbereich aufgelöst werden kann. Plugins haben die Möglichkeit, ihre Shortcodes nur auf der eigentlichen Website – also nicht im Administrationsbereich – auflösen zu lassen. In diesem Fall kann Prosodia VGW OS die Ausgabe der Shortcodes nicht erhalten und folglich auch nicht die korrekte Zeichenanzahl bestimmen. Zur Zeit ist uns für dieses Problem leider keine Lösung bekannt. Davon abgesehen, kann stets eine Zählmarke zugeordnet werden, auch wenn die nötige Zeichenanzahl nicht erreicht wurde bzw. nicht korrekt ermittelt werden konnte.

== Screenshots ==

1. Zählmarken und Export.
2. Import von Zählmarken.
3. Einstellungen.
4. Komplexe Operationen und Einstellungen.
5. Integration in der Beitragsübersicht von WordPress (Spalte „Zeichen“, Filter)
6. Integration beim Bearbeiten eines Beitrags.
7. Zählmarkenausgabe (markiert) im HTML-Quelltext eines Beitrags.

== Changelog ==

= 3.25.3 =
* Fehler bez. aufwendiger Operationen behoben. 

= 3.25.2 =
* Fehler bez. Zählmarke (in-)aktiv setzen behoben. 

= 3.25.1 =
* Fehler bez. Seitentyp-Erkennung behoben.

= 3.25.0 =
* Medien (z. B. PDF) können nun Zählmarken zugeordnet werden (nicht für AMP).
* Zählmarkenausgebe-Format verbessert (referrerpolicy). Warnung hinzugefügt, falls das eingestellte Zählmarken-Ausgabe-Format vom Standardwert abweicht (abstellbar).
* VG-WORT-Datenschutz-Hinweis für Datenschutzerklärung aktualisiert.
* Begriff „Beitrag“ plugin-weit durch „Seite“ ersetzt.
* Kompatibilität zu WordPress 5.7 getestet.
* Diverse Quellcode-Verbesserungen.
* Diverse Fehlerbehebungen. 

= 3.24.11 =
* Plugin erneut auf wordpress.org hochgeladen.  

= 3.24.10 =

_Update empfohlen._

* Fehler (Klasse „WPVGW“ nicht gefunden) in Bezug auf PHP 8.0 behoben.

= 3.24.9 =
* Problem behoben, dass aufgrund restriktiver Datenbankkonfiguration auftreten konnte (`SQL_BIG_SELECTS` in MySQL).

= 3.24.8 =
* Fehler Umbenennung von `cleanWordPressText()` behoben.

= 3.24.7 =
* Fehler im Build-System behoben, der den Quellcode falsch formatiert hat. 

= 3.24.6 =
* Kompatibilität zu WordPress 5.6 und PHP 8.0 hergestellt. Code-Verbesserungen.

= 3.24.5 =
* Methode verbessert, den Block-Editor zu erkennen.

= 3.24.4 =
* WordPress-Filter `deny_get_marker_data` hinzugefügt. 

= 3.24.3 =
* Fehler behoben.

= 3.24.2 =
* Notwendige Mindestversion von PHP auf 7.0 gesetzt.

= 3.24.1 =
* Fehler behoben, der in der Zählmarken-Übersicht bei der Suche auftrat.  

= 3.24.0 =
* Integration in die [WordPress-REST-API](https://developer.wordpress.org/rest-api/), um Zählmarken-Daten automatisiert abrufen zu können.

= 3.23.0 =

_Update empfohlen._

* Berechnung der Zeichenanzahlen verbessert.
* Kompatibilität mit WordPress 5.5 getestet.
* Sicherheit für AJAX-Aufrufe verbessert. 
* Code-Qualität an einigen Stellen erhöht.

= 3.22.10 =
* Lizenztext geändert. Lizenzrestriktionen entfernt.

= 3.22.9 =
* Fehler behoben.

= 3.22.8 =
* Fehler behoben.

= 3.22.7 =
* Fehler behoben (`'AMPHTML'`).

= 3.22.6 =
* Die Zeitspanne für die automatische Aktualisierung der Anzeige der Zeichenanzahl im Beitrags-Editor kann nun eingestellt werden (Deaktivierung auch möglich).
* CSS-Klasse `wpvgw-marker-image` im `img`-Tag für Zählmarkenausgabe hinzugefügt. Kann z. B. für Ausnahmeregeln in Lazy-Load-Plugins verwendet werden.  
* Kompatibilität mit fehlerhaften Shortcodes diverser Plugins verbessert (Teil 3).

= 3.22.5 =
* Kompatibilität mit fehlerhaften Shortcodes diverser Plugins verbessert (Teil 2). 

= 3.22.4 =
* Kompatibilität mit fehlerhaften Shortcodes diverser Plugins verbessert.

= 3.22.3 =

_Update empfohlen._

* Standard-Ausgabe für Zählmarken in Bezug auf Lazy-Loading-Plugins verbessert (`loading="eager"` und `data-no-lazy="1"` hinzugefügt).

= 3.22.2 =
* Es werden nun auch dynamische Felder des Plugins [Advanced Custom Fields](https://de.wordpress.org/plugins/advanced-custom-fields/) (ACF) bei der Berechnung der Zeichenanzahl berücksichtigt.
* Plugin-Namen geändert auf „Prosodia VGW OS“.
* Position von „Prosodia VGW OS“ im WordPress-Menü hinter „Einstellungen“ verschoben.

= 3.22.1 =

_Update empfohlen._

* VG-WORT-Datenschutzerklärung zur Website-Integration aktualisiert. Bitte auf Website anpassen! Es erscheint eine entsprechende Warnung. 

= 3.22.0 =
* Unterstützung für das Plugin [Advanced Custom Fields](https://de.wordpress.org/plugins/advanced-custom-fields/) (ACF) hinzugefügt. Die Zeichenanzahl von ACF-Feldern kann nun zur Zeichenanzahl des Beitrags addiert werden (muss pro ACF-Feld aktiviert werden).
* Zusätzliche `<noscript>`-Ausgabe im Falle von Lazy Loading als Fallback für deaktiviertes JavaScript hinzugefügt.
* Impressum-Seite aktualisiert.

= 3.21.7 =
* Einem geplanten Beitrag kann nun auch eine Zählmarke zugeordnet werden.

= 3.21.6 =
* Einem Beitrag kann nun nur noch eine Zählmarke zugeordnet, wenn dieser veröffentlicht ist/wird. Lässt sich per Einstellung de/aktivieren.
* Meldungen über Zählmarken-Zuordnungen usw. verbessert.
* Kleinere Verbesserungen in der Zählmarken-Übersicht.
* Es wird nun mindestens WordPress 5.0 vorausgesetzt.

= 3.21.5 =

_Update empfohlen._

* Kompatibilität mit WordPress 5.3 getestet.
* Fehler behoben, der unter WordPress 5.3 und Gutenberg-/Block-Editor das Zählen der Zeichen verhinderte.
* Beschreibungstext unter „Server“ auf der Import-Seite verbessert.
* „Autosave“-Erkennung beim Editieren von Beiträgen verbessert.

= 3.21.4 =
* Fehler behoben, der im Falle von Lazy-Loading auftrat, wenn jQuery ab Version 3.0 verwendet wird. 

= 3.21.3 =
* Fehler behoben, der im Zusammenhang mit dem Plugin „Jetpack von WordPress.com“ auftrat.

= 3.21.2 =
* Beiträgen wird nun nur noch standardmäßig eine Zählmarke zugeordnet, wenn die entsprechende Einstellung aktiviert ist und der aktuelle Benutzer auch der Beitragsautor ist.
* Fehler behoben, der die Zählmarkenausgabe verhinderte, wenn das Plugin „All In One WP Security“ aktiviert war.

= 3.21.1 =

_Update empfohlen._

* Neuer Link zur Prüfen-Funktion der Zählmarken.
* Kompatibilität mit AMP-Plugins verbessert (Dank an Daniel Hüsken).
* „Prosodia VGW OS“ → „Einstellungen“ etwas verbessert.
* Plugin-Kompatibilität mit PHP 7.3 erfolgreich getestet. 

= 3.21.0 =
* Möglichkeit hinzugefügt, Zählmarken in Feeds (RSS, Atom, RDF) ausgeben zu lassen.

= 3.20.7 =
* Verbesserung der Suchfunktion in der Zählmarken-Übersicht.

= 3.20.6 =

_Update empfohlen._

* Vollständige Unterstützung des seit WordPress 5.0 verfügbaren Block-Editors (Gutenberg-Editor).

= 3.20.5 =
 
_Update empfohlen._

* Fehler behoben, der Zeichenzählung im Gutenberg-Editor verhinderte und den Maus-Cursor zurücksetzte.

= 3.20.4 =
* Verbesserung der Unterstützung des Gutenberg-Editors. Meta-Box „Zählmarke für VG WORT“ wird beim Speichern noch nicht neu geladen.

= 3.20.3 =

_Update empfohlen._

* Handhabung von HTTP und HTTPS nach aktuellen VG-WORT-Spezifikationen angepasst (es gibt nicht mehr nur genau einen HTTPS-Server für Zählmarken).

= 3.20.2 =
* Teilweise Unterstützung des Gutenberg-Editors. Meta-Box „Zählmarke für VG WORT“ wird beim Speichern noch nicht neu geladen.

= 3.20.1 =
* Fehler behoben, der die Beitragsübersicht bei geringer Fensterbreite deformierte.

= 3.20.0 =
* Unterstützung des Plugins „WP AMP“. Auf dessen AMP-Seiten werden nun Zählmarken ausgegeben.

= 3.19.1 =

_Update dringend empfohlen._

* Datenbankfehler behoben, der bei Neuinstallationen von Version 3.19.0 auftrat. 

= 3.19.0 =

_Update empfohlen._

* Zählmarken-Übersicht („Prosodia VGW OS“ → „Zählmarken“) verbessert und um Spalte „Status“ erweitert.
* Möglichkeit hinzugefügt, Zählmarken auf nicht zuordenbar zu setzen. Zählmarke kann dann nicht mehr zugeordnet werden (ist also reserviert).
* Möglichkeit hinzugefügt, Beiträge von der standardmäßigen Zählmarken-Zuordnung auszunehmen (falls die Einstellung „Beiträgen standardmäßig eine Zählmarke zuordnen“ aktiviert ist).
* Möglichkeit hinzugefügt, Zählmarken via „Lazy Load“ (Nachladen mittels JavaScript) auszugeben.
* Prüfen-Funktion von Zählmarken auf Webseiten wieder aktiviert und DSGVO-konform implementiert.
* Datenschutzerklärung hinzugefügt.
* VG-WORT-Datenschutz-Hinweis für Datenschutzerklärung aktualisiert.
* `wp_vgwort_frontend_display`-Filter um die Parameter `use_tls` und `is_amp` erweitert.
* Inaktive Zählmarken werden nun automatisch aktiviert, wenn sie einem Beitrag zugeordnet werden.
* Mehrere Fehler behoben (vielen Dank an Stephan Kockmann).

= 3.18.3 =
* Fehler im Zusammenhang mit dem Plugin InfiniteWP behoben.

= 3.18.2 =
* Fehler behoben, der Server von Zählmarken falsch anzeigte, wenn TLS/SSL (HTTPS) deaktiviert ist.

= 3.18.1 =
* Bessere Anzeige von Servern von Zählmarken, die aufgrund von TLS/SSL (HTTPS) bei der Ausgabe überschrieben werden.

= 3.18.0 =
* Möglichkeit hinzugefügt, die Zeichenanzahl von Beiträgen selbst zu berechnen. Siehe [Anzahl der Zeichen selbst berechnen](https://prosodia.de/prosodia-vgw-os/anzahl-der-zeichen-selbst-berechnen/).

= 3.17.4 =

_Update dringend empfohlen._

* Fehler behoben, der auf der Beitrag-Bearbeiten-Seite auftrat und dort u. a. den Text-Editor teilweise lahmlegte. Betrifft ausschließlich Version 3.17.3.
* Kleinere JavaScript-Fehler behoben.

= 3.17.3 =

_Update empfohlen._

* Methode verbessert, wie Zählmarken in einem Beitrag ausgegeben werden.
* Dienst [Zählmarke auf Webseite finden](https://prosodia.de/app/prosodia-vgw-services/marke-finden) und damit die Zählmarken-Prüfen-Funktion verbessert. Es wird nun überprüft, ob die zugeordnete öffentliche Zählmarke auf der Webseite tatsächlich enthalten ist. 

= 3.17.2 =

_Update empfohlen._

* Fehler behoben, der das VG-WORT-Server-Format zu ungenau validierte.
* Regulären Ausdruck verbessert, der manuelle Zählmarken in Beiträgen erkennt.
* Zählmarken mit dem VG-WORT-Server „ssl-vg03.met.vgwort.de/na“ werden nun immer über HTTPS ausgegeben (auch, wenn HTTP aktiviert ist).

= 3.17.1 =
* Beschreibung der HTTP(S)-Warnung korrigiert.

= 3.17.0 =
* Funktion hinzugefügt, die erkennt, ob Zählmarken über HTTP oder HTTPS ausgegeben werden müssen. Ggf. wird eine Warnung angezeigt.
* Dienst [Zählmarke auf Webseite finden](https://prosodia.de/app/prosodia-vgw-services/marke-finden) und damit die Zählmarken-Prüfen-Funktion verbessert. Es werden mehr Details und Fehler angezeigt.
* [Anleitung](https://prosodia.de/prosodia-vgw-os/transportverschluesselung-tls-ssl-aktivieren/) für Zählmarken und HTTPS erstellt.  

= 3.16.2 =
* Fehler behoben, der unter Umständen Zählmarken auf AMP-Seiten doppelt ausgab.

= 3.16.1 =
* Erkennung der Zählmarken beim CSV-Import toleranter gemacht.
* Fehler behoben, der unter Umständen Zählmarken auf AMP-Seiten doppelt ausgab.

= 3.16.0 =
* Funktion hinzugefügt, die es ermöglicht zu prüfen, ob eine zugeordnete Zählmarke in einem Beitrag tatsächlich ausgegeben wird (Link „Prüfen“ unter „Prosodia VGW OS“ → „Zählmarken“).  
* Fehler behoben, der seit WordPress 4.7 keine Zählmarken mehr unter „Alle Seiten“ anzeigen ließ. 

= 3.15.6 =

_Update empfohlen._

* Methode verändert, wie Zählmarken in einem Beitrag ausgegeben werden. Diese ist nun deutlich robuster.
* Kompatibilität mit InfiniteWP hergestellt.
* Benutzerrollen, die die Verwendung von Zählmarken zulassen, können nun via `apply_filters()` verändert werden.
* Kompatibilität mit WordPress 4.6 getestet.

= 3.12.5 =
* Möglichkeit hinzugefügt, Zählmarken vor dem Beitragsinhalt auszugeben, anstatt im Fußbereich der Webseite („Prosodia VGW OS“ → „Einstellungen“ → „Zählmarken-Ausgabe-Position“ → „Zählmarke vor Beitragsinhalt ausgeben“).

= 3.12.4 =
* Fehler behoben, der unter gewissen Umständen das Ausführen komplexer Operationen verhinderte.

= 3.12.3 =

_Update empfohlen._

* Kompatibilität zu WordPress 4.5 sichergestellt.

= 3.12.2 =
* Das Ausgabe-Format einer Zählmarke auf AMP-Seiten kann nun eingestellt werden („Prosodia VGW OS“ → „Einstellungen“ → „Zählmarken-Ausgabe unverschlüsselt“ → „Ausgabe im AMP-Plugin“ ff.)
* Die Standard-Ausgabe einer Zählmarke auf AMP-Seiten wurde korrigiert (jetzt als `<amp-pixel>`-Tag).

= 3.12.1 =
* Zählmarken werden nun auch in Beiträgen ausgegeben, die vom Plugin AMP generiert werden.
* Kleinere Verbesserungen vorgenommen.

= 3.12.0 =

_Update empfohlen._

* Aufwendige Operationen (z. B. „Zeichenanzahlen aller Beiträge neuberechnen“) werden nun Schrittweise ausgeführt (AJAX-Aufrufe). Dadurch können noch mehr Beiträge verarbeitet werden. Außerdem wird der Fortschritt der Operationen angezeigt, und Operationen können abgebrochen werden. Die Gefahr, dass der Server oder PHP Operationen aufgrund einer Zeitüberschreitung abbricht (PHP, `max_execution_time`), ist damit signifikant geringer.
* Für Zählmarken wurde das Bestelldatum (bei der VG WORT) hinzugefügt. Beim Import über CSV-Dateien wird das Bestelldatum automatisch erkannt (nur Autoren-Konto). Bereits importierte CSV-Dateien können erneut importiert werden, damit Bestelldaten bestehender Zählmarken aktualisiert werden.
* Mehrere kleinere Fehlerbehebungen und Verbesserungen.

= 3.11.1 =
* Fehler behoben, der einen Teil des Textes des Datenschutz-Hinweises löschte. Ursache ist ein Fehler im build tool Phing. Falls der Text des Datenschutz-Hinweises verwendet wurde, muss er möglicherweise korrigiert werden.

= 3.11.0 =
* In der Zählmarken-Übersicht („Prosodia VGW OS“ → „Zählmarken“) können nun private Zählmarken, Beitrags-Titel, -Texte und -Links einfach in die Zwischenablage kopiert werden. Das Melden von Beiträgen bei der VG WORT verläuft damit schneller.
* Möglichkeit hinzugefügt, TLS/SSL (https) zur Übertragung von Zählmarken zu verwenden („Prosodia VGW OS“ → „Einstellungen“ → „Zählmarken“).
* Die Hilfe-Texte können nun über „i“-Symbole ein- und ausgeblendet werden. Die Seiten „Einstellungen“, „Operationen“ usw. sind daher übersichtlicher.
* Zählmarken und Zählmarken-Zuordnungen können nun aus dem Plugin „Worthy“ von B. Holzmüller importiert werden („Prosodia VGW OS“ → „Operationen“ → „Zählmarken aus dem Plugin ‚Worthy‘ von B. Holzmüller importieren“).

= 3.10.2 =
* Fehler behoben, der die korrekte Anzeige der Spalte „Zeichen“ in der Beitrags-Übersicht im Zusammenhang mit Caching-Plugins (namentlich W3 Total Cache) verhinderte.

= 3.10.1 =
* Fehler behoben, der verhinderte, dass die Massenbearbeitungen in der unteren Auswahlliste (z. B. „Zählmarke zuordnen“) in der Beitrags-Übersicht durchgeführt werden konnten.

= 3.10.0 =
* Neuen Filter „Zählm.-Format“ in der Zählmarken-Übersicht hinzugefügt, mit dem nach ungültigen Zählmarken gefiltert werden kann. Besonders sinnvoll, wenn manuelle Zählmarken aus Beiträgen importiert wurden, da diese eventuell durch falsche Eingabe ungültig sein könnten.
* Fehler behoben, der verhinderte, dass der Lade-Spinner im Bereich „Zählmarke der VG WORT“ in der Beitrags-Bearbeitung ab WordPress 4.2 angezeigt wurde.

= 3.9.0 =
* Die Zeichenanzahl der Auszüge von Beiträgen kann bei der Berechnung der Zeichenanzahl eines Beitrags nun mit einberechnet werden. Aktivierung unter „Einstellungen“.
* Das Plugin ist nun vollständig kompatibel zu WordPress 4.2, insbesondere im Hinblick auf die Änderung der Zeichenkodierung von `utf8` zu `utf8mb4` in der MySQL-Datenbank.

= 3.8.0 =
* Der Shortcode `[pvgw_post_stats]` wurde verbessert: Die Zeichenanzahl pro Blatt kann nun manuell eingestellt werden. Das Runden auf halbe Seiten wurde hinzugefügt.
* In der Beitrags-Übersicht werden „Zählmarke möglich“ und „Zählmarke zugeordnet“ in der Spalte „Zeichen“ zur besseren Übersicht farblich hervorgehoben. Dies kann in den Einstellungen wieder zurückgesetzt werden.

= 3.7.0 =
* Shortcode `[pvgw_post_stats]` hinzugefügt, über den die Zeichenanzahl und die Anzahl der Normseiten eines Beitrags ausgeben werden kann.

= 3.6.0 =
* Es können nun Zählmarken und Zählmarken-Zuordnungen vom Plugin „VG-Wort Krimskram“ von H. Otterstedt importiert werden.

= 3.5.0 =
* Beiträgen kann nun standardmäßig eine Zählmarke zugeordnet werden. Muss in den Einstellungen aktiviert werden.
* Absätze in den Seiten unter „Prosodia VGW OS“ haben nun eine maximale Breite, damit diese nicht mehr zu lang gesetzt werden.

= 3.4.6 =
* Link zu „Import“ wird nun auch in der Nachricht, dass zu wenig Zählmarken vorhanden sind, in der Beitrags-Übersicht angezeigt.
* Fehler behoben, der auftrat, wenn bei der Massenbearbeitung in der Beitrags-Übersicht keine Beiträge ausgewählt wurden (nur Plugin-Aktionen).

= 3.4.5 =
* Regulären Ausdruck zur Erkennung von manuellen Zählmarken in Beiträgen (deren Inhalt) verbessert.

= 3.4.4 =
* Fehler bei Code-Migration, die doppelte Einstellung „Zählmarken pro Seite in der Übersicht“ unter „Einstellungen“ verursacht hatte.

= 3.4.3 =
* Filter „Zuordnung“ in der Zählmarken-Übersicht hinzugefügt.
* Alle Operationen sind nun auf die ausgewählten Beitrags-Typen beschränkt.
* Plugin auf WordPress 4.1 getestet.
* Fehler behoben, die auftraten, wenn kein Beitrags-Typ ausgewählt wurde.
* Fehler behoben, der bei der Plugin-Deinitialisierung auftrat (Null-Referenz, bei Verwendung mit „NextGEN Gallery“).

= 3.4.2 =
* Verbesserung der Handhabung von nicht verfügbaren Beitrags-Typen bei Deaktivierung und Aktualisierung von Plugins/Themes, die eigene Beitrags-Typen definieren.
* Fehler bei Code-Migration, der die Deinstallationsmöglichkeit unter „Einstellungen“ entfernt hatte.

= 3.4.1 =
* Benutzer mit der Rolle „Mitarbeiter“ können nun Zählmarken zuordnen.
* Fehler behoben, der Import von CSV-Daten verhinderte (nur für PHP unter Version 5.5 relevant).

= 3.4.0 =
* Es können nun optional auch Zählmarken vom einem Verlags-Konto bei der VG WORT importiert werden (anderes CSV-Format).
* Die Zeichenanzahlen können nun in der Beitrags-Übersicht und in der Zählmarken-Übersicht für ausgewählte Beiträge neuberechnet werden.
* Die Zeichenanzahl im visuellen Beitrags-Editor wird jetzt genauer berechnet und ist jetzt mit dem textuellen Beitrags-Editor synchron.
* Fehler behoben (JavaScript), der den Beitrags-Editor unbrauchbar machte, wenn bei den Benutzereinstellungen „Beim Schreiben den WYSIWYG-Editor nicht benutzen“ aktiviert wurde.
* Fehler behoben, der anzeigte, dass die Zeichenanzahl nicht genügte, wenn Zählmarken in der Beitrags-Übersicht zugeordnet wurden.
* Fehler „Catchable fatal error: must be an instance of callable, instance of Closure given“ behoben (nur für PHP 5.3 relevant).

= 3.3.0 =
* Möglichkeit hinzugefügt, Shortcodes bei Berechnung der Zeichenanzahl mit auswerten zu lassen („Prosodia VGW OS“ → „Einstellungen“ → „Zeichenanzahl“).
* Möglichkeit hinzugefügt, die maximale Ausführungszeit für Operationen zu ändern, falls Operationen abbrechen („Prosodia VGW OS“ → „Einstellungen“ → „Verschiedenes“).
* Workaround für die Berechnung der Zeichenanzahl bei der Beitrags-Bearbeitung (manche Plugins manipulieren den visuellen Editor).

= 3.2.0 =
* Es sollte nun leichter verständlich sein, dass Zählmarken beim Bearbeiten eines Beitrags nur zugeordnet werden und nicht eingeben/importiert werden können (Benutzeroberfläche verbessert).
* Leistungsverbesserung bei der Auswahl der Beitrags-Typen und der Neuberechnung der Zeichenanzahlen aller Beiträge.
* In der Beitrags-Übersicht „Alle Beiträge“ wird jetzt „nicht berechnet“ angezeigt anstatt „0“, wenn die Zeichenanzahl noch nicht berechnet wurde.
* Fehler behoben, der das Anzeigen aller Beitrags-Typen unter „Operationen“ verhinderte.
* Nachricht im Administrationsbereich hinzugefügt, falls die zu importierende CSV-Datei (oder CSV-Text) ein ungültiges Format hat.

= 3.1.1 =
* Fehler bezüglich leerer Meta-Name-Option aus Plugin-Version < 3.0.0 behoben. Import aus alter Plugin-Version sollte nun in diesem Fall wieder möglich sein.
* Option „Meta-Name“ unter „Operationen“ hinzugefügt.

= 3.1.0 =
* Leistungsverbesserung (insbesondere geringere Arbeitsspeichernutzung) der Funktionen im Bereich „Operationen“.

= 3.0.1 =
* Fehler Behoben, der Import aus anderen Plugins verhinderte (closures unterstützen keinen Zugriff auf private members in PHP 5.3).

= 3.0.0 =
* Plugin wurde vollständig neu entwickelt.
* Viele neue Funktionen. Siehe Plugin-Beschreibung.
* Plugin-Name geändert.
* Unterstützung durch Prosodia – Verlag für Musik und Literatur.

= 2.1.6 =
* Fehlerhaften Link zu „Datenschutz“ behoben (Dank an Jan Eric Hellbusch).

= 2.1.5 =
* Fehlerbehebung (Dank an rrho).

= 2.1.4 =
* Fehlerbehebung.

= 2.1.3 =
* Name des Plugins geändert.
* Hinweis auf Datenschutzrichtlinien hinterlegt.

= 2.1.1 =
* Fehler im Export behoben.

= 2.1.1 =
* Diverse PHP Warnings behoben.
* Rechtschreibung und Ausdruck verbessert.
* PO-Datei für Übersetzer aktualisiert.
* Administrations-Bereich auf WordPress 3.8 angepasst.

= 2.1.0 =
* Kleinere Fehler behoben.
* Filterfunktion für Zählmarkenausgabe hinzugefügt.
* System der Versionsnummer-Vergabe umgestellt (https://semver.org/).

= 2.0.4 = 
* Zählmarken werden direkt vor dem </body>-Tag eingefügt.

= 2.0.3 = 
* Löschen-Schaltfläche hinzugefügt.

= 2.0.2 = 
* Fehlerbehebung.
* Sprachübersetzung/-änderung nun möglich.

= 2.0.1 = 
* Kompatibilität-Problem mit wp_minify behoben.

= 2.0.0 = 
* Neues Feature.

= 1.9 = 
* Fehlerbehebung.

= 1.8 = 
* Fehlerbehebung.

= 1.7 = 
* Zählmarke wird nur in Beiträgen und Seiten angezeigt.

= 1.6 = 
* Zählmarke auch auf Seiten möglich.

= 1.5 = 
* Anzeige von Inhalten mit weniger als 1800 Zeichen im Benutzerprofil.

= 1.4 =
* Probleme mit Shortcode behoben.
* Ausgabe der Zeichen im Editor angepasst.
* Filterfunktion für Zählmarke im Benutzerprofil.
* Feedback-Funktionen hinzugefügt.

= 1.3 =
* Speichern der Zählmarke bei vorhandener Zählmarke (Fehlerbehebung).

= 1.2 =
* Einbau Zählmarke (Fehlerbehebung).

= 1.1 =
* Spalte für Zählmarken in Beitragsübersicht angepasst.

= 1.0 =
* Initial-Release.

== Upgrade Notice ==

= 3.25.3 =
Einen Fehler behoben.

= 3.25.2 =
Einen Fehler behoben.

= 3.25.1 =
Einen Fehler behoben.

= 3.25.0 =
Neue Funktionen. Verbesserungen. Fehlerbehebungen.

= 3.24.11 =
Ein Problem behoben.

= 3.24.10 =
Update empfohlen! Einen Fehler behoben.

= 3.24.9 =
Ein Problem behoben.

= 3.24.8 =
Einen Fehler behoben.

= 3.24.7 =
Einen Fehler behoben.

= 3.24.6 =
Verbesserungen.

= 3.24.5 =
Verbesserung.

= 3.24.4 =
Filter hinzugefügt.

= 3.24.3 =
Einen Fehler behoben.

= 3.24.2 =
PHP-Mindestversion auf 7.0 gesetzt.

= 3.24.1 =
Einen Fehler behoben.

= 3.24.0 =
REST-API für Zählmarken implementiert.

= 3.23.0 =
Eine Verbesserung. Sicherheit verbessert.

= 3.22.10 =
Lizenz aktualisiert.

= 3.22.9 =
Einen Fehler behoben.

= 3.22.8 =
Einen Fehler behoben.

= 3.22.7 =
Einen Fehler behoben.

= 3.22.6 =
Zwei Verbesserungen. Einen Fehler behoben.

= 3.22.5 =
Verbesserung.

= 3.22.4 =
Verbesserung.

= 3.22.3 =
Update empfohlen! Verbesserung.

= 3.22.2 =
Unterstützung von „Advanced Custom Fields“ verbessert. Plugin-Name geändert. „Prosodia VGW OS“ im Menü verschoben.

= 3.22.1 =
VG-WORT-Datenschutzerklärung aktualisiert. Bitte auf Website anpassen!

= 3.22.0 =
Unterstützung von „Advanced Custom Fields“. Verbesserungen.

= 3.21.7 =
Verbesserung.

= 3.21.6 =
Verbesserungen. WordPress 5.0 vorausgesetzt.

= 3.21.5 =
Update empfohlen! Einen Fehler behoben. Zwei Verbesserungen.

= 3.21.4 =
Einen Fehler behoben.

= 3.21.3 =
Einen Fehler behoben.

= 3.21.2 =
2 Fehler behoben.

= 3.21.1 =
Update empfohlen! Verbesserungen vorgenommen.

= 3.21.0 =
Zählmarkenausgabe in Feeds hinzugefügt.

= 3.20.7 =
Verbesserung Suchfunktion.

= 3.20.6 =
Update empfohlen! Vollständige Unterstützung Block-Editor (Gutenberg-Editor).

= 3.20.5 =
Update empfohlen! Einen Fehler behoben.

= 3.20.4 =
Verbesserung Unterstützung Gutenberg-Editor.

= 3.20.3 =
Update empfohlen! HTTP/HTTPS-Handhabung verbessert.

= 3.20.2 =
Teilweise Unterstützung Gutenberg-Editor.

= 3.20.1 =
Einen Fehler behoben.

= 3.20.0 =
Unterstützung von „WP AMP“.

= 3.19.1 =
Update dringend empfohlen! Datenbankfehler behoben für Neuinstallationen von Version 3.19.0.

= 3.19.0 =
Update empfohlen! Neue Funktionalitäten hinzugefügt. Mehrere Fehler behoben.

= 3.18.3 =
Einen Fehler behoben.

= 3.18.2 =
Einen Fehler behoben.

= 3.18.1 =
Anzeige verbessert.

= 3.18.0 =
Zeichenanzahl kann selbst berechnet werden.

= 3.17.4 =
Update dringend empfohlen! Einen kritischen Fehler behoben. Einen weiteren Fehler behoben.

= 3.17.3 =
Update empfohlen! Zwei Verbesserungen vorgenommen.

= 3.17.2 =
Update empfohlen! Drei Fehler behoben.

= 3.17.1 =
Einen Fehler behoben.

= 3.17.0 =
HTTPS-Unterstützung verbessert.

= 3.16.2 =
Einen Fehler behoben.

= 3.16.1 =
CSV-Import verbessert. Einen Fehler behoben.

= 3.16.0 =
Prüfen-Funktion für zugeordnete Zählmarken. Einen Fehler behoben.

= 3.15.6 =
Update empfohlen! Zwei Verbesserungen vorgenommen. Filter für Benutzerrollen hinzugefügt.

= 3.12.5 =
Zählmarken können vor dem Beitragsinhalt ausgegeben werden.

= 3.12.4 =
Einen Fehler behoben.

= 3.12.3 =
Update empfohlen! Kompatibilität zu WordPress 4.5 sichergestellt.

= 3.12.2 =
Einen Fehler behoben. Verbesserte Unterstützung des Plugins „AMP“.

= 3.12.1 =
Unterstützung des Plugins „AMP“. Kleinere Verbesserungen vorgenommen.

= 3.12.0 =
Update empfohlen! Bessere Implementierung aufwendiger Operationen. Bestelldatum für Zählmarken hinzugefügt. Mehrere Fehler behoben.

= 3.11.1 =
Fehler behoben, der Teil des Textes des Datenschutz-Hinweises löschte. Bitte ggf. korrigieren.

= 3.11.0 =
Kopieren von Beitrags-Texten usw. in die Zwischenablage in der Zählmarken-Übersicht möglich. TLS/SSL-Unterstützung. Hilfe-Texte über „i“-Symbole anzeigen. Zählmarken-Import vom Plugin „Worthy“ möglich.

= 3.10.2 =
Einen Fehler behoben.

= 3.10.1 =
Einen Fehler behoben.

= 3.10.0 =
Neuen Filter „Zählm.-Format“ hinzugefügt. Einen Fehler behoben.

= 3.9.0 =
Beitrags-Auszug kann bei Berechnung der Zeichenanzahl mit ausgewertet werden. Kompatibilität zu WordPress 4.2 hergestellt.

= 3.8.0 =
Shortcode `[pvgw_post_stats]` verbessert. Farbhervorhebung für Spalte „Zeichen“ in Beitrags-Übersicht hinzugefügt.

= 3.7.0 =
Shortcode `[pvgw_post_stats]` hinzugefügt.

= 3.6.0 =
Zählmarken und Zählmarken-Zuordnungen vom Plugin „VG-Wort Krimskram“ importierbar.

= 3.5.0 =
Beiträgen kann nun standardmäßig eine Zählmarke zugeordnet werden. Absätze in den Seiten unter „Prosodia VGW OS“ haben nun eine maximale Breite.

= 3.4.6 =
Zwei Fehler behoben.

= 3.4.5 =
Regulären Ausdruck zur Erkennung von manuellen Zählmarken in Beiträgen (deren Inhalt) verbessert.

= 3.4.4 =
Einen Fehler behoben.

= 3.4.3 =
Filter „Zuordnung“ in der Zählmarken-Übersicht hinzugefügt. Alle Operationen sind nun auf die ausgewählten Beitrags-Typen beschränkt. Plugin auf WordPress 4.1 getestet. Zwei Fehler behoben.

= 3.4.2 =
Verbesserung der Handhabung von nicht verfügbaren Beitrags-Typen. Einen Fehler behoben.

= 3.4.1 =
„Mitarbeiter“ können Zählmarken zuordnen. Einen Fehler behoben.

= 3.4.0 =
Zählmarken für Verlags-Konto importierbar. Zeichenanzahlen für ausgewählte Beiträge neuberechenbar. Berechnung Zeichenanzahl im visuellen Beitrags-Editor verbessert Drei Fehler behoben.

= 3.3.0 =
Shortcodes können bei Zeichenanzahl-Berechnung ausgewertet werden. Maximale Ausführungszeit für Operationen änderbar. Workaround für Zeichenanzahl-Berechnung in der Beitrags-Bearbeitung.

= 3.2.0 =
Benutzeroberfläche verbessert. Leistungsverbesserung für Berechnung der Zeichenanzahlen aller Beiträge. Einen Fehler behoben.

= 3.1.1 =
Fehler bezüglich leerer Meta-Name-Option aus Plugin-Version < 3.0.0 behoben. Import aus alter Plugin-Version wieder möglich.

= 3.1.0 =
Leistungsverbesserung (insbesondere geringere Arbeitsspeichernutzung) der Funktionen im Bereich „Operationen“.

= 3.0.1 =
Fehler Behoben, der Import aus anderen Plugins verhinderte (closures unterstützen keinen Zugriff auf private members in PHP 5.3).

= 3.0.0 =
PLUGIN VOLLSTÄNDIG NEU ENTWICKELT! Nach der Aktualisierung werden Warnungen erscheinen, was normal ist. Diese bitte einfach abarbeiten. Weitere Informationen: https://wordpress.org/plugins/wp-vgwort/faq/
