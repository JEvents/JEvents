<?php
/**
 * @copyright   copyright (C) 2012-2016 GWE Systems Ltd - All rights reserved
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
	//protected $table = '#__jevents_vevdetail';
	protected $table = '#__jevents_vevent';

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
			// assign event id as id since the adapter class uses ->id
			$row->id    = $row->ev_id;

			// Check if the access levels are different
			if (!$isNew && $this->old_access != $row->access) {
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
		$item->summary  = FinderIndexerHelper::prepareContent($item->summary, $item->params);
		$item->body     = FinderIndexerHelper::prepareContent($item->body, $item->params);

		// get the menu Itemid
		$Itemid = $this->params->get("target_itemid",0);

		// build the necessary route and path information
		$item->url      = 'index.php?option=com_jevents&task=icalevent.detail&evid=' . $item->ev_id;
		$item->route    = $item->url . '&Itemid=' . $Itemid . '&title=' . $item->title;
		$item->path     = FinderIndexerHelper::getContentPath($item->route);

		// Translate the state. Event should only be published if the category is published.
		$item->state = $this->translateState($item->state, $item->cat_state);

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Event');

		// Add the creator taxonomy data.
		if (!empty($item->creator) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Creator', !empty($item->created_by_alias) ? $item->created_by_alias : $item->creator);
		}

		// Add the category taxonomy data. - can we do multiple categories?
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		//$item->addTaxonomy('Language', $item->language);

		// Get jevents extras.
		FinderIndexerHelper::getContentExtras($item);

		// index the item
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

		return true;
	}

	/**
	 * Overload method to get the SQL query used to retrieve the list of jevents items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getListQuery($query = NULL)
	{
		$db = $this->db;

		$query = $query instanceof JDatabaseQuery ? $query : $db->getQuery(true)
			->select('a.ev_id, a.catid, a.state, a.created_by, a. modified_by, a.access, a.rawdata, c.access AS cat_access')
			->select('d.evdet_id, d.modified, d.summary AS title, d.description AS summary, d.description AS body')
			->select('d.dtstart, d.dtend')
			->select('u.name AS author')
			->from($this->table . ' AS a')
			->join('LEFT', $db->quoteName('#__jevents_vevdetail', 'd') . ' ON ' . $db->quoteName('d.evdet_id') . ' = ' . $db->quoteName('a.detail_id'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

		return $query;
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
		$sql = $this->getListQuery();
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

		// query the repeat table to get start and end of repeats
		$sql = $this->getRepeats();
		$sql->where($this->db->quoteName('r.eventid') . ' = ' . $this->db->quote((int) $id));
		$this->db->setQuery($sql);
		$repeats = $this->db->loadAssoc();

		// Convert the item to a result object.
		$item = ArrayHelper::toObject((array) $row, 'FinderIndexerResult');

		//set the publish start date and publish end date using start/end repeat
		$item->publish_start_date   = $row['modified'];
		$item->publish_end_date     = $repeats['end_repeat'];
		$item->start_date           = $item->publish_start_date;

			// Set the item type.
		$item->type_id = $this->type_id;

		// Set the item layout.
		$item->layout = $this->layout;

		// convert date
		$item->publish_up = JevDate::strftime('%Y-%m-%d %H:%M:%S',$item->publish_up);

		return $item;
	}


	/**
	 * Overload method to get a SQL query to load the published and access states for
	 * an article and category.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getStateQuery()
	{
		$db     = $this->db;
		$query  = $db->getQuery(true);

		// Item ID
		$query->select('a.ev_id');

		// Item and category published state
		$query->select('a.state AS ' . $this->state_field, 'c.published AS cat_state');

		// Item and category access levels
		$query->select('a.access, c.access AS cat_access')
			->from($db->quoteName($this->table, 'a'))
			->join('LEFT', $db->quoteName('#__jevents_vevdetail', 'd') . ' ON ' . $db->quoteName('d.evdet_id') . ' = ' . $db->quote('a.detail_id'))
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		return $query;
	}

	/**
	 * Overload method to update index data on access level changes
	 *
	 * @param   JTable  $row  A JTable object
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function itemAccessChange($row)
	{
		$query = clone $this->getStateQuery();
		$query->where('a.ev_id = ' . (int) $row->id);

		// Get the access level.
		$this->db->setQuery($query);
		$item = $this->db->loadObject();

		// Set the access level.
		$temp = max($row->access, $item->cat_access);

		// Update the item.
		$this->change((int) $row->id, 'access', $temp);
	}

	/**
	 * Overload method to update index data on published state changes
	 *
	 * @param   array    $pks    A list of primary key ids of the content that has changed state.
	 * @param   integer  $value  The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function itemStateChange($pks, $value)
	{
		/*
		 * The item's published state is tied to the category
		 * published state so we need to look up all published states
		 * before we change anything.
		 */
		foreach ($pks as $pk)
		{
			$query = clone $this->getStateQuery();
			$query->where('a.ev_id = ' . (int) $pk);

			// Get the published states.
			$this->db->setQuery($query);
			$item = $this->db->loadObject();

			// Translate the state.
			$temp = $this->translateState($value, $item->cat_state);

			// Update the item.
			$this->change($pk, 'state', $temp);

			// Reindex the item
			$this->reindex($pk);
		}
	}

	/**
	 * Overload method to remove an item from the index.
	 *
	 * @param   string  $id  The ID of the item to remove.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function remove($id)
	{
		// set the item URL overriding $this->getURL() since JEvents is not using Joomla standard view & layout
		$url    = 'index.php?option=' . $this->extension . '&task=icalevent.detail&evid=' . $id;

		// Get the link ids for the content items.
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('link_id'))
			->from($this->db->quoteName('#__finder_links'))
			->where($this->db->quoteName('url') . ' LIKE ' . $this->db->quote('%' . $url . '%'));
		$this->db->setQuery($query);
		$items = $this->db->loadColumn();

		// Check the items.
		if (empty($items)) {
			return true;
		}

		// Remove the items.
		foreach ($items as $item) {
			$this->indexer->remove($item);
		}

		return true;
	}

	/**
	 * Custom method to get the event repeat data.
	 * This method will only get the first row Start Repeat Date and the Last Row End Repeat Date.
	 * The dates will be use to populate the publish_start_date, publish_end_date, start_date, end_date.
	 *
	 * @param   void
	 *
	 * @since   1.0
	 * @return  Query string.
	 */
	protected function getRepeats()
	{
		$db     = $this->db;
		$query  = $db->getQuery(true);

		$query->select('MIN(r.startrepeat) AS start_repeat, MAX(r.endrepeat) AS end_repeat')
			->from($db->quoteName('#__jevents_repetition', 'r'))
			->group($db->quoteName('r.eventid'));

		return $query;
	}

}
