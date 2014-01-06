<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1406 2009-04-04 09:54:18Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
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
class FlatViewMonth extends JEventsFlatView
{

	function calendar($tpl = null)
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		JEVHelper::script('layout.js', 'components/'.JEV_COM_COMPONENT."/views/".$this->jevlayout."/assets/js/" );
		
		$this->data = $this->datamodel->getCalendarData($this->year, $this->month, $this->day );

		// for adding events in day cell
		$this->popup=false;
		if ($params->get("editpopup",0)){
			JHTML::_('behavior.modal');
			JHTML::script('components/'.JEV_COM_COMPONENT.'/assets/js/editpopup.js');
			$this->popup=true;
			$this->popupw = $params->get("popupw",800);
			$this->popuph = $params->get("popuph",600);
		}

		$this->is_event_creator = JEVHelper::isEventCreator();

	}
/*
	public function sortjevents($a,$b){
		if ($a->_publish_up == $b->_publish_up) {
			return 0;
		}
		return ($a->_publish_up < $b->_publish_up) ? -1 : 1;
	}
	*/
}
