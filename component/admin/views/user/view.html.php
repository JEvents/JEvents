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

jimport( 'joomla.filesystem.file');
jimport( 'joomla.application.component.view');
jimport('joomla.html.pane');

class AdminUserViewUser extends JEventsAbstractView
{
	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function overview($tpl = null)
	{

		$document =& JFactory::getDocument();
		// this already includes administrator
		$livesite = JURI::base();
		$document->addStyleSheet($livesite.'components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');

		$document->setTitle(JText::_( 'JEVENTS' ) . ' :: ' .JText::_( 'JEVENTS' ));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'USERS' ), 'jevents' );
		JToolBarHelper::addNew("user.edit");
		JToolBarHelper::editList("user.edit");
		//JToolBarHelper::publish("user.publish");
		//JToolBarHelper::unpublish("user.unpublish");
		JToolBarHelper::deleteList("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_USER", "user.remove");
		//JToolBarHelper::preferences(JEV_COM_COMPONENT, '580', '750');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', 'JEV_ADMIN_CPANEL', false );
		//JToolBarHelper::help( 'screen.user', true);

		$search		= JFactory::getApplication()->getUserStateFromRequest( "usersearch{".JEV_COM_COMPONENT."}", 'search', '' );
		$db = JFactory::getDbo();
		$search		= $db->getEscaped( trim( strtolower( $search ) ) );
		
		$option				= JRequest::getCmd('option', JEV_COM_COMPONENT);

		$pagination = & $this->get( 'Pagination' );
		$users	= &$this->get('users');

		$this->assignRef('pagination',	$pagination);
		$this->assignRef('users', $users);
		$this->assignRef('search', $search);

		JHTML::_('behavior.tooltip');
	}

	function edit($tpl = null)
	{

		$document =& JFactory::getDocument();
		// this already includes administrator
		$livesite = JURI::base();
		if (JVersion::isCompatible("1.6.0")){
			JEVHelper::stylesheet('eventsadmin16.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/');
		}
		else {
			$document->addStyleSheet($livesite.'components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		}

		$document->setTitle(JText::_( 'JEVENTS' ) . ' :: ' .JText::_( 'JEVENTS' ));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'JEV_EDIT_USER' ), 'jevents' );

		JToolBarHelper::save("user.save");
		JToolBarHelper::cancel("user.overview");

		//JToolBarHelper::help( 'edit.user', true);

		$option				= JRequest::getCmd('option', JEV_COM_COMPONENT);

		$db=& JFactory::getDBO();

		$params =& JComponentHelper::getParams( JEV_COM_COMPONENT );
		if (JVersion::isCompatible("1.6.0")) {
			$rules = JAccess::getAssetRules("com_jevents", true);
			$data = $rules->getData();
			$creatorgroups = $data["core.create"]->getData();
			foreach ($data["core.admin"]->getData() as $creatorgroup => $permission) {
				if ($permission){
					$creatorgroups[$creatorgroup]=$permission;
				}
			}
			// array_merge does a re-indexing !!
			//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups["core.create"]->getData());
			$users = array(0);
			foreach ($creatorgroups as $creatorgroup => $permission){
				if ($permission==1){
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


		$userOptions[] = JHTML::_('select.option', '-1',JText::_('SELECT_USER') );
		foreach( $users as $user )
		{
			$userOptions[] = JHTML::_('select.option', $user->id, $user->name. " ($user->username)" );
		}
		$jevuser	= &$this->get('user');
		$userlist = JHTML::_('select.genericlist', $userOptions, 'user_id', 'class="inputbox" size="1" ', 'value', 'text', $jevuser->user_id);

		JLoader::register('JEventsCategory',JEV_ADMINPATH."/libraries/categoryClass.php");

		$categories = JEventsCategory::categoriesTree();
		$lists['categories'] = JHTML::_('select.genericlist', $categories, 'categories[]', 'multiple="multiple" size="15"', 'value', 'text',explode("|",$jevuser->categories));

		// get calendars
		$sql = "SELECT label as text, ics_id as value FROM #__jevents_icsfile where icaltype=2";
		$db->setQuery( $sql );
		$calendars = $db->loadObjectList();
		$lists['calendars'] = JHTML::_('select.genericlist', $calendars, 'calendars[]', 'multiple="multiple" size="15"', 'value', 'text',explode("|",$jevuser->calendars));

		$this->assignRef('lists',$lists);

		$this->assignRef("users",$userlist);
		$this->assignRef('jevuser', $jevuser);

		JHTML::_('behavior.tooltip');
	}


}
