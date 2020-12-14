<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

$this->_header();
$this->_showNavTableBar();

$user = Factory::getUser();
if ($user->id > 0)
{
	echo $this->loadTemplate("body");
}

$this->_viewNavAdminPanel();

$this->_footer();


