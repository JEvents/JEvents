<?php
/* 
 *@JEvents Helper for Generating Exports
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

function DefaultExportGoogle($view, $publiclink, $privatelink)
{

	echo "<div class='jev_google_export'>";
	echo "<div class='jev_google_export_pub'><a href='http://www.google.com/calendar/render?cid=" . urlencode(str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $publiclink)) . "' target='_blank'><img src='" . Uri::root() . "components/com_jevents/images/gc_button6.gif' border='0'></a>";
	echo Text::_('JEV_REP_ICAL_PUBLIC_WEBCAL_SHORT') . "</div>\n";
	$user = Factory::getUser();
	if ($user->id != 0)
	{

		echo "<div class='jev_google_export_priv'><a href='http://www.google.com/calendar/render?cid=" . urlencode(str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $privatelink)) . "' target='_blank'><img src='" . Uri::root() . "components/com_jevents/images/gc_button6.gif' border='0'></a>";
		echo Text::_('JEV_REP_ICAL_PRIVATE_WEBCAL_SHORT') . "</div>\n";
	}
	echo "</div>";
}