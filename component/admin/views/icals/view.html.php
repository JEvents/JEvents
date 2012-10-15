<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3548 2012-04-20 09:25:43Z geraintedwards $
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

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JHTML::stylesheet( 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		else JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'ICALS' ));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'ICALS' ), 'jevents' );

		JToolBarHelper::publishList('icals.publish');
		JToolBarHelper::unpublishList('icals.unpublish');
		JToolBarHelper::addNew('icals.edit');
		JToolBarHelper::editList('icals.edit');
		JToolBarHelper::deleteList('Delete Ical and all associated events and repeats?','icals.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', 'JEV_ADMIN_CPANEL', false );
		//JToolBarHelper::help( 'screen.ical', true);

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option='.JEV_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');
	}

	function edit($tpl = null)
	{

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JHTML::stylesheet( 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		else JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		JEVHelper::script('editical.js','administrator/components/'.JEV_COM_COMPONENT.'/assets/js/');

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'EDIT_ICS' ));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'EDIT_ICS' ), 'jevents' );

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

		$db = JFactory::getDbo();
		if ($params->getValue("authorisedonly",0)){
			// get authorised users
			$sql = "SELECT u.* FROM #__jev_users as jev LEFT JOIN #__users as u on u.id=jev.user_id where jev.published=1 and jev.cancreate=1";
			$db=& JFactory::getDBO();
			$db->setQuery( $sql );
			$users = $db->loadObjectList();
		}
		else {
			if (JVersion::isCompatible("1.6.0")) {
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

				$sql = "SELECT * FROM #__users where id IN (".implode(",",array_values($users)).") ORDER BY name asc";
				$db->setQuery( $sql );
				$users = $db->loadObjectList();

			}
			else {
				$minaccess = $params->getValue("jevcreator_level",19);
				// get users AUTHORS and above
				$sql = "SELECT * FROM #__users where gid>=".$minaccess;
				$db->setQuery( $sql );
				$users = $db->loadObjectList();

			}

		}
		$userOptions = array();
		foreach( $users as $user )
		{
			$userOptions[] = JHTML::_('select.option', $user->id, $user->name. " ($user->username)" );
		}
		$jevuser	= JFactory::getUser();
		if ($this->editItem && isset($this->editItem->ics_id) && $this->editItem->ics_id >0 && $this->editItem->created_by>0){
			$created_by = $this->editItem->created_by;
		}
		else {
			$created_by = $jevuser->id;
		}
		if (count($userOptions)>0){
			$userlist = JHTML::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1" ', 'value', 'text', $created_by);
		}
		else {
			$userList = "";
		}
		$this->assignRef("users",$userlist);


		JHTML::_('behavior.tooltip');
	}

}