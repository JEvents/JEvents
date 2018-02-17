<?php

/**
 * @copyright	Copyright (c) 2014-2018 GWE Systems Ltd. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die( ' Restricted Access ' );

jimport('joomla.plugin.plugin');

/**
 * content - JEvents Plugin
 *
 * @package		Joomla.Plugin
 * @subpakage	jevents.JEvents
 */
class plgContentJEvents extends JPlugin
{

	public
			function onContentBeforeSave($context, $data)
	{

		if (intval($data->id)==0){
			return true;
		}
		
		if ($context == "com_categories.category" && $data->extension == "com_jevents" && ( $data->published != 1 || $data->published != 0 ))
		{
			$lang = JFactory::getLanguage();
			$lang->load("com_jevents", JPATH_ADMINISTRATOR);

			 $catids = $data->id;

			// Get a db connection & new query object.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// So lets see if there are any events in the categories selected
			$params = JComponentHelper::getParams(JRequest::getCmd("option"));
            if ($data->published == "-2" || $data->published == "2") {
                if ($params->get("multicategory", 0)) {
                    $query->select($db->quoteName('map.catid'));
                    $query->from($db->quoteName('#__jevents_vevent', 'ev'));
                    $query->join('INNER', $db->quoteName('#__jevents_catmap', 'map') . ' ON (' . $db->quoteName('ev.ev_id') . ' = ' . $db->quoteName('map.evid') . ' )');
                    $query->where($db->quoteName('map.catid') . ' IN (' . $catids . ')');
                } else {
                    $query->select($db->quoteName('ev.catid'));
                    $query->from($db->quoteName('#__jevents_vevent', 'ev'));
                    $query->where($db->quoteName('ev.catid') . ' IN (' . $catids . ')');
                }

                // Reset the query using our newly populated query object.
                $db->setQuery($query);

                // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                $results = $db->loadColumn();

                $result_count = count($results);
            } else {
                $result_count = 0;
            }

			if ($result_count >= 1)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JEV_CAT_MAN_DELETE_WITH_IDS',  $result_count), 'Warning');
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JEV_CAT_DELETE_MSG_EVENTS_FIRST'), 'Warning');

				return false;
			}
			else
			{
				return true;
			}
		}

	}

	public
			function onCategoryChangeState($extension, $pks, $value)
	{
		//We need to use on categoryChangeState
		// Only run on JEvents
		if ($extension == "com_jevents" && ($value == "-2" || $value == "2"))
		{
			//$value params
			// 1  = Published
			// 0  = Unpublished
			// 2  = Archived
			// -2 = Transhed

			$lang = JFactory::getLanguage();
			$lang->load("com_jevents", JPATH_ADMINISTRATOR);

			$catids = implode(',', $pks);

			// Get a db connection & new query object.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// So lets see if there are any events in the categories selected
			$params = JComponentHelper::getParams(JRequest::getCmd("option"));
			if ($params->get("multicategory", 0))
			{
				$query->select($db->quoteName('map.catid'));
				$query->from($db->quoteName('#__jevents_vevent', 'ev'));
				$query->join('INNER', $db->quoteName('#__jevents_catmap', 'map') . ' ON (' . $db->quoteName('ev.ev_id') . ' = ' . $db->quoteName('map.evid') . ' )');
				$query->where($db->quoteName('map.catid') . ' IN (' . $catids . ')');
			}
			else {
				$query->select($db->quoteName('ev.catid'));
				$query->from($db->quoteName('#__jevents_vevent', 'ev'));
				$query->where($db->quoteName('ev.catid') . ' IN (' . $catids . ')');
			}


			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadColumn();
			//Quick way to query debug without launching netbeans.
			//JFactory::getApplication()->enqueueMessage($query, 'Error');

			$result_count = count($results);

			if ($result_count  >= 1)
			{

				// Ok so we are trying to change the published category that has events! STOP
				$u_cats = implode(',', array_unique($results, SORT_REGULAR));

				// Create a new query object.
				$query = $db->getQuery(true);

				// Select all records from the user profile table where key begins with "custom.".
				// Order it by the ordering field.
				$query->update($db->quoteName('#__categories'));
				$query->set($db->quoteName('published') . ' = 1');
				$query->where($db->quoteName('id') . ' IN (' . $u_cats . ')');

				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				$db->loadObjectList();

				//Quick way to query debug without launching netbeans.
				//JFactory::getApplication()->enqueueMessage($query, 'Error');

				JFactory::getApplication()->enqueueMessage(JText::sprintf('JEV_CAT_MAN_DELETE_WITH_IDS',  $result_count), 'Warning');
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JEV_CAT_DELETE_MSG_EVENTS_FIRST'), 'Warning');
			}
		}

	}

}
