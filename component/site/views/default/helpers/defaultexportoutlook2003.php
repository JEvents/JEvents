<?php

/*
 * @JEvents Helper for Generating Exports - Outlook 2003
 */

defined('_JEXEC') or die('Restricted access');

function DefaultExportOutlook2003($view, $publiclink, $privatelink)
{
	$user = JFactory::getUser();
	if ($user->id != 0)
	{
		echo "<div class='ical_form_button export_public'><h3>" . JText::_('JEV_ICAL_OUTLOOK_SPECIFIC') . "</h3></div>";
	} else {
		echo "<div class='ical_form_button export_public clearleft' ><h3>" . JText::_('JEV_ICAL_OUTLOOK_SPECIFIC') . "</h3></div>";
	}
	echo "<div class='ical_form_button export_public'><a href='$publiclink&outlook2003=1'>" . JText::_('JEV_REP_ICAL_PUBLIC') . "</a></div>";
	
	if ($user->id != 0)
	{
		echo "<div class='ical_form_button export_private'><a href='$privatelink&outlook2003='>" . JText::_('JEV_REP_ICAL_PRIVATE') . "</a></div>";
	}

}