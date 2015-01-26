<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultInformation16($view){
	// disable if any club addons are installed
	$plugins = JPluginHelper::getPlugin("jevents");
	if (count($plugins)>0){
		return;
	}	
	
	// remove backlink completely
	return;

	echo '<li class="info-icon">';
	echo '<a href="https://www.jevents.net" target="_blank" rel="nofollow">'
	. "<img src=\"" . JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$view->getViewName()."/assets/images/help.gif\" border=\"0\" alt=\"help\" class='jev_help' />"
	. "</a>";
	echo "</li>";
}

