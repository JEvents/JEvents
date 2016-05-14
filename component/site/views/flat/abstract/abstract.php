<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: abstract.php 1440 2009-05-11 08:22:54Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML Abstract view class for the component frontend
 *
 * @static
 */
JLoader::register('JEventsDefaultView', JEV_VIEWS . "/default/abstract/abstract.php");

class JEventsFlatView extends JEventsDefaultView
{

	var
			$jevlayout = null;

	function __construct($config = null)
	{
		parent::__construct($config);

		$this->jevlayout = "flat";

		$this->addHelperPath(dirname(__FILE__) . "/../helpers/");

		$this->addHelperPath(JPATH_BASE . '/' . 'templates' . '/' . JFactory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . "helpers");
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		JEVHelper::componentStylesheet($this);

		if ($params->get("darktemplate", 0))
			JEVHelper::componentStylesheet($this, "dark.css");

		$stylelink = '<!--[if lte IE 6]>' . "\n";
		$stylelink .= '<link rel="stylesheet" href="' . JURI::root() . 'components/com_jevents/views/flat/assets/css/ie6.css" />' . "\n";
		$stylelink .= '<![endif]-->' . "\n";
		$document->addCustomTag($stylelink);

		$this->colourscheme = $params->get("flatcolourscheme", "red");
		if ($this->colourscheme == "gray")
		{
			//$this->colourscheme = "";
		}

	}

}
