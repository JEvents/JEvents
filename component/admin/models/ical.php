<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');


use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;

class JeventsModelical extends ListModel
{
	public $queryModel;
	private $total = 0;
	private $_debug = false;

	/**
	 * Constructor
	 *
	 * @param array $config Configuration array for model. Optional.
	 *
	 * @since 1.5
	 */
	function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$db = Factory::getDbo();

			$config['filter_fields'] = array(
				'id', 'a.' . $db->quoteName('id'), 'a.id',
				'title', 'a.' . $db->quoteName('title'),'a.title',
				'catid', 'a.' . $db->quoteName('catid'), 'a.catid',
				'state', 'a.' . $db->quoteName('state'), 'a.state',
				'access', 'a.' . $db->quoteName('access'), 'a.access'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		// Set the model state set flag to true.  Needed to stop recursion
		$this->__state_set = true;

		// Special sanitising of values
		if ($this->getState('filter.icsFile', '£$%$£') !== '£$%$£')
		{
			$this->setState('filter.icsFile', (int) $this->getState('filter.icsFile', '£$%$£'));
		}
	}
		/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		// get the state data into the model
		$this->getStoreId();

		$app    = Factory::getApplication();
		$limit      = intval($this->getState('list.limit', $app->get('list_limit', 10)));
		$limitstart = intval($this->getState('list.start',  0));

		$option = JEV_COM_COMPONENT;
		$db     = Factory::getDbo();

		$app    = Factory::getApplication();
		$input  = $app->input;

		$state = intval($this->getState('filter.state', 3));
		$catid    = intval($this->getState('filter.catid', 0));

		$search     = $this->getState('filter.search', '');
		$search     = $db->escape(trim(strtolower($search)));

		$where      = array();

		// Trap cancelled edit and reset category ID.
		$icsid = intval($input->getInt('icsid', -1));
		if ($icsid > -1)
		{
			$catid = 0;
		}
		if ($search)
		{
			$where[] = "LOWER(icsf.label) LIKE '%$search%'";
		}
		// Weird bug? catid is set somewhere as 1, can never be 1 as Joomla! has a category by default.
		if ($catid > 1)
		{
			$where[] = "catid = $catid";
		}
		// get the total number of records
		$query = "SELECT count(*)"
			. "\n FROM #__jevents_icsfile AS icsf"
			. (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
		$db->setQuery($query);
		$total = 0;

		try
		{
			$total = $db->loadResult();
		} catch (Exception $e) {
			echo $e;
		}

		$this->total = $total;

		if ($limitstart > $total)
		{
			$limitstart = 0;
		}


		$query = "SELECT icsf.*, a.title as _groupname"
			. "\n FROM #__jevents_icsfile as icsf "
			. "\n LEFT JOIN #__viewlevels AS a ON a.id = icsf.access"
			. (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');

		$query .= "\n ORDER BY icsf.isdefault DESC, icsf.label ASC";
		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$catData = JEV_CommonFunctions::getCategoryData();

		for ($s = 0; $s < count($rows); $s++)
		{
			$row =& $rows[$s];
			if (array_key_exists($row->catid, $catData))
			{
				$row->category = $catData[$row->catid]->name;
			}
			else
			{
				$row->category = "?";
			}
		}

		$cfg          = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		if ($this->_debug)
		{
			echo '[DEBUG]<br />';
			echo 'query:';
			echo '<pre>';
			echo $query;
			echo '-----------<br />';
			echo 'option "' . $option . '"<br />';
			echo '</pre>';
			//die( 'userbreak - mic ' );
		}

		return $rows;

	}

	public function getTotal()
	{
		return $this->total;
	}


}