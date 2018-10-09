<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;

function DefaultInformation16($view)
{

	// Disable if any club addons are installed
	$plugins = PluginHelper::getPlugin("jevents");
	if (count($plugins) > 0)
	{
		return;
	}

	// Remove backlink completely
	return;

	echo '<li class="info-icon">';
	echo '<a href="https://www.jevents.net" target="_blank" rel="nofollow">'
		. "<img src=\"" . Uri::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images/help.gif\" border=\"0\" alt=\"help\" class='jev_help' />"
		. "</a>";
	echo "</li>";
}

