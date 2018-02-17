<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
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
class ExtViewICalevent extends JEventsExtView 
{
	
	public function detail($tpl = null)
	{
		JEVHelper::componentStylesheet($this);

		$jinput = JFactory::getApplication()->input;

		$this->data = $this->datamodel->getEventData( $this->evid, $this->jevtype, $this->year, $this->month, $this->day, $this->uid );
		// Dynamic pathway
		if (isset($this->data['row'])){
			$pathway = JFactory::getApplication()->getPathway();

			$pathway->addItem($this->data['row']->title() ,"");

			// Set date in view for use in navigation icons
			$this->year = $this->data['row']->yup();
			$this->month = $this->data['row']->mup();
			$this->day = $this->data['row']->dup();

			// seth month and year to be used by mini-calendar if needed
			if (!$jinput->getInt("month", 0)) $jinput->set("month", $this->month);
			if (!$jinput->getInt("year", 0))  $jinput->set("year", $this->year);
		}
		
	}	
}
