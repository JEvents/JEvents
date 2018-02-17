<?php
/**
 * @copyright   copyright (C) 2012-2018 GWE Systems Ltd - All rights reserved
 *
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


defined('JPATH_BASE') or die;

// SEE  http://docs.joomla.org/Creating_a_Smart_Search_plug-in

jimport('joomla.application.component.helper');

use Joomla\Utilities\ArrayHelper;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

/**
 * Finder adapter for com_jevents.
 *
 * @package     JEvents.Plugin
 * @subpackage  Finder.JEvents
 * @since       2.5
 */
class plgFinderJEvents extends FinderIndexerAdapter
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
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   2.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/*
	public function onStoreCustomDetails($evdetail)
	{
		// Only use this method when editing and saving a specific repeat
		if (JRequest::getCmd("task")!="icalrepeat.save" && JRequest::getCmd("task")!="icalrepeat.apply" ){
			return true;
		}
		$detailid = $evdetail->evdet_id;

		// Reindex the item
		$this->reindex($detailid);
		return true;
	}
	 */

	public function onAfterSaveEvent (&$vevent, $dryrun) {
		if ($dryrun || !isset($vevent->detail_id) || $vevent->detail_id==0){
			return;
		}
		$detailid = $vevent->detail_id;

		// Reindex the item
		$this->reindex($detailid);
		return true;
	}

	private function findEventFromDetail($evdetail)
	{

	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
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
	 * @param   string   $context  The context of the jevents passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the jevents has just been created
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
		if ($context == 'com_categories.category' && $row->extension=="com_jevents")
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
	 * @param   string   $context  The context for the jevents passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the jevents that has changed state.
	 * @param   integer  $value    The value of the state that the jevents has been changed to.
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
	 * @param   FinderIndexerResult  $item    The item to index as an FinderIndexerResult object.
	 * @param   string               $format  The item format
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// Initialize the item parameters.
		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params = JComponentHelper::getParams('com_jevents', true);
		$item->params->merge($registry);

		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry;

		// Trigger the onContentPrepare event.
		$item->summary = FinderIndexerHelper::prepareContent($item->summary, $item->params);
		$item->body = FinderIndexerHelper::prepareContent($item->body, $item->params);

		// Build the necessary route and path information.
		$itemid= $this->params->get("target_itemid",0);
		$item->url = "index.php?option=com_jevents&task=icalevent.detail&evid=".$item->eventid."&Itemid=".$itemid;//$this->getURL($item->id, $this->extension, $this->layout);
		$item->route = "index.php?option=com_jevents&task=icalevent.detail&evid=".$item->eventid."&Itemid=".$itemid;

		$item->path = FinderIndexerHelper::getContentPath($item->route);

		$item->publish_start_date	= isset($item->modified) ?$item->modified : "2010-01-01 00:00:00" ;
		$item->publish_end_date	= "2099-12-31 00:00:00" ;

		// title is already set
		//$item->title;

		// Events should only be published if the category is published.etc. - do this later
		//$item->state;

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
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		if (JevJoomlaVersion::isCompatible("3.0.0")){
			$this->indexer->index($item);
		}
		else {
			FinderIndexer::index($item);
		}
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
	 * Method to get the SQL query used to retrieve the list of jevents items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getListQuery($query = NULL, $type = 'list')
	{
		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = $db->getQuery(true);
		$sql->select('det.evdet_id, det.summary as title, det.description  AS summary, det.description  AS body');
		$sql->select('det.state, det.modified ');
		$sql->select('rpt.rp_id, rpt.eventid ');
		$sql->select('evt.catid, evt.icsid, evt.created_by, evt.access ');
		$sql->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');
		$sql->select('u.name AS author');

		$sql->from('#__jevents_vevdetail AS det');
		$sql->leftjoin('#__jevents_repetition  AS rpt ON rpt.eventdetail_id=det.evdet_id');
		$sql->leftjoin('#__jevents_vevent AS evt ON rpt.eventid=evt.ev_id');
		$sql->leftjoin('#__categories AS c ON c.id=evt.catid');
		$sql->join('LEFT', '#__users AS u ON u.id = evt.created_by');

        if ($type === 'list' ) {
            $sql->where('evt.state = 1');
        }

		return $sql;
	}

	/**
	 * Method to get a event item to index.
	 *
	 * @param   integer  $id  The id of the content item.
	 *
	 * @return  FinderIndexerResult  A FinderIndexerResult object.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function getItem($id)
	{
		//JLog::add('FinderIndexerAdapter::getItem', JLog::INFO);

		// Get the list query and add the extra WHERE clause.
		$sql = $this->getListQuery($query = NULL, 'item');
		$sql->where('det.'. $this->db->quoteName('evdet_id') . ' = ' . (int) $id);

		// Get the item to index.
		$this->db->setQuery($sql);
		$row = $this->db->loadAssoc();
		$query = (string)$this->db->getQuery();
		// Check for a database error.
		if ($this->db->getErrorNum())
		{
			// Throw database error exception.
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Convert the item to a result object.
		$item = ArrayHelper::toObject($row, 'FinderIndexerResult');

		// Set the item type.
		$item->type_id = $this->type_id;

		// Set the item layout.
		$item->layout = $this->layout;

		return $item;
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
		$sql->join('LEFT',' #__jevents_repetition AS rpt ON rpt.eventdetail_id=a.evdet_id');
		$sql->join('LEFT',' #__jevents_vevent AS evt ON rpt.eventid=evt.ev_id');
		$sql->join('LEFT', '#__categories AS c ON c.id = evt.catid');

		return $sql;
	}

}
