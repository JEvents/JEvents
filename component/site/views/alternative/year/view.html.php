<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML View class for the component frontend
 *
 * @static
 */
#[\AllowDynamicProperties]
class AlternativeViewYear extends JEventsAlternativeView
{
	function listevents($tpl = null)
	{

		JEVHelper::componentStylesheet($this);
		$document = Factory::getDocument();
		$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);

	}

}
