<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
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
class DefaultViewIcal extends JEventsDefaultView
{

	function ical($tpl = null)
	{
		JEVHelper::componentStylesheet($this);

		$document = JFactory::getDocument();
		// TODO do this properly
		//$document->setTitle(JText::_( 'BROWSER_TITLE' ));

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$this->assign("introduction", $params->get("intro",""));

		$this->data = $this->datamodel->getCalendarData($this->year, $this->month, $this->day);

		// for adding events in day cell
		$this->popup = false;
		if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
		{
			JevHtmlBootstrap::modal();
			JEVHelper::script('editpopupJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
			$this->popup = true;
			$this->popupw = $params->get("popupw", 800);
			$this->popuph = $params->get("popuph", 600);
		}

		$this->is_event_creator = JEVHelper::isEventCreator();

	}

}
