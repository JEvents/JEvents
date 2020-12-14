<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;

$this->_header();
$this->_showNavTableBar();

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

echo $this->loadTemplate("body");


$this->_viewNavAdminPanel();

$this->_footer();


