<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Component\ComponentHelper;

if (!isset($this->jevviewdone))
{
	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	if ($params->get("newfrontendediting", 1))
	{
		echo LayoutHelper::render('gslframework.header', null, JPATH_COMPONENT_ADMINISTRATOR . "/layouts");
	}

	$this->loadModules("jevpreeditevent");

	include_once(JEV_ADMINPATH . "/views/icalevent/tmpl/" . basename(__FILE__));

	/*
	$bar = JToolBar::getInstance('toolbar');
	$barhtml = $bar->render();
	$barhtml = str_replace('id="','id="x', $barhtml);
	echo $barhtml;
	 */
	$this->jevviewdone = true;

	$this->loadModules("jevposteditevent");

	if ($params->get("newfrontendediting", 1))
	{
		echo LayoutHelper::render('gslframework.footer', null, JPATH_COMPONENT_ADMINISTRATOR . "/layouts");
	}
}