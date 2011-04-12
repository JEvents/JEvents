<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class DefaultsModelDefaults extends JModel
{

	/**
	 * Method to get weblinks item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__jev_defaults");
			$this->_data = $db->loadObjectList();					
		}
		return $this->_data;
	}

	/**
	 * Method to get the total number of weblink items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			// Manually add the form data which is not stored in the database in the same way
			$db = JFactory::getDBO();
			$db->setQuery("SELECT count(*) FROM #__jev_defaults");
			$this->_total = $db->loadResult();

		}

		return $this->_total;
	}


}
