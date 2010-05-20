<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultInformation($view){
	echo '<td class="buttonheading" align="right">';
	echo '<a href="http://www.jevents.net" target="_blank">'
	. "<img src=\"" . JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$view->getViewName()."/assets/images/help.gif\" border=\"0\" alt=\"help\" class='jev_help' />"
	. "</a>";
	echo "</td>";
}

