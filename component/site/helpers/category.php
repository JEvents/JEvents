<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: category.php 1142 2010-09-08 10:10:52Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');


class JEventsCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__jevents_vevent';
		$options['field'] = 'catid';
		$options['key'] = 'ev_id';
		$options['extension'] = 'com_jevents';
		parent::__construct($options);
	}
	
	/**
	 * Load method - our version MUST set the access level correctly for iCal exports!
	 *
	 * @param   integer  $id  Id of category to load
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function _load($id)
	{
		$registry = JRegistry::getInstance("jevents");
		// need both paths for Joomla 2.5 and 3.0
		$puser = $registry->get("jevents.icaluser" , $registry->get("icaluser" , false));

		if (!$puser){

			$this->_options['currentlang'] = 0;
			return parent::_load($id);
		}
		
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		
		// overload permissions for iCal Export
		$user = $puser;
		
		$extension = $this->_extension;
		// Record that has this $id has been checked
		$this->_checkedCategories[$id] = true;

		$query = $db->getQuery(true);

		// Right join with c for category
		$query->select('c.*');
		$case_when = ' CASE WHEN ';
		$case_when .= $query->charLength('c.alias');
		$case_when .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= $c_id . ' END as slug';
		$query->select($case_when);

		$query->from('#__categories as c');
		$query->where('(c.extension=' . $db->Quote($extension) . ' OR c.extension=' . $db->Quote('system') . ')');

		if ($this->_options['access'])
		{
			$query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		}

		if ($this->_options['published'] == 1)
		{
			$query->where('c.published = 1');
		}

		$query->order('c.lft');

		// s for selected id
		if ($id != 'root')
		{
			// Get the selected category
			$query->where('s.id=' . (int) $id);
			if ($app->isSite() && $app->getLanguageFilter())
			{
				$query->leftJoin('#__categories AS s ON (s.lft < c.lft AND s.rgt > c.rgt AND c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')) OR (s.lft >= c.lft AND s.rgt <= c.rgt)');
			}
			else
			{
				$query->leftJoin('#__categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)');
			}
		}
		else
		{
			if ($app->isSite() && $app->getLanguageFilter())
			{
				$query->where('c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
			}
		}

		$subQuery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ' .
			'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension = ' . $db->quote($extension) .
			' AND parent.published != 1 GROUP BY cat.id) ';
		$query->leftJoin($subQuery . 'AS badcats ON badcats.id = c.id');
		$query->where('badcats.id is null');

		// i for item
		if (isset($this->_options['countItems']) && $this->_options['countItems'] == 1)
		{
			if ($this->_options['published'] == 1)
			{
				$query->leftJoin(
					$db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id AND i.' . $this->_statefield . ' = 1'
				);
			}
			else
			{
				$query->leftJoin($db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id');
			}

			$query->select('COUNT(i.' . $db->quoteName($this->_key) . ') AS numitems');
		}

		// Group by
		$query->group('c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
 			c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
		 	c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
 			c.path, c.published, c.rgt, c.title, c.modified_user_id');

		// Get the results
		$db->setQuery($query);
		$results = $db->loadObjectList('id');
		$childrenLoaded = false;

		if (count($results))
		{
			// Foreach categories
			foreach ($results as $result)
			{
				// Deal with root category
				if ($result->id == 1)
				{
					$result->id = 'root';
				}

				// Deal with parent_id
				if ($result->parent_id == 1)
				{
					$result->parent_id = 'root';
				}

				// Create the node
				if (!isset($this->_nodes[$result->id]))
				{
					// Create the JCategoryNode and add to _nodes
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);

					// If this is not root and if the current node's parent is in the list or the current node parent is 0
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1))
					{
						// Compute relationship between node and its parent - set the parent in the _nodes field
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
				elseif ($result->id == $id || $childrenLoaded)
				{
					// Create the JCategoryNode
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);

					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id))
					{
						// Compute relationship between node and its parent
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					if (!isset($this->_nodes[$result->parent_id]))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}

				}
			}
		}
		else
		{
			$this->_nodes[$id] = null;
		}
	}

	
}