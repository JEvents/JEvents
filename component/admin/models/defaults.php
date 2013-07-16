<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class DefaultsModelDefaults extends JModelLegacy
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
			$query	= $db->getQuery(true);
			
			$query->select("def.*");
			$query->from("#__jev_defaults as def");

			// Join over the language
			$query->select('l.title AS language_title');
			// some servers have mixed collations
			$query->join('LEFT', $db->quoteName('#__languages').' AS l ON BINARY l.lang_code = BINARY def.language');
			
			$language  = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_language", 'filter_language', "*");
			if (count ($this->getLanguages())==1){
				$language = "*";
			}
			if ($language  != "" ){
				$query->where('def.language = '.$db->quote($language));
			}
			
			$filter_published  = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_published", 'filter_published', "");
			if ($filter_published  != "" ){
				$query->where('def.state = '.intval($filter_published));
			}

			$layouttype = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_layout_type", 'filter_layout_type', "jevents");			
			if ($layouttype  != "" ){
				if ($layouttype=="jevlocations"){
					$query->where('def.name like ("com_jevlocations%")');
				}
				if ($layouttype=="jevpeople"){
					$query->where('def.name like ("com_jevpeople%")');
				}
				if ($layouttype=="jevents"){
					$query->where('def.name NOT like ("com_jevpeople%") AND def.name NOT like ("com_jevlocations%")' );
				}
			}
			
			$query->order("def.title asc");
			$db->setQuery($query);
			$this->_data = $db->loadObjectList();		
			//var_dump($this->_data);
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
			$language  = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_language", 'filter_language', "*");
			
			$query	= $db->getQuery(true);
			
			$query->select("count(*)");
			$query->from("#__jev_defaults as def");
			
			if (count ($this->getLanguages())==1){
				$language = "*";
				if ($language  != "" ){
					$query->where('def.language = '.$db->quote($language));
				}
			}
			$db->setQuery($query);
			
			$this->_total = $db->loadResult();
		}

		return $this->_total;
	}


	function getLanguages()
	{
		static  $languages;
		if (!isset($languages)){
			$db = JFactory::getDBO();

			// get the list of languages first 
			$query	= $db->getQuery(true);
			$query->select("l.*");
			$query->from("#__languages as l");
			$query->where('l.lang_code <> "xx-XX"');
			$query->order("l.lang_code asc");

			$db->setQuery($query);
			$languages  = $db->loadObjectList('lang_code');
		}
		return $languages;
	}
}
