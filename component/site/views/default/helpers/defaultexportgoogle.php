<?php 
/* 
 *@JEvents Helper for Generating Exports
 */

defined('_JEXEC') or die('Restricted access');

function DefaultExportGoogle ($view, $privatelink, $publiclink) {
	
		echo "<div class='jev_google_export'>";
		echo "<div class='jev_google_export_pub'><a href='http://www.google.com/calendar/render?cid=". urlencode($publiclink) ."' target='_blank'><img src='". JURI::root() ."/components/com_jevents/images/gc_button6.gif' border='0'></a>";
		echo JText::_('JEV_REP_ICAL_PUBLIC_WEBCAL_SHORT') . "</div>\n";
		$user = JFactory::getUser();
		if ($user->id != 0)
		{
			echo "<div class='jev_google_export_priv'><a href='http://www.google.com/calendar/render?cid=". urlencode($privatelink) ."' target='_blank'><img src='". JURI::root() ."/components/com_jevents/images/gc_button6.gif' border='0'></a>";
			echo JText::_('JEV_REP_ICAL_PRIVATE_WEBCAL_SHORT'). "</div>\n";
		}
		echo"</div>";
}