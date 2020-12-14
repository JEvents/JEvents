<?php

/*
 * @JEvents Helper for Generating Exports - Webcal
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

function DefaultExportWebcal($view, $publiclink, $privatelink)
{

	//Webcal Subscribe button:
	//Replace http with webcal	
	$webcalurl_pub = str_replace(array('http:', 'https:'), array('webcal:', 'webcal:'), $publiclink);
	echo "<div class='ical_form_button export_public'><a href='$webcalurl_pub'>" . Text::_('JEV_REP_ICAL_PUBLIC_WEBCAL') . "</a></div>";

	$user = Factory::getUser();
	if ($user->id != 0)
	{
		//Webcal Subscribe button:
		//Replace http with webcal	
		$webcalurl_priv = str_replace(array('http:', 'https:'), array('webcal:', 'webcal:'), $privatelink);
		echo "<div class='ical_form_button export_private'><a href='$webcalurl_priv'>" . Text::_('JEV_REP_ICAL_PRIVATE_WEBCAL') . "</a></div>";
	}

}