<?php
/**
 * @copyright	Copyright (c) 2014 jevents. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * content - JEvents Plugin
 *
 * @package		Joomla.Plugin
 * @subpakage	jevents.JEvents
 */
class plgContentJEvents extends JPlugin {
        public function onContentBeforeSave($context, $data) {
                if ($context == "com_categories.category" && $data->extension == "com_jevents" && $data->published != 1 || $context == "com_categories.category" && $data->extension == "com_jevents" && $data->published != 0) {
                        // Get a db connection & new query object.
                        $db = JFactory::getDbo();
                        $query = $db->getQuery(true);

                       // So lets see if there are any events in the categories selected
                        $query->select($db->quoteName('map.catid'));
                        $query->from($db->quoteName('#__jevents_vevent', 'ev'));                               
                        $query->join('INNER', $db->quoteName('#__jevents_catmap', 'map') . ' ON (' . $db->quoteName('ev.ev_id') . ' = '  . $db->quoteName('map.evid') . ' )');
                        $query->where($db->quoteName('map.catid') . ' = ' . $data->id . '');

                        // Reset the query using our newly populated query object.
                        $db->setQuery($query);

                        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                        $results = $db->loadResultArray();
                        
                        $result_count = count($results);
                        
                        if ($result_count >= 1) {
                             JFactory::getApplication()->enqueueMessage(JText::_('JEV_CAT_DELETE_MSG_CONTAINS'), 'Error');
                             JFactory::getApplication()->enqueueMessage(JText::_('JEV_CAT_DELETE_MSG_EVENTS_FIRST'), 'Error');   
                             return false;   
                        } else {
                                return true;
                        }
                        
                  
                }
        }
        public function onCategoryChangeState($extension, $pks, $value) {
                //We need to use on categoryChangeState
                // Only run on JEvents
                if ($extension == "com_jevents" && $value != 1) {
                        //$value params
                        // 1  = Published
                        // 0  = Unpublished
                        // 2  = Archived
                        // -2 = Transhed
                        
                        $catids = implode(',', $pks);
                        
                        // Get a db connection & new query object.
                        $db = JFactory::getDbo();
                        $query = $db->getQuery(true);

                       // So lets see if there are any events in the categories selected
                        $query->select($db->quoteName('map.catid'));
                        $query->from($db->quoteName('#__jevents_vevent', 'ev'));                               
                        $query->join('INNER', $db->quoteName('#__jevents_catmap', 'map') . ' ON (' . $db->quoteName('ev.ev_id') . ' = '  . $db->quoteName('map.evid') . ' )');
                        $query->where($db->quoteName('map.catid') . ' IN (' . $catids . ')');
                        
                        
                        // Reset the query using our newly populated query object.
                        $db->setQuery($query);

                        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                        $results = $db->loadResultArray();
                        //Quick way to query debug without launching netbeans.
                        //JFactory::getApplication()->enqueueMessage($query, 'Error');
                        
                        if (count($results) >= 1) {
                                
                                // Ok so we are trying to change the published category that has events! STOP  
                                $u_cats = implode(',', array_unique($results, SORT_REGULAR));  
                                
                                // Create a new query object.
                                $query = $db->getQuery(true);

                                // Select all records from the user profile table where key begins with "custom.".
                                // Order it by the ordering field.
                                $query->update($db->quoteName('#__categories'));
                                $query->set($db->quoteName('published') . ' = 1' );
                                $query->where($db->quoteName('id') . ' IN (' . $u_cats . ')');
                                
                                // Reset the query using our newly populated query object.
                                $db->setQuery($query);
                                $db->loadObjectList();
                                
                                //Quick way to query debug without launching netbeans.
                                //JFactory::getApplication()->enqueueMessage($query, 'Error');
                                
                                JFactory::getApplication()->enqueueMessage(JText::_('JEV_CAT_MAN_DELETE_WITH_IDS') . $u_cats . JText::_('JEV_CAT_MAN_DELETE_WITH_IDS_ENABLED'), 'Warning');
                                JFactory::getApplication()->enqueueMessage(JText::_('JEV_CAT_DELETE_MSG_EVENTS_FIRST'), 'Warning');

                        }
                        
                        
                }
                
        }
	
}