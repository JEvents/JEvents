<?php

/*
 * @JEvents Helper for Generating Exports - Webcal
 */

defined('_JEXEC') or die('Restricted access');

function DefaultExportWebcal($view, $publiclink, $privatelink)
{
	//Webcal Subscribe button:
	//Replace http with webcal	
	$webcalurl_pub = str_replace(array('http:', 'https:'), 'webcal:', $publiclink);
	echo "<div class='ical_form_button export_public'><a href='$webcalurl_pub'>" . JText::_('JEV_REP_ICAL_PUBLIC_WEBCAL') . "</a></div>";
	
	$user = JFactory::getUser();
	if ($user->id != 0)
	{
			//Webcal Subscribe button:
			//Replace http with webcal	
			$webcalurl_priv = str_replace(array('http:', 'https:'), 'webcal:', $privatelink);
			echo "<div class='ical_form_button export_private'><a href='$webcalurl_priv'>" . JText::_('JEV_REP_ICAL_PRIVATE_WEBCAL') . "</a></div>";
	}

}