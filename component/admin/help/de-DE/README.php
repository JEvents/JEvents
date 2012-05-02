<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: README.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Readme Events - german language


defined( '_JEXEC' ) or die( 'Restricted access' );

// required during install!
include_once(JPATH_ADMINISTRATOR."/components/".JEV_COM_COMPONENT."/libraries/version.php");

$version = JEventsVersion::getInstance();
?>
<style type="text/css" media="screen">
    <!--
    h1 {
        color           : #30559C;
        font-size       : 112%;
        border-left     : 25px solid #30559C;
        border-bottom   : 1px solid #30559C;
        padding         : 0 0 2px 5px;
        width			: 95%;
        text-align		: left;
    }
    pre {
        color   		: #FF0000;
    }
    .text {
    	color			: #666666;
    	text-align		: left;
    	margin			: 10px;
    }
    hr {
    	border-bottom   : 1px solid #30559C;
    }
    .tip {
    	color			: #FF0000;
    	font-weight     : bold;
    }
    .ads {
        white-space 	: pre;
        border      	: 1px solid #336699;
        padding     	: 5px;
        margin      	: auto;
        width       	: 750px;
        background  	: #F9FDFF;
        text-align  	: center;
        clear			: both;
    }
    .hint {
        background  	: #FFDDDD;
        border      	: 1px solid #FF0000;
        margin      	: 5px;
        padding     	: 5px;
    }
    .highlight {
    	color           : #30559C;
    	font-weight     : bold;
    }
    .docinfo {
    	font-size		: 9pt;
    	color			: #666666;
    }
    -->
</style>
<h1>JEvents - Termine und Veranstaltungen leicht gemacht</h1>
<div class="text">
	<ul>
		<li>Version <?php echo $version->getShortVersion();?> - <a href="http://www.jevents.net/" target="_blank" title="Projektseite">Projektseite</a></li>
		<li><?php echo $version->getLongCopyright();?></li>
		<li>Copyright (C) 2000 - 2003 Eric Lamette, Dave McDonnell</li>
		<li>Voraussetzung: <a href="http://www.joomla.org" target="_blank" title="Joomla">Joomla</a> 1.x</li>
		<li>Lizenz: GNU/GPL <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank" title="Lizenz">Lizenz</a></li>
		<li>Webseite: <a href="http://www.jevents.net/" target="_blank" title="JEvents">JEvents</a></li>
		<li>Support: <a href="http://www.jevents.net/" target="_blank" title="JEvents">JEvents</a></li>
		<li>Email: <a href="http://www.jevents.net/" target="_blank" title="JEvents">JEvents</a></li>
	</ul>
	<hr />

    <div>
    	JEvents ist eine Zusatzkomponente zur Darstellung von Terminen und Veranstaltungen. Die Features:
    	<ol>
    		<li>vollst&auml;ndige Verwaltung diverser Einstellungen im Backend</li>
    		<li>beliebig viele Kategorien</li>
    		<li>beliebig viele Termine</li>
    		<li>farbliche Unterscheidung (zuordenbar und frei w&auml;hlbar) der Termine</li>
    		<li>Tages-, Wochen-, Monats- und Jahrestermine</li>
    		<li>wiederkehrende Termine</li>
    		<li>.... und viele weitere Einstellungsm&ouml;glichkeiten ....</li>
    	</ol>
    </div>
    <div>
        <span class="highlight">JEvents</span> besteht aus mehreren Teilen:
        <ul>
        	<li>Komponente</li>
        	<li>Modulen "MiniKalender", "N&auml;chste Termine" und "Legende der Terminkategorien"</li>
        	<li>Bot (zur Suche innerhalb der Termine, eingebunden in die generelle Suche)</li>
        </ul>
    </div>
    <div class="hint">
        <span class="tip">Hinweis</span>Zur Anzeige der Termine in der Seitenleiste muss ebenso das
        JEvents-Modul (MiniKalender) installiert werden, andernfalls k&ouml;nnen die Termine nur als Kalender
        in der Artikelhauptspalte dargestellt werden
    </div>
    <div>
    	Das Modul MiniKalender und Suchbot sind extra herunterzuladen und nach Installation freizugeben.
    </div>
    <div>
    	<span class="highlight">Changelog</span>
   		<div style='font-weight:bold'>version 1.4.0</div>
    	<ul>
            <li>Backend voll mehsprachenf&auml;hig</li>
            <li>Backend umgestellt auf aktuelle Routinen</li>
            <li>zu lange Terminbezeichnungen k&ouml;nnen im Frontend abgek&uuml;rzt angezeigt werden</li>
            <li>umfangreiche Hilfetexte (Backend)</li>
            <li>Adminmen&uuml;s in Benutzersprache</li>
            <li>etliche Fehlerbereinigungen</li>
            <li>XHTML-konforme Ausgabe</li>
        </ul>
   		<div style='font-weight:bold'>version 1.3.x_beta</div>
    	<ul>
            <li>Aktivierung des CMS-eigenen Chache</li>
            <li>Joomfish kompatibel</li>
            <li>Slovenisch, Finnisch, D&auml;nisch, Griechisch hinzugef&uuml;gt</li>
            <li>Spanisch &uuml;berarbeitet</li>
            <li>M&ouml;glichkeit f&uuml;r alle Benutzer Termine hinzuzuf&uuml;gen (mit anschliessender Genehmigung)</li>
            <li>Beschreibung nicht mehr notwendig</li>
            <li>Freigeben von Terminen im Frontend</li>
            <li>viele kleinere Fehlerbereinigungen</li>
        </ul>
    </div>
    <hr />
	<div>
    	Wir w&uuml;nschen viel Vergn&uuml;gen mit dieser Komponente
    </div>
    <div class="docinfo">Doc.Revision: 1.0 - 2006.08.06</div>
</div>
