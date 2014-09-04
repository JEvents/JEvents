<?php 
defined('_JEXEC') or die('Restricted access');

function FlatViewHelperHeader($view){
	if (version_compare(JVERSION, "1.6.0", 'ge')){
		return $view->_header16();
	}
}
