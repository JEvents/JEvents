<?php

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

function DefaultLoadModules($view, $position)
{

	$cfg = JEVConfig::getInstance();

	$article       = Table::getInstance('content');
	$article->text = "{loadposition $position}";

	$app    = Factory::getApplication();
	$params = ComponentHelper::getParams("com_content");

	PluginHelper::importPlugin('content');

	$results    = Factory::getApplication()->triggerEvent('onContentPrepare', array('com_content.article', &$article, &$params, 0));

	if ($article->text == "{loadposition $position}")
	{
		// in case the content plugin is not enabled
		return "";
	}
	else
	{
		echo $article->text;
	}

}

