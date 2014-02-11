<?php 
defined('_JEXEC') or die('Restricted access');

function flatsortjevents($view, $a,$b){
	if ($a->_publish_up == $b->_publish_up) {
		return 0;
	}
	return ($a->_publish_up < $b->_publish_up) ? -1 : 1;
}