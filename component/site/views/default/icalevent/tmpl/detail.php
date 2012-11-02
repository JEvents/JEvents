<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();
//  don't show navbar stuff for events detail popup
if( !$this->pop ){
	$this->_showNavTableBar();
}

if (JVersion::isCompatible("1.6.0")){
	echo $this->loadTemplate("body16");
}
else {
	echo $this->loadTemplate("body");
}

if( !$this->pop ){
	$this->_viewNavAdminPanel();
}

$this->_footer();
