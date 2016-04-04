<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');


class DefaultsModelDefault extends JModelLegacy
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
		$jinput = JFactory::getApplication()->input;
		$id = $jinput->getInt("id");
		$edit	= $jinput->getBool('edit', true);
		if($edit){
			$this->setId($id);
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

		if (isset($data["params"]) && is_array($data["params"])){
			if (isset($data["id"])) {
				$this->setId(intval($data["id"]));
				$this->_loadData();
				if (isset($this->_data->params) && $this->_data->params!=""){
					$oldparams = json_decode($this->_data->params);
					if (!is_array($oldparams)){
						$keys = array_keys(get_object_vars($oldparams));
						foreach ($keys as $key){
							if ($key == "modid" || $key=="modval"){
								continue;
							}
							$data["params"][$key] = $oldparams->$key;
						}
					}
				}
				$data["params"] = json_encode($data["params"]);
			}
		}

		$row =  $this->getTable();

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
			$query = 'SELECT d.* , c.title as category_title FROM #__jev_defaults as d ' .
			'LEFT JOIN #__categories as c on c.id = d.catid' .
			' WHERE d.id = '.$this->_db->Quote($this->_id);
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			//echo $this->_db->getErrorMsg();
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
			$default->id				= 0;
			$default->name				= "";
			$default->title				= "";
			$default->subject			= "";
			$default->value				= "";
			$default->_state			= 1;
			$this->_data				= $default;
			return (boolean) $this->_data;
		}
		return true;
	}


}