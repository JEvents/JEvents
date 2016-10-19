<?php 
defined('_JEXEC') or die('Restricted access');

if (!isset($this->jevviewdone)){
	$this->loadModules("jevpreeditevent");

	include_once(JEV_ADMINPATH."/views/icalevent/tmpl/".basename(__FILE__));

	/*
	$bar =  JToolBar::getInstance('toolbar');
	$barhtml = $bar->render();
	$barhtml = str_replace('id="','id="x', $barhtml);
	echo $barhtml;
	 */
	$this->jevviewdone = true;
	
	$this->loadModules("jevposteditevent");
        
}