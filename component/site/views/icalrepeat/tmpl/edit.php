<?php
defined('_JEXEC') or die('Restricted access');

$this->loadModules("jevpreeditrepeat");

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'jui/icomoon.css', array('version' => 'auto', 'relative' => true));

include_once(JEV_ADMINPATH . "/views/icalrepeat/tmpl/" . basename(__FILE__));

$this->loadModules("jevposteditrepeat");
