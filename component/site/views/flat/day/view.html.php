<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1415 2009-04-17 18:26:00Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
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
class flatViewDay extends JEventsflatView 
{
	function listevents($tpl = null)
	{
		JEVHelper::componentStylesheet($this);
		$document = JFactory::getDocument();						
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	}	
}
