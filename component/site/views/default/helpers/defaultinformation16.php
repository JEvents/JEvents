<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultInformation16($view){
	echo '<li class="info-icon">';
	echo '<a href="http://www.jevents.net" target="_blank">'
	. "<img src=\"" . JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$view->getViewName()."/assets/images/help.gif\" border=\"0\" alt=\"help\" class='jev_help' />"
	. "</a>";
	echo "</li>";
}

