<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
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
class AdminIcaleventViewIcalevent extends JEventsAbstractView
{
	function overview($tpl = null)
	{

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('ICal Events'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'ICal Events' ), 'jevents' );

		JToolBarHelper::custom('icalevent.csvimport','upload.png','upload.png','JEV_ADMIN_CSVIMPORT',false);
		JToolBarHelper::publishList('icalevent.publish');
		JToolBarHelper::unpublishList('icalevent.unpublish');
		JToolBarHelper::addNew('icalevent.edit');
		JToolBarHelper::editList('icalevent.edit');
		JToolBarHelper::custom('icalevent.editcopy','copy.png','copy.png','JEV_ADMIN_COPYEDIT');
		JToolBarHelper::deleteList('Delete Event and all repeats?','icalevent.delete');
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
		$document =& JFactory::getDocument();
		include(JEV_LIBS."editStrings.php");
		$document->addScriptDeclaration($editStrings);

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		JHTML::script('editical.js?v=1.5.2','administrator/components/'.JEV_COM_COMPONENT.'/assets/js/');

		$document->setTitle(JText::_('Edit ICal Event'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Edit ICal Event' ), 'jevents' );

		$bar = & JToolBar::getInstance('toolbar');
		if ($this->id>0){
			if ($this->editCopy){
				$this->toolbarConfirmButton("icalevent.save",JText::_("save copy warning"),'save','save','Save',false);
				$this->toolbarConfirmButton("icalevent.apply",JText::_("save copy warning"),'apply','apply','Apply',false);
			}
			else {
				$this->toolbarConfirmButton("icalevent.save",JText::_("save icalevent warning"),'save','save','Save',false);
				$this->toolbarConfirmButton("icalevent.apply",JText::_("save icalevent warning"),'apply','apply','Apply',false);
			}
		}
		else {
			JToolBarHelper::save('icalevent.save');
			JToolBarHelper::apply('icalevent.apply');
			//$bar->appendButton( 'Apply',  'apply', "Apply",'icalevent.apply', false, false );
		}

		JToolBarHelper::cancel('icalevent.list');
		//JToolBarHelper::help( 'screen.icalevent.edit', true);

		$this->_hideSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');

		$this->setCreatorLookup();
	}

	function csvimport($tpl = null)
	{
		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('CSV Import'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'CSV Import' ), 'jevents' );

		JToolBarHelper::cancel('icalevent.list');

		$this->_hideSubmenu();

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.JEV_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');
	}

	protected function setCreatorLookup(){
		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser	= JEVHelper::getAuthorisedUser();
		$user = JFactory::getUser();

		// Get an ACL object
		$acl =& JFactory::getACL();
		$grp = $acl->getAroGroup($user->get('id'));
		// if no valid group (e.g. anon user) then skip this.
		if (!$grp) return;

		$access = $acl->is_group_child_of($grp->name, 'Public Backend');

		if (($jevuser && $jevuser->candeleteall) || $access){
			$params =& JComponentHelper::getParams( JEV_COM_COMPONENT );
			$minaccess = $params->getValue("jevcreator_level",19);
			$sql = "SELECT * FROM #__users where gid>=".$minaccess;
			$sql .= " ORDER BY name ASC";
			$db = JFactory::getDBO();
			$db->setQuery( $sql );
			$users = $db->loadObjectList();

			$userOptions[] = JHTML::_('select.option', '-1','Select User' );
			foreach( $users as $user )
			{
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name );
			}
			$creator = $this->row->created_by()>0?$this->row->created_by():(isset($jevuser)?$jevuser->user_id:0);
			$userlist = JHTML::_('select.genericlist', $userOptions, 'jev_creatorid', 'class="inputbox" size="1" ', 'value', 'text', $creator);

			$this->assignRef("users",$userlist);
		}

	}

	function toolbarConfirmButton($task = '',  $msg='',  $icon = '', $iconOver = '', $alt = '', $listSelect = true){
		include_once(JEV_ADMINPATH."libraries/jevbuttons.php");
		$bar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton( 'Jevconfirm', $msg, $icon, $alt, $task, $listSelect ,false,"document.adminForm.updaterepeats.value" );

	}

}