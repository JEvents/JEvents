<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 1406 2009-04-04 09:54:18Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class flatViewWeek extends JEventsflatView 
{
	function listevents($tpl = null)
	{
		JEVHelper::componentStylesheet($this);
		$document = JFactory::getDocument();						
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

	}	
}