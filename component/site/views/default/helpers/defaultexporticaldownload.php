<?php

/*
 * @JEvents Helper for Generating Exports - Ical Download
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

function DefaultExportIcalDownload($view, $publiclink, $privatelink)
{

	echo "<div class='ical_form_button export_public'><a href='$publiclink'>" . Text::_('JEV_REP_ICAL_PUBLIC') . "</a></div>";

	$user = Factory::getUser();
	if ($user->id != 0)
	{
		echo "<div class='ical_form_button export_private'><a href='$privatelink'>" . Text::_('JEV_REP_ICAL_PRIVATE') . "</a></div>";
	}
}