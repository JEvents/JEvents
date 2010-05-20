<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: user.php 1457 2009-06-01 09:49:51Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');


class AdminUserController extends JController   {

	/** @var string		current used task */
	var $task=null;

	/** @var array		int or array with the choosen list id */
	var $cid=null;

	function __construct( ){
		parent::__construct();
		$this->registerDefaultTask( 'showUser' );

		$this->task =  JRequest::getVar( 'task', '' );
		$this->cid =  JRequest::getVar( 'cid', array(0) );
		if (!is_array( $this->cid )) {
			$this->cid = array(0);
		}

		$this->registerTask( 'overview', 'showUsers' );
		$this->registerTask( 'list', 'showUsers' );
		$this->registerTask( 'edit', 'editUser' );
		$this->registerTask( 'save', 'saveUser' );
		$this->registerTask( 'publish', 'publishUser' );
		$this->registerTask( 'unpublish', 'unpublishUser' );
		$this->registerTask( 'remove', 'removeUser' );

		// Populate common data used by view
		// get the view
		$this->view = & $this->getView("user","html");

		// Assign data for view
		$this->view->assignRef('task', $this->task);
	}

	function showUsers() {
		//JLoader::import( 'models.user',JPATH_COMPONENT_ADMINISTRATOR);

		$model	=& $this->getModel( 'user' );	
		$this->view->setModel($model,true);

		// Set the layout
		$this->view->setLayout('overview');

		$this->view->display();
	}

	function editUser(  ) {

		////JLoader::import( 'models.user',JPATH_COMPONENT_ADMINISTRATOR);

		$siteuser = JFactory::getUser();
		if ($siteuser->usertype!="Administrator" && $siteuser->usertype!="Super Administrator"){
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);		
			return;
		}

		$model	=& $this->getModel( 'user' );	
		$this->view->setModel($model,true);
		
		// Set the layout
		$this->view->setLayout('edit');

		$this->view->display();
	}

	function saveUser( ) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$siteuser = JFactory::getUser();
		if ($siteuser->usertype!="Administrator" && $siteuser->usertype!="Super Administrator"){
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);		
			return;
		}
		
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid = (int) $cid[0];

		$model = $this->getModel('user');

		if ($model->store($cid,$post)) {
			$msg = JText::_( 'User Saved' );
		} else {
			$msg = JText::_( 'Error Saving User' );
		}

		$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);
		$this->setRedirect($link, $msg);
		
	}

	function removeUser() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$siteuser = JFactory::getUser();
		if ($siteuser->usertype!="Administrator" && $siteuser->usertype!="Super Administrator"){
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);		
			return;
		}
		
		$model = $this->getModel('user');		
		$users = TableUser::getUsers($this->cid);

		$countdeleted = 0;
		foreach ($users as $user) {
			$countdeleted += $user->delete()?1:0;
		}
		if ($countdeleted = count($users)){
			$msg = JText::_( 'Users Deleted' );
		} else {
			$msg = JText::_( 'Not All Users Deleted' );
		}

		$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);
		$this->setRedirect($link, $msg);
		
	}
	
	function publishUser(  ) {
		$this->changeState("published",1, JText::_( 'User Enabled' ));
	}

	
	function unpublishUser(  ) {
		$this->changeState("published",0, JText::_( 'User Disabled' ));
	}

	function cancreate(  ) {
		$this->changeState("cancreate",1, JText::_( 'User Can Create Events' ));
	}

	function cannotcreate(  ) {
		$this->changeState("cancreate",0, JText::_( 'User Cannot Create Events' ));
	}
	
	function canedit(  ) {
		$this->changeState("canedit",1, JText::_( 'User Can Edit Events' ));
	}

	function cannotedit(  ) {
		$this->changeState("canedit",0, JText::_( 'User Cannot Edit Events' ));
	}

	function candeleteown(  ) {
		$this->changeState("candeleteown",1, JText::_( 'User Can Delete Own' ));
	}

	function cannotdeleteown(  ) {
		$this->changeState("candeleteown",0, JText::_( 'User Cannot Delete Own' ));
	}
	
	function candeleteall(  ) {
		$this->changeState("candeleteall",1, JText::_( 'User Can Delete All' ));
	}

	function cannotdeleteall(  ) {
		$this->changeState("candeleteall",0, JText::_( 'User Cannot Delete All' ));
	}
	
	function canpublishown(  ) {
		$this->changeState("canpublishown",1, JText::_( 'User Can Publish Own' ));
	}

	function cannotpublishown(  ) {
		$this->changeState("canpublishown",0, JText::_( 'User Cannot Publish Own' ));
	}


	function canpublishall(  ) {
		$this->changeState("canpublishall",1, JText::_( 'User Can Publish All' ));
	}

	function cannotpublishall(  ) {
		$this->changeState("canpublishall",0, JText::_( 'User Cannot Publish All' ));
	}

	function canuploadimages(  ) {
		$this->changeState("canuploadimages",1, JText::_( 'User Can Upload Images' ));
	}

	function cannotuploadimages(  ) {
		$this->changeState("canuploadimages",0, JText::_( 'User Cannot Upload Images' ));
	}
	
	function canuploadmovies(  ) {
		$this->changeState("canuploadmovies",1, JText::_( 'User Can Upload Files' ));
	}

	function cannotuploadmovies(  ) {
		$this->changeState("canuploadmovies",0, JText::_( 'User Cannot Upload Files' ));
	}

	// These apply to extra attributes - user specific or global
	function cancreateown(  ) {
		$this->changeState("cancreateown",1, JText::_( 'User Can Create Own Extras' ));
	}

	function cannotcreateown(  ) {
		$this->changeState("cancreateown",0, JText::_( 'User Cannot Create Own Extras' ));
	}
	
	function cancreateglobal(  ) {
		$this->changeState("cancreateglobal",1, JText::_( 'User Can Create Global Extras' ));
	}

	function cannotcreateglobal(  ) {
		$this->changeState("cancreateglobal",0, JText::_( 'User Cannot Create Global Extras' ));
	}
	
	
	private function changeState($field, $newstate, $successMessage){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$siteuser = JFactory::getUser();
		if ($siteuser->usertype!="Administrator" && $siteuser->usertype!="Super Administrator"){
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);		
			return;
		}
		
		$model = $this->getModel('user');
		$user = $model->getUser();
		$user->$field = $newstate;
		if ($user->store()){
			$msg = $successMessage;
		} else {
			$msg = JText::_( 'Error Updating User' );
		}

		$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);
		$this->setRedirect($link, $msg);		
		
	}
	
}
