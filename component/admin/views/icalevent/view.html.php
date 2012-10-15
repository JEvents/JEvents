<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3401 2012-03-22 15:35:38Z geraintedwards $
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

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
			JHTML::stylesheet('administrator/components/' . JEV_COM_COMPONENT . '/assets/css/eventsadmin.css');
		else
			JHTML::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_( 'ICAL_EVENTS' ));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_( 'ICAL_EVENTS' ), 'jevents');

		//JToolBarHelper::custom('icalevent.csvimport','upload.png','upload.png','JEV_ADMIN_CSVIMPORT',false);
		JToolBarHelper::publishList('icalevent.publish');
		JToolBarHelper::unpublishList('icalevent.unpublish');
		JToolBarHelper::addNew('icalevent.edit');
		JToolBarHelper::editList('icalevent.edit');
		JToolBarHelper::custom('icalevent.editcopy', 'copy.png', 'copy.png', 'JEV_ADMIN_COPYEDIT');
		JToolBarHelper::deleteList('Delete Event and all repeats?', 'icalevent.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('cpanel.cpanel', 'default.png', 'default.png', 'JEV_ADMIN_CPANEL', false);
		//JToolBarHelper::help( 'screen.ical', true);

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option=' . JEV_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');

	}

	function edit($tpl = null)
	{
		$document = & JFactory::getDocument();
		include(JEV_LIBS . "editStrings.php");
		$document->addScriptDeclaration($editStrings);

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');
		JEVHelper::script('editical.js', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/js/');

		$document->setTitle(JText::_( 'EDIT_ICAL_EVENT' ));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_( 'EDIT_ICAL_EVENT' ), 'jevents');

		$bar = & JToolBar::getInstance('toolbar');
		if ($this->id > 0)
		{
			if ($this->editCopy)
			{
				$this->toolbarConfirmButton("icalevent.save", JText::_("save_copy_warning"), 'save', 'save', 'Save', false);
				$this->toolbarConfirmButton("icalevent.savenew", JText::_("save_copy_warning"), 'save', 'save', 'JEV_Save_New', false);
				$this->toolbarConfirmButton("icalevent.apply", JText::_("save_copy_warning"), 'apply', 'apply', 'Jev_Apply', false);
			}
			else
			{
				$this->toolbarConfirmButton("icalevent.save", JText::_("save_icalevent_warning"), 'save', 'save', 'Save', false);
				$this->toolbarConfirmButton("icalevent.savenew", JText::_("save_icalevent_warning"), 'save', 'save', 'JEV_Save_New', false);
				$this->toolbarConfirmButton("icalevent.apply", JText::_("save_icalevent_warning"), 'apply', 'apply', 'JEV_Apply', false);
			}
		}
		else
		{
			JToolBarHelper::save('icalevent.save');
			JToolBarHelper::save('icalevent.savenew', "JEV_Save_New");
			if (JEVHelper::isEventEditor())
				JToolBarHelper::apply('icalevent.apply', "JEV_Apply");				
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
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
			JHTML::stylesheet('administrator/components/' . JEV_COM_COMPONENT . '/assets/css/eventsadmin.css');
		else
			JHTML::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_( 'CSV_IMPORT' ));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_( 'CSV_IMPORT' ), 'jevents');

		JToolBarHelper::cancel('icalevent.list');

		$this->_hideSubmenu();

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option=' . JEV_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');

	}

	protected function setCreatorLookup()
	{
		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser = JEVHelper::getAuthorisedUser();
		$user = JFactory::getUser();

		if (JVersion::isCompatible("1.6.0"))
		{
			//$access = JAccess::check($user->id, "core.deleteall", "com_jevents");
			$access = $user->authorise('core.admin', 'com_jevents');
		}
		else
		{
			// Get an ACL object
			$acl = & JFactory::getACL();
			$grp = $acl->getAroGroup($user->get('id'));
			// if no valid group (e.g. anon user) then skip this.
			if (!$grp)
				return;

			$access = $acl->is_group_child_of($grp->name, 'Public Backend');
		}


		$db = JFactory::getDBO();
		if (($jevuser && $jevuser->candeleteall) || $access)
		{

			if (JVersion::isCompatible("1.6.0"))
			{
				$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				// if authorised only then load from database
				if ($authorisedonly)
				{
					$sql = "SELECT tl.*, ju.*  FROM #__jev_users AS tl ";
					$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
					$sql .= " WHERE tl.cancreate=1";
					$sql .= " ORDER BY ju.name ASC";
					$db->setQuery($sql);
					$users = $db->loadObjectList();
				}
				else
				{
					$rules = JAccess::getAssetRules("com_jevents", true);
					$creatorgroups = $rules->getData();
					// need to merge the arrays because of stupid way Joomla checks super user permissions
					//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups["core.create"]->getData());
					// use union orf arrays sincee getData no longer has string keys in the resultant array
					//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
					// use union orf arrays sincee getData no longer has string keys in the resultant array
					$creatorgroupsdata = $creatorgroups["core.admin"]->getData();
					// take the higher permission setting
					foreach ($creatorgroups["core.create"]->getData() as $creatorgroup => $permission)
					{
						if ($permission){
							$creatorgroupsdata[$creatorgroup]=$permission;
						}
					}

					$users = array(0);
					foreach ($creatorgroupsdata as $creatorgroup => $permission)
					{
						if ($permission == 1)
											{
							$users = array_merge(JAccess::getUsersByGroup($creatorgroup, true), $users);
						}
					}

					$sql = "SELECT * FROM #__users where id IN (" . implode(",", array_values($users)) . ") ORDER BY name asc";
					$db->setQuery($sql);
					$users = $db->loadObjectList();
				}
			}
			else
			{
				$db = JFactory::getDBO();

				$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				// if authorised only then load from database
				if ($authorisedonly)
				{
					$sql = "SELECT tl.*, ju.*  FROM #__jev_users AS tl ";
					$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
					$sql .= " WHERE tl.cancreate=1";
					$sql .= " ORDER BY ju.name ASC";
					$db->setQuery($sql);
					$users = $db->loadObjectList();
				}
				else
				{
					$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
					$minaccess = $params->getValue("jevcreator_level", 19);
					$sql = "SELECT * FROM #__users where gid>=" . $minaccess;
					$sql .= " ORDER BY name ASC";
					$db->setQuery($sql);
					$users = $db->loadObjectList();
				}
			}

			$userOptions[] = JHTML::_('select.option', '-1', JText::_('SELECT_USER'));
			foreach ($users as $user)
			{
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name. " ( ".$user->username." )");
			}
			$creator = $this->row->created_by() > 0 ? $this->row->created_by() : (isset($jevuser) ? $jevuser->user_id : 0);
			$userlist = JHTML::_('select.genericlist', $userOptions, 'jev_creatorid', 'class="inputbox" size="1" ', 'value', 'text', $creator);

			$this->assignRef("users", $userlist);
		}

	}

	function toolbarConfirmButton($task = '', $msg='', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		include_once(JEV_ADMINPATH . "libraries/jevbuttons.php");
		$bar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jevconfirm', $msg, $icon, $alt, $task, $listSelect, false, "document.adminForm.updaterepeats.value");

	}

}
