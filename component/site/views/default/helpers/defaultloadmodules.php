<?php

defined('_JEXEC') or die('Restricted access');

function DefaultLoadModules($view, $position)
{


	$cfg = & JEVConfig::getInstance();

	$article = JTable::getInstance('content');
	$article->text = "{loadposition $position}";

	$app = JFactory::getApplication();
	$params = JComponentHelper::getParams("com_content");

	JPluginHelper::importPlugin('content');
	$dispatcher	= JDispatcher::getInstance();
	if (JVersion::isCompatible("1.6"))
	{
		$results = $dispatcher->trigger('onContentPrepare', array('com_content.article', &$article, &$params, 0));
	}
	else
	{
		$results = $dispatcher->trigger('onPrepareContent', array('com_content.article', &$article, &$params, 0));
	}

	echo $article->text;

}

