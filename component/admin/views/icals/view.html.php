<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1676 2010-01-20 02:50:34Z geraint $
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
class AdminIcalsViewIcals extends JEventsAbstractView
{
	function overview($tpl = null)
	{

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('ICals'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'ICals' ), 'jevents' );

		JToolBarHelper::publishList('icals.publish');
		JToolBarHelper::unpublishList('icals.unpublish');
		JToolBarHelper::addNew('icals.edit');
		JToolBarHelper::editList('icals.edit');
		JToolBarHelper::deleteList('Delete Ical and all associated events and repeats?','icals.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', 'JEV_ADMIN_CPANEL', false );
		//JToolBarHelper::help( 'screen.ical', true);

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.JEV_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');
	}

	function edit($tpl = null)
	{

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		JHTML::script('editical.js?v=1.5.4','administrator/components/'.JEV_COM_COMPONENT.'/assets/js/');

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Edit ICS'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Edit ICS' ), 'jevents' );

		//JToolBarHelper::save('icals.save');
		$bar = & JToolBar::getInstance('toolbar');
		if ($this->editItem && isset($this->editItem->ics_id) && $this->editItem->ics_id >0){
			JToolBarHelper::save('icals.savedetails');
		}
		JToolBarHelper::cancel('icals.list');
		//JToolBarHelper::help( 'screen.icals.edit', true);

		$this->_hideSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		if ($params->getValue("authorisedonly",0)){
			// get authorised users
			$sql = "SELECT u.* FROM #__jev_users as jev LEFT JOIN #__users as u on u.id=jev.user_id where jev.published=1 and jev.cancreate=1";
			$db=& JFactory::getDBO();
			$db->setQuery( $sql );
			$users = $db->loadObjectList();
		}
		else {
			$minaccess = $params->getValue("jevcreator_level",19);

			// get users of required level
			$sql = "SELECT * FROM #__users where gid>=".$minaccess;
			$db=& JFactory::getDBO();
			$db->setQuery( $sql );
			$users = $db->loadObjectList();
		}
		$userOptions = array();
		foreach( $users as $user )
		{
			$userOptions[] = JHTML::_('select.option', $user->id, $user->name. " ($user->username)" );
		}
		$jevuser	= &JFactory::getUser();
		if ($this->editItem && isset($this->editItem->ics_id) && $this->editItem->ics_id >0 && $this->editItem->created_by>0){
			$created_by = $this->editItem->created_by;
		}
		else {
			$created_by = $jevuser->id;
		}
		$userlist = JHTML::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1" ', 'value', 'text', $created_by);
		$this->assignRef("users",$userlist);


		JHTML::_('behavior.tooltip');
	}

}