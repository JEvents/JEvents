<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminCategoriesViewCategories extends JEventsAbstractView 
{
	function overview($tpl = null)
	{
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'CATEGORIES' ));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'CATEGORIES' ), 'jevents' );
	
		JToolBarHelper::publishList('categories.publish');
		JToolBarHelper::unpublishList('categories.unpublish');
		JToolBarHelper::addNew('categories.add');
		JToolBarHelper::editList('categories.edit');
		JToolBarHelper::deleteList("delete category",'categories.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', 'JEV_ADMIN_CPANEL', false );
		//JToolBarHelper::help( 'screen.categories', true);

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option='.JEV_COM_COMPONENT, true);
						
		JHTML::_('behavior.tooltip');

		// Preprocess the list of items to find ordering divisions.
		// RSH 9/28/10 Added check for empty list - if no items were created.
		if (count($this->items) > 0) {
			foreach ($this->items as &$item) {
				$this->ordering[$item->parent_id][] = $item->id;
			}
		}
	}	


	function edit($tpl = null)
	{
		JRequest::setVar( 'hidemainmenu', 1 );
		
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JHTML::stylesheet( 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		else JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'CATEGORIES' ));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'CATEGORIES' ), 'jevents' );
	
		JToolBarHelper::save('categories.save');
		JToolBarHelper::cancel('categories.list');
		//JToolBarHelper::help( 'screen.categories.edit', true);

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option='.JEV_COM_COMPONENT, true);
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		
		JHTML::_('behavior.tooltip');
	}	

}