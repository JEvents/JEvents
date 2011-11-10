<?php 
defined('_JEXEC') or die('Restricted access');

$this->loadModules("jevpreeditevent");

include_once(JEV_ADMINPATH."/views/icalevent/tmpl/".basename(__FILE__));

$this->loadModules("jevposteditevent");