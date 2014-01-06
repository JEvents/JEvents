<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();
$this->_showNavTableBar();

$user =  JFactory::getUser();
if (!( strtolower( JEVHelper::getUserType($user) ) == '')) {
	echo $this->loadTemplate("body");
}

$this->_viewNavAdminPanel();

$this->_footer();


