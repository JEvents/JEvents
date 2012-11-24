<?php 
defined('_JEXEC') or die('Restricted access');

$this->loadModules("jevpreeditrepeat");

include_once(JEV_ADMINPATH."/views/icalrepeat/tmpl/".basename(__FILE__));

$this->loadModules("jevposteditrepeat");
