<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;

function DefaultInformation($view)
{

	// disable if any club addons are installed
	$plugins = PluginHelper::getPlugin("jevents");
	if (count($plugins) > 0)
	{
		return;
	}

	// remove backlink completely
	return;

	echo '<td class="buttonheading" >';
	echo '<a href="http://www.jevents.net" target="_blank" rel="nofollow">'
		. "<img src=\"" . Uri::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images/help.gif\" border=\"0\" alt=\"help\" class='jev_help' />"
		. "</a>";
	echo "</td>";
}

