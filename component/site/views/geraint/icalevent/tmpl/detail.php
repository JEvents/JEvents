<?php 
defined('_JEXEC') or die('Restricted access');

ob_start();
echo $this->loadTemplate("body");
$body = ob_get_clean();

$this->_header();
//  don't show navbar stuff for events detail popup
if( !$this->pop ){
	$this->_showNavTableBar();
}
echo $body;
if( !$this->pop ){
	$this->_viewNavAdminPanel();
}

$this->_footer();
