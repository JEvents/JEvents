<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();
$this->_showNavTableBar();

$cfg	 = JEVConfig::getInstance();

if (intval($cfg->get('rollingweeks',1)>1)){
	$rolling = "rolling";
}
else {
	$rolling = "";
}
echo $this->loadTemplate($rolling."responsive");

$this->_viewNavAdminPanel();

$this->_footer();


