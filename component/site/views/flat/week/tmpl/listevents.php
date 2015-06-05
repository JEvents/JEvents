<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();
$this->_showNavTableBar();

$cfg	 = JEVConfig::getInstance();

echo $this->loadTemplate("responsive");

$this->_viewNavAdminPanel();

$this->_footer();


