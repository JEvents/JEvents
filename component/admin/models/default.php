<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');


class DefaultsModelDefault extends JModel
{
	/**
	 * id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$name = JRequest::getString('name',  '');
		$edit	= JRequest::getVar('edit',true);
		if($edit){
			$this->setId($name);
		}
	}

	function setId($id)
	{
		// Set session id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a session
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the session data
		if ($this->_loadData())
		{

		}
		else  $this->_initData();

		return $this->_data;
	}


	/**
	 * Method to store the data
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		$row = & $this->getTable();

		// Bind the form fields to the session table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the session table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to load content default data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT * FROM #__jev_defaults' .
			' WHERE name = '.$this->_db->Quote($this->_id);
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$default = new stdClass();
			$default->name				= "";
			$default->title				= "";
			$default->subject			= "";
			$default->value				= "";
			$detaulf->_state			= 1;
			$this->_data				= $default;
			return (boolean) $this->_data;
		}
		return true;
	}


}