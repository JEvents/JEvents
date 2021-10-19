<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

jimport('joomla.application.component.model');

class DefaultsModelDefault extends BaseDatabaseModel
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
		$input = Factory::getApplication()->input;
		$id     = $input->getInt("id");
		if ($id == 0)
		{
			$this->modid       = $input->getInt("modid");
			$this->layouttype  = $input->getCmd("type", "module.latest_event");
		}
		$edit   = $input->getBool('edit', true);
		if ($edit)
		{
			$this->setId($id);
		}
	}

	function setId($id)
	{

		// Set session id and wipe data
		$this->_id   = $id;
		$this->_data = null;
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
	 * Method to load content default data
	 *
	 * @access    private
	 * @return    boolean    True on success
	 * @since     1.5
	 */
	function _loadData()
	{

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			if ($this->_id > 0)
			{
				$query = 'SELECT d.* , c.title as category_title FROM #__jev_defaults as d ' .
					'LEFT JOIN #__categories as c on c.id = d.catid' .
					' WHERE d.id = ' . $this->_db->Quote($this->_id);
				$this->_db->setQuery($query);
				$this->_data = $this->_db->loadObject();

				$input = Factory::getApplication()->input;
				$catid = $input->getInt("catid", -1);

				// we have changed the category in the edit page so we should find the matching layout
				if ($catid >-1 && $this->_data && intval($this->_data->catid) !== $catid)
				{
					$query = 'SELECT d.* , c.title as category_title FROM #__jev_defaults as d ' .
						'LEFT JOIN #__categories as c on c.id = d.catid' .
						' WHERE d.catid = ' . $this->_db->Quote($catid) .
						' AND   d.name = '  . $this->_db->Quote($this->_data->name) .
						' AND   d.language = ' . $this->_db->Quote($this->_data->language)
					;
					$this->_db->setQuery($query);
					$this->_data = $this->_db->loadObject();
				}
			}
			else if ($this->modid > 0)
			{
				$db = Factory::getDbo();
				$db->setQuery("SELECT * FROM #__jev_defaults as d WHERE d.catid = 0 AND d.language = '*'");
				$defaults = $db->loadObjectList("name");

				$layoutname = $this->layouttype . '.' . $this->modid;
				if (!isset($defaults[$layoutname ]))
				{
					$db->setQuery("INSERT INTO  #__jev_defaults set name='$layoutname',
						title=" . $db->Quote("JEV_TAB_LATEST_MOD") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
					$db->execute();
					$db->setQuery("SELECT * FROM #__jev_defaults as d WHERE d.catid = 0 AND d.language = '*'");
					$defaults = $db->loadObjectList("name");
				}
				else
				{
					$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_TAB_LATEST_MOD") . " WHERE name='$layoutname'");
					$db->execute();
				}
				$this->_data = $defaults[ $layoutname ];

			}

			return (boolean) $this->_data;
		}

		return true;
	}

	function _initData()
	{

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$default          = new stdClass();
			$default->id      = 0;
			$default->name    = "";
			$default->title   = "";
			$default->subject = "";
			$default->value   = "";
			$default->_state  = 1;
			$this->_data      = $default;

			return (boolean) $this->_data;
		}

		return true;
	}

	/**
	 * Method to store the data
	 *
	 * @access    public
	 * @return    boolean    True on success
	 * @since     1.5
	 */
	function store($data)
	{

		if (isset($data["params"]) && is_array($data["params"]))
		{
			if (isset($data["id"]))
			{
				$this->setId(intval($data["id"]));
				$this->_loadData();
				if (isset($this->_data->params) && $this->_data->params != "")
				{
					$oldparams = json_decode($this->_data->params);
					if (!is_array($oldparams))
					{
						$keys = array_keys(get_object_vars($oldparams));
						foreach ($keys as $key)
						{
							if ($key == "modid" || $key == "modval" || $key == "customjs"  || $key == "customcss" || $key == "header" || $key == "footer"  || $key == "columnsL")
							{
								continue;
							}
							$data["params"][$key] = $oldparams->$key;
						}
					}
				}
				$data["params"] = json_encode($data["params"]);
			}
		}

		if ($data["catid"] == "")
		{
			$data["catid"] = 0;
		}
		$row = $this->getTable();

		// Bind the form fields to the session table
		$row->bind($data);

		// Store the session table to the database
		if (!$row->store())
		{
			echo $row->getError();
			exit();
			return false;
		}

		return true;
	}


}