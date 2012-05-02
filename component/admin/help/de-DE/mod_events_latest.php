<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_events_latest.php 1812 2011-03-21 10:04:50Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Events - Help german language


defined( '_JEXEC' ) or die( 'Restricted access' );

$_cal_lang_lev_main_help = <<<END
<div style="width:300px">
<p>
	Dieses - optionale - Modul zeigt die neuesten (und wenn so konfiguriert) auch vergangene Termine an.
	Das Modul ist <strong>nicht</strong> Bestandteil der Komponente und muss extra von der
	<a href="http://www.jevents.net" title="JEvents" target="_blank">JEvents Projektseite</a>
	downgeloaded und installiert, sowie Ver&ouml;ffentlicht werden!
</p>
<p>
	Die Ausgabe des Moduls erfolgt mittels einer HTML-Tabelle mit n Reihen mit 1 Kolumne, wobei der Wert n
	die Anzahl der angezeigten Termine angibt.
	Die maximale Anzeige wird mittels Parameter definiert.
</p>

<b>CSS Stil:</b>

<p>
	Jeder Termin begintn mit einem Beginndatum in der ersten Zelle, gefolgt von dem Termintitel in der 2. Zeile.
	Beide - das Datum sowie der Titel - haben eine eigene CSS-Klasse.
</p>
<p>
	Der erste angezeigte Termin hat ebenso seine eigene CSS-Klasse (mod_events_latest_first). Alle nachfolgenden
	Termine verwenden die CSS-Klasse (mod_events_latest). Die Termine werden durch eine horizontale Linie getrennt.
</p>
<p>
	<strong>Hinweis</strong>: Alle CSS-Klassen sind in der Modul-CSS-Datei enthalten und k&ouml;nnen
	&uuml;ber den Modulmanager automatisch eingebunden werden, oder von Hand in die CSS-Datei der
	Komponente hinzugef&uuml;gt werden(empfohlene Variante, da XHTML konform)</strong>
</p>
</div>
END;

$_cal_lang_lev_custformstr_help = <<<END
<div style="width:450px;font-size:xx-small;">
= string  Hier kann eine individuelle Formatierung zur Terminanzeige eingegeben werden (Format ist string).
Es kann sowohl eines der u.a. M&ouml;glichkeiten verwendet werden, als auch zus&auml;tzlich weitere HTML.Tags.
Weiters kann jedes Feld in der Form \${event_field} verwendet werden.<br />
Wenn gew&uuml;nscht k&ouml;nnen auch CSS-Formatierungsangaben verwendet werden
( &lt;div&gt; oder &lt;span&gt;). Ebenso k&ouml;nnen neue CSS-Klassen in der CSS-Datei definiert werden
und diese dann hier verwendet werden.<br /><br />

= [cond: string ]  Hier kann eine individuelle Formatierung zur Terminanzeige eingegeben werden
die nur angezeigt wird, wenn die Bedingung "cond" true ist.<br /><br />
	Verf&uuml;gbare Bedingungen:<br /><br />
	<b>a</b>&nbsp;Termin ist ganzt&auml;gig(ohne Zeitangaben)<br />
	<b>!a</b>&nbsp;Termin ist nicht ganzt&auml;gig(ohne Zeitangaben)<br /><br />

M&ouml;gliche Felder sind: \${startDate}, \${eventDate}, \${endDate}, \${title}, \${category}, \${contact},
\${content}, \${addressInfo}, \${extraInfo}, \${createdByAlias}, \${createdByUserName}, \${createdByUserEmail},
\${createdByUserEmailLink}, \${eventDetailLink}, \${color}<br /><br />
Die Felder <strong>\${startDate}, \${eventDate} und \${endDate}</strong> sind spezielle Formatierungsfelder,
welche weiters individuell angepasst werden k&ouml;nnen.<br />
Zum Einsatz kommen hier die PHP-Eigenen Funktionen [ date() und JevDate::strftime() ] - mehr darzu unter
<a href="http://de3.php.net/manual/de/function.date.php" title"PHP date" target="_blank">PHP date-funktion</a>
und <a href="http://de3.php.net/manual/de/function.JevDate::strftime.php" title"PHP JevDate::strftime" target="_blank">PHP JevDate::strftime-funktion</a><br /><br />
<strong>Hinweis</strong>: wird in der Formatierung ein <strong>%</strong> verwendet, wird
automatisch das JevDate::strftime-Format angewendet (lokale Spracheinstellungen werden unterst&uuml;tzt)!<br />
<strong>Hinweis</strong>: als Vorgabe wird '\${eventDate}[!a: - \${endDate(%I:%M%p)}]&lt;br /&gt;\${title}' verwendet,
damit sieht die  Terminanzeige so aus: Datum des Termins (oder wenn Mehrt&auml;giger oder Wiederkehrender dann den heutigen Teil).
Die Start- und Endezeit wird nur angezeigt, wenn es kein ganzt&auml;giger Termin ist(Bedingung !a).
</div>
END;

$_cal_lang_lev_date_help = <<<END
<div style="width:450px;">
<p><b><u>php date() Parameter:</u></b></p>
<table cellpadding="0" cellspacing="0" style="table-layout:auto;vertical-align:text-top;font-size:xx-small">
<colgroup>
	<col style="width: 30px;vertical-align:text-top;">
	<col style="vertical-align:text-top;">
</colgroup>
<tbody style="font-size:xx-small">
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">a</td>
		<td style="font-size: xx-small;">am (ante meridiem) und pm (post meridiem) Kleingeschrieben (US-Format)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">A</td>
		<td style="font-size: xx-small;">AM (ante meridiem) und PM (post meridiem) Grossgeschrieben  (US-Format)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">B</td>
		<td style="font-size: xx-small;">Swatch Internet Zeit ( 000 bis 999 )</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">d</td>
		<td style="font-size: xx-small;">Tag, 2 Stellen mit f&uuml;hrender Null (01 bis 31)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">D</td>
		<td style="font-size: xx-small;">Textform der Tage in Kurzform - 3 Buchstaben (Mon - Son)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">F</td>
		<td style="font-size: xx-small;">Textform der Monate - ausgeschrieben ( Jannuar bis Dezember)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">g</td>
		<td style="font-size: xx-small;">Stunden im 12-Stundenformat (1 bis 12) ohne f&uuml;hrende Null</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">G</td>
		<td style="font-size: xx-small;">Stunden im 24-Stundenformat (01 bis 24) mit f&uuml;hrender Null</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">h</td>
		<td style="font-size: xx-small;">Stunden im 12-Stundenformat (01 bis 12) mit f&uuml;hrender Null</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">H</td>
		<td style="font-size: xx-small;">Stunden im 24-Stundenformat (01 bis 23) mit f&uuml;hrender Null</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">i</td>
		<td style="font-size: xx-small;">Minuten mit f&uuml;hrender Null ( 00 bis 56)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">I</td>
		<td style="font-size: xx-small;">(Grosses i) Ber&uuml;cksichtigt eventuelle Sommer-/Winterzeit (1 ja, 0 nein)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">j</td>
		<td style="font-size: xx-small;">Tag ohne f&uuml;hrender Null (1 bis 31)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">l</td>
		<td style="font-size: xx-small;">(Kleines L) Tage voll ausgeschrieben (Sonntag bis Samstag)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">L</td>
		<td style="font-size: xx-small;">Anzeige ob Schaltjahr oder nicht (1 ja, 0 nein)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">m</td>
		<td style="font-size: xx-small;">Monat als Zahl f&uuml;hrender Null (01 bis 12)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">M</td>
		<td style="font-size: xx-small;">Monate im 3-Zeichenformat (Jan bis Dez)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">n</td>
		<td style="font-size: xx-small;">Monate als Zahl, ohne f&uuml;hrender Null (1 bis 12)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">O</td>
		<td style="font-size: xx-small;">Differenz zur Greenwich time (GMT) in Stunden. Beispiel: +0200</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">r</td>
		<td style="font-size: xx-small;">RFC 822 Konforme Datumsformatierung. Beispiel: Thu, 21 Dec 2000 16:01:07 +0200</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">s</td>
		<td style="font-size: xx-small;">Sekunden mit f&uuml;hrender Null (00 bis 59)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">S</td>
		<td style="font-size: xx-small;">Erweiterung bei Anzeige als anglikanisches Datum (st, nd, rd oder th). In Kombination mit dem Parameter j</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">t</td>
		<td style="font-size: xx-small;">Anzahl der Monatstage (28 bis 31)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">T</td>
		<td style="font-size: xx-small;">Zeitzone - Beipiele: EST, MDT, usw.</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">U</td>
		<td style="font-size: xx-small;">Sekunden seit Beginn der UNIX-Zeitrechnung( 1. Jannuar 1970 00:00:00 GMT)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">w</td>
		<td style="font-size: xx-small;">Wochentag als Zahl ( 0 - f&uuml;r Sonntag bis 6 - f&uuml;r Samstag)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">W</td>
		<td style="font-size: xx-small;">ISO-8601 Wochennummer, Woche startet mit Montag. Beispiel: 42 (42. woche des angezeigten Jahres)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">Y</td>
		<td style="font-size: xx-small;">Jahreszahl mit 4 Stellen (z.B. 2006)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">y</td>
		<td style="font-size: xx-small;">Jahreszahl mit 2 Stellen (z.B. 06 f&uuml;r 2006)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">z</td>
		<td style="font-size: xx-small;">Nummer des Tages im laufenden Jagr( 0 bis 366)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">Z</td>
		<td style="font-size: xx-small;">Zeitzonenunterscheid in Sekunden (nach Greenwich). Westlich daon negatibe Werte, &ouml;stlich davon positive Werte (-43200 bis 43200)</td>
	</tr>
</tbody>
</table>
</div>
END;

$_cal_lang_lev_strftime_help = <<<END
<div style="width:450px;">
<p><b><u>php JevDate::strftime() function Formate(formatiert entsprchend der System Locale Einstellung):</u></b></p>
<table cellpadding="0" cellspacing="0" style="table-layout:auto;vertical-align:text-top;font-size:xx-small">
<colgroup>
	<col style="width: 30px;vertical-align:text-top;">
	<col style="vertical-align:text-top;">
</colgroup>
<tbody style="font-size:xx-small">
	<tr style="vertical-align:text-top;">
		<td colspan="2" style="font-size: xx-small;"><strong>Hinweis</strong>: alle folgenden Einstellungen sind abh&auml;ngig von den richtigen Servereinstellungen!<br />
		Sollte die eine oder/und andere Einstellung nicht das gew&uuml;nschte Ergebnis anzeigen, dann bitte mit dem Provider in Verbindung setzen und ihn (eventuell) h&ouml;flich darauf hinweisen, dass er die lokalen Servereinstellungen &uuml;berpr&uuml;fen sollte!<br />&nbsp;
		</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%a</td>
		<td style="font-size: xx-small;">Abgek&uuml;rzter Wochentag</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%A</td>
		<td style="font-size: xx-small;">Voll ausgeschriebener Wochentag</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%b</td>
		<td style="font-size: xx-small;">Abgek&uuml;rzter Monatsname</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%B</td>
		<td style="font-size: xx-small;">Voll ausgeschriebener Monatsname</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%c</td>
		<td style="font-size: xx-small;">Bevorzugte Anzeige von Datum und Uhrzeit</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%C</td>
		<td style="font-size: xx-small;">Nummer des Jahrhunderts (Jahr dividiert durch 100, umgewandelt zu einem Integer, 00 bis 99)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%d</td>
		<td style="font-size: xx-small;">Tag als Dezimalzahl (01 bis 31)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%D</td>
		<td style="font-size: xx-small;">Das Gleiche wie %m/%d/%y</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%e</td>
		<td style="font-size: xx-small;">Aktueller Tag des Monats als Dezimalzahl ohne f&uuml;hrende Null, stattdessen mit Leerzeichen ( ' 1' bis '31')</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%g</td>
		<td style="font-size: xx-small;">Wie %G, aber ohne Jahrhundert</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%G</td>
		<td style="font-size: xx-small;">Jahreszahl mit 4 Stellen (z.B. 2006) in &Uuml;bereinstimmung zur ISO-Wochennummer (siehe auch %V)
		<br />
		Dieser Parameter verwendet im Prinzp dieselbe Ausgabe wie %V, ausgenommen wenn die ISO_Woche zum Vorjahr oder n&auml;chsten Jahr geh&ouml;rt, wird das jahr angezeigt</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%h</td>
		<td style="font-size: xx-small;">Wie %b</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%H</td>
		<td style="font-size: xx-small;">Stunde als Dezimlazahl im 24-Stundenformat (00 bis 23)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%I</td>
		<td style="font-size: xx-small;">Stunde als Dezimalzahl im 12-Stundenformat (01 to 12)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%j</td>
		<td style="font-size: xx-small;">Tag als Dezimalzahl mit f&uuml;hrenden Nullen (001 bis 366)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%m</td>
		<td style="font-size: xx-small;">Monat als Dezimalzahl mit f&uuml;hrender Null (01 bis 12)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%M</td>
		<td style="font-size: xx-small;">Minute asl Dezimalzahl mit f&uuml;hrender Null (00 bis 59)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%n</td>
		<td style="font-size: xx-small;">Neue Linie (als Trennzeichen)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%p</td>
		<td style="font-size: xx-small;">entweder 'am' oder 'pm' (abh&auml;ngig von der Zeitanzeige) oder die jeweilige Anzeige zur lokalen Einstellung</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%r</td>
		<td style="font-size: xx-small;">Zeit als a.m. oder p.m. (nur Anglikanisch)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%R</td>
		<td style="font-size: xx-small;">Zeit im 24-Stundenformat (00 bis 24)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%S</td>
		<td style="font-size: xx-small;">Sekunde als Dezimalzahl (00 bis 59)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td width=10% style="vertical-align:text-top;">%t</td>
		<td style="font-size: xx-small;">Abstand als Tab</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%T</td>
		<td style="font-size: xx-small;">Lokale Zeit (wie %H:%M:%S)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%u</td>
		<td style="font-size: xx-small;">Wochentag als Dezimalzahl (1 bis 7 - Montag ist 1!)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%U</td>
		<td style="font-size: xx-small;">Wochennummer des aktuellen jahres als Dezimalzahlm beginnend mit Sonntag als dem ersten Tag der ersten Woche</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%V</td>
		<td style="font-size: xx-small;">Kalenderwoche (nach ISO 8601:1988) des aktuellen Jahres. Als Dezimal-Zahl mit dem Wertebereich 01 bis 53, wobei die Woche 01 die erste Woche mit mindestens 4 Tagen im aktuellen Jahr ist. Die Woche beginnt montags (nicht sonntags). (Benutzen Sie %G or %g f&uuml;r die Jahreskomponente, die der Wochennummer für den gegebenen Timestamp entspricht.)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%W</td>
		<td style="font-size: xx-small;">Wochennummer des aktuellen jahres als Dezimalzahl, beginnend mit Montag als dem ersten Tag der ersten Woche</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%w</td>
		<td style="font-size: xx-small;">Tag der Woche als Dezimalzahl, beginnend mit Sonntag als 0</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%x</td>
		<td style="font-size: xx-small;">Anzeige des lokalen Datums ohne Uhrzeit</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%X</td>
		<td style="font-size: xx-small;">Anzeige der lokalen Uhrzeit ohne Datum</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%y</td>
		<td style="font-size: xx-small;">Jahr als Dezimalzahl mit 2 Stellen (00 bis 99)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%Y</td>
		<td style="font-size: xx-small;">Jahr alas 4-stellige Dezimalzahl (z.B. 2006)</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%Z</td>
		<td style="font-size: xx-small;">Zeitzone</td>
	</tr>
	<tr style="vertical-align:text-top;">
		<td style="font-size: xx-small;">%%</td>
		<td style="font-size: xx-small;">Wird als `%' angezeigt</td>
	</tr>
</tbody>
</table>
</div>
END;
?>
