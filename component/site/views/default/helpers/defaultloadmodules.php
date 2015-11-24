<?php

defined('_JEXEC') or die('Restricted access');

function DefaultLoadModules($view, $position)
{


	$cfg = JEVConfig::getInstance();

	$article = JTable::getInstance('content');
	$article->text = "{loadposition $position}";

	$app = JFactory::getApplication();
	$params = JComponentHelper::getParams("com_content");

	JPluginHelper::importPlugin('content');
	$dispatcher	= JEventDispatcher::getInstance();
	$results = $dispatcher->trigger('onContentPrepare', array('com_content.article', &$article, &$params, 0));

        if ($article->text == "{loadposition $position}"){
		// in case the content plugin is not enabled
		return "";
	}
	else {
		echo $article->text;
	}

}

