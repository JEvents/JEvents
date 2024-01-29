<?php
/**
 * @copyright   copyright (C) 2012-JEVENTS_COPYRIGHT GWESystems Ltd - All rights reserved
 *
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


defined('JPATH_BASE') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;


// SEE  http://docs.joomla.org/Creating_a_Smart_Search_plug-in

jimport('joomla.application.component.helper');

use Joomla\Utilities\ArrayHelper;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for com_jevents.
 *
 * @package     JEvents.Plugin
 * @subpackage  Finder.JEvents
 * @since       2.5
 */
#[\AllowDynamicProperties]
class plgFinderJEvents extends Adapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $context = 'JEvents';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $extension = 'com_jevents';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $layout = 'event';

	/**
	 * The type of jevents that the adapter indexes.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $type_title = 'Event';

	/**
	 * The table name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $table = '#__jevents_vevdetail';

	/**
	 * Constructor
	 *
	 * @param   object &$subject The object to observe
	 * @param   array  $config   An array that holds the plugin configuration
	 *
	 * @since   2.5
	 */
	public function __construct(&$subject, $config)
	{

		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onPublishEvent($ids, $newstate)
	{

		foreach ($ids as $event_id)
		{
			// Get a db connection.
			$db = Factory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName('eventdetail_id'));
			$query->from($db->quoteName('#__jevents_repetition'));
			$query->where($db->quoteName('eventid') . ' = ' . $event_id);

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$eventdetail_id = (int) $db->loadResult('eventdetail_id');

			// Reindex the item
			if (!empty($eventdetail_id))
			{
				$this->reindex($eventdetail_id);
			}
		}

		return true;
	}


	public function onFinderResult (& $result, & $query)
	{
	    // make sure it is an event and if it should be hidden etc.
		if ($result->getElement('rp_id') > 0)
		{
			// Just in case we don't have JEvents plugins registered yet
			PluginHelper::importPlugin("jevents");

			$output = Factory::getApplication()->triggerEvent('onJEventsFinderResult', array(& $result, & $query));

		}
        $x = 1;
	}


	public function onAfterSaveEvent(& $vevent, $dryrun)
	{

		if ($dryrun || !isset($vevent->detail_id) || $vevent->detail_id == 0)
		{
			return;
		}
		$detailid = $vevent->detail_id;

		// Reindex the item
		$this->reindex($detailid);

		return true;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string $context The context of the action being performed.
	 * @param   Table $table   A Table object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{

		if ($context == 'com_jevents.event')
		{
			$id = $table->id;
		}
		elseif ($context == 'com_finder.index')
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}

		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string  $context The context of the jevents passed to the plugin.
	 * @param   Table  $row     A Table object
	 * @param   boolean $isNew   If the jevents has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew)
	{

		// We only want to handle events here
		if ($context == 'com_jevents.event' || $context == 'com_jevents.form')
		{
			// Check if the access levels are different
			if (!$isNew && $this->old_access != $row->access)
			{
				// Process the change.
				$this->itemAccessChange($row);
			}

			// Reindex the item
			$this->reindex($row->id);
		}

		// Check for access changes in the category
		if ($context == 'com_categories.category' && $row->extension == "com_jevents")
		{
			// Check if the access levels are different
			if (!$isNew && $this->old_cataccess != $row->access)
			{
				// TODO sort out category access change finder updates later
				// $this->categoryAccessChange($row);
			}
		}

		return true;
	}

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string  $context The context for the jevents passed to the plugin.
	 * @param   array   $pks     A list of primary key ids of the jevents that has changed state.
	 * @param   integer $value   The value of the state that the jevents has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function onFinderChangeState($context, $pks, $value)
	{

		// We only want to handle events here
		if ($context == 'com_jevents.event' || $context == 'com_jevents.form')
		{
			$this->itemStateChange($pks, $value);
		}
		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			$this->pluginDisable($pks);
		}
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   Result $item   The item to index as an Result object.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function index(Result $item)
	{

		// Check if the extension is enabled
		if (ComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// Initialize the item parameters.
		$registry = new JevRegistry;
		$registry->loadString(isset($item->params) ? $item->params : "");
		$item->params = ComponentHelper::getParams('com_jevents', true);
		$item->params->merge($registry);

		$registry = new JevRegistry;
		$registry->loadString(isset($item->metadata) ? $item->metadata : "");
		$item->metadata = $registry;

		// Trigger the onContentPrepare event.
		$item->summary = Helper::prepareContent($item->summary, $item->params);
		$item->body    = Helper::prepareContent($item->body, $item->params);

		// Build the necessary route and path information.
		$itemid      = $this->params->get("target_itemid", 0);
		if ($itemid == 0)
		{
			$itemid = $item->params->get("permatarget", 0);
		}
		if (false && (int) $item->getElement('rp_id'))
		{
			$rpid = (int) $item->getElement('rp_id');
			$item->url   = "index.php?option=com_jevents&task=icalrepeat.detail&evid=" . $rpid . "&Itemid=" . $itemid;//$this->getURL($item->id, $this->extension, $this->layout);
			$item->route = "index.php?option=com_jevents&task=icalrepeat.detail&evid=" . $rpid . "&Itemid=" . $itemid;
		}
		else
		{
			$item->url   = "index.php?option=com_jevents&task=icalevent.detail&evid=" . $item->eventid . "&Itemid=" . $itemid;//$this->getURL($item->id, $this->extension, $this->layout);
			$item->route = "index.php?option=com_jevents&task=icalevent.detail&evid=" . $item->eventid . "&Itemid=" . $itemid;
		}

		include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

		$item->path = $item->route;
		// get the data and query models
		$dataModel  = new JEventsDataModel();
		$queryModel = new JEventsDBModel($dataModel);

		// get the repeat (allowing for it to be unpublished)
		$theevent = array($queryModel->listEventsById($item->rp_id));

		if (isset($theevent[0]) && $theevent[0]) {
			PluginHelper::importPlugin('jevents');
			Factory::getApplication()->triggerEvent('onJevFinderIndexing', array(&$theevent));
			try
			{
				$item->title       = $theevent[0]->title();
				$item->description = $theevent[0]->content();
				$item->setElement('body', $theevent[0]->content());
				$item->setElement('summary', $theevent[0]->content());
			}
			catch (Exception $e)
			{

			}

			$db = Factory::getDbo();
			$sql = $db->getQuery(true);
			$sql->select("*")
				->from("#__jev_files_combined")
				->where("evdet_id = "  . (int) $theevent[0]->_evdet_id);
			try
			{
                $this->db->setQuery($sql);
				$images = $db->loadObject();
				if ($images && isset($images->imagename1) &&  !empty($images->imagename1))
				{
					$item->imageUrl = $images->imagename1;
					$item->imageAlt = $images->imagetitle1 ?? '';
				}
			}
			catch (Exception $e)
			{
				$images = false;
			}

		}

		$theevent = count($theevent) === 1 ? $theevent[0] : $theevent;

		JLoader::register('JevDate', JPATH_SITE . "/components/com_jevents/libraries/jevdate.php");

		if ($this->params->get("past", -1) != -1 && $theevent)
		{
			$past                     = str_replace(array('-', '+' , ''), '', $this->params->get("past", -1));
			$date                     = new Date($theevent->startDate() . " - $past days");
			$item->publish_start_date = $date->toSql();
		}
		else
		{
			$item->publish_start_date	= isset($item->modified) ?$item->modified : "2010-01-01 00:00:00" ;
		}
		if ($this->params->get("future", -1) != -1  && $theevent)
		{
			$future                 = str_replace(array('-', '+' , ''), '', $this->params->get("future", -1));
			$date                   = new Date($theevent->endDate() . " + $future days");
			$item->publish_end_date = $date->toSql();
		}
		else
		{
			$item->publish_end_date	= "2099-12-31 00:00:00" ;
		}

		// If the timelimit plugin has values set let's overrride the previous values.
		if (isset($theevent->timelimits) && !empty($theevent->timelimits)) {
			// Must change to correct timezone - GMT in finder tables
			$compparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$jtz = $compparams->get("icaltimezonelive", "");

			if ($theevent->timelimits->startlimit !== '') {
				//$date = new JevDate($theevent->timelimits->startlimit);
				//$sql = $date->toMySQL(true);

				$date = new Date($theevent->timelimits->startlimit, (isset($theevent->_tzid) && !empty($theevent->_tzid)) ? $theevent->_tzid : $jtz);
				$gmtsql = $date->format('Y-m-d H:i:s');

				$item->publish_start_date   = $gmtsql;
			}
			if ($theevent->timelimits->endlimit) {
				//$date = new JevDate($theevent->timelimits->endlimit, $jtz);
				//$sql = $date->toMySQL();

				$date = new Date($theevent->timelimits->endlimit, (isset($theevent->_tzid) && !empty($theevent->_tzid)) ? $theevent->_tzid : $jtz);
				$gmtsql = $date->format('Y-m-d H:i:s');

				$item->publish_end_date     = $gmtsql;
			}
		}


		// title is already set
		//$item->title;

		// Events should only be published if the category is published.etc. - do this later
		$item->state     = $this->translateState($item->state, $item->cat_state);
		$item->published = $item->state;

		if ($item->state !== 1) {
			// Ok Finder is weird, although we set published and state = 0 it still publishes it.
			// So we will set the publish end date the same as the start so it's not found.
			$item->publish_end_date = $item->publish_start_date;
		}

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Event');

		// Add the creator taxonomy data.
		if (!empty($item->creator) || !empty($item->created_by_alias))
		{
			$item->addTaxonomy('Creator', !empty($item->created_by_alias) ? $item->created_by_alias : $item->creator);
		}

		// Add the category taxonomy data. - can we do multiple categories?
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		//$item->addTaxonomy('Language', $item->language);

		// Get jevents extras.
		Helper::getContentExtras($item);

		// Index the item.
		$this->indexer->index($item);

	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	protected function setup()
	{

		// Load dependent classes.
		include_once JPATH_SITE . '/components/com_jevents/jevents.defines.php';

		//include_once JPATH_SITE . '/components/com_jevents/helpers/route.php';

		return true;
	}

	/**
	 * Method to get a event item to index.
	 *
	 * @param   integer $id The id of the content item.
	 *
	 * @return  Result  A Result object.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function getItem($id)
	{

		//Log::add('Adapter::getItem', Log::INFO);

		// Get the list query and add the extra WHERE clause.
		$sql = $this->getListQuery($query = null, 'item');
		$sql->where('det.' . $this->db->quoteName('evdet_id') . ' = ' . (int) $id);

		// Get the item to index.
		$this->db->setQuery($sql);

		try
		{
			$row   = $this->db->loadAssoc();
		} catch (Exception $e) {
			throw new Exception($e, 500);

		}
/*
		echo (string) $sql;
		echo "<br>";
		var_dump($row);
		exit();
*/

		// Convert the item to a result object.
		$item = ArrayHelper::toObject($row, 'FinderIndexerResult');

		// Set the item type.
		$item->type_id = $this->type_id;

		// Set the item layout.
		$item->layout = $this->layout;

		return $item;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of jevents items.
	 *
	 * @param   mixed $sql A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function  getListQuery($query = null, $type = 'list')
	{

		$db = Factory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = $db->getQuery(true);
		$sql->select('det.evdet_id, det.summary as title, det.description  AS summary, det.description  AS body');
		$sql->select('det.modified ');
		$sql->select('rpt.rp_id, rpt.eventid ');
		$sql->select('evt.catid, evt.icsid, evt.created_by, evt.access ');
		$sql->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');
		$sql->select('u.name AS author');
		$sql->select('evt.state AS state');

		$sql->from('#__jevents_vevdetail AS det');
		$sql->leftjoin('#__jevents_repetition  AS rpt ON rpt.eventdetail_id=det.evdet_id');
		$sql->leftjoin('#__jevents_vevent AS evt ON rpt.eventid=evt.ev_id');
		$sql->leftjoin('#__categories AS c ON c.id=evt.catid');
		$sql->join('LEFT', '#__users AS u ON u.id = evt.created_by');

		if ($type === 'list')
		{
			$sql->where('evt.state = 1');
		}

		return $sql;
	}

	/**
	 * Method to get a SQL query to load the published and access states for
	 * an article and category.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getStateQuery()
	{

		$sql = $this->db->getQuery(true);
		// Item ID
		$sql->select('a.evdet_id as id');
		// Item and category published state
		$sql->select('a.' . $this->state_field . ' AS state, c.published AS cat_state');
		// Item and category access levels
		$sql->select('evt.access, c.access AS cat_access');
		$sql->from($this->table . ' AS a');
		$sql->join('LEFT', ' #__jevents_repetition AS rpt ON rpt.eventdetail_id=a.evdet_id');
		$sql->join('LEFT', ' #__jevents_vevent AS evt ON rpt.eventid=evt.ev_id');
		$sql->join('LEFT', '#__categories AS c ON c.id = evt.catid');

		return $sql;
	}

	private function findEventFromDetail($evdetail)
	{

	}


	/**
	 * Method to remove outdated index entries
	 *
	 * @return  integer
	 *
	 * @since   4.2.0
	 */
	public function onFinderGarbageCollection()
	{

		$db = $this->db;
		$type_id = $this->getTypeId();

		$query = $db->getQuery(true);
		$subquery = $db->getQuery(true);
		$subquery->select('CONCAT(' . $db->quote($this->getUrl('', $this->extension, $this->layout)) . ', evdet_id)')
			->from($db->quoteName($this->table));
		$query->select($db->quoteName('l.link_id'))
			->from($db->quoteName('#__finder_links', 'l'))
			->where($db->quoteName('l.type_id') . ' = ' . $type_id)
			->where($db->quoteName('l.url') . ' LIKE ' . $db->quote($this->getUrl('%', $this->extension, $this->layout)))
			->where($db->quoteName('l.url') . ' NOT IN (' . $subquery . ')');
		$db->setQuery($query);
		$items = $db->loadColumn();

		foreach ($items as $item) {
			$this->indexer->remove($item);
		}

		return count($items);
	}

}
