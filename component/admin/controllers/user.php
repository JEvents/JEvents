<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: user.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

use Joomla\Utilities\ArrayHelper;

class AdminUserController extends JControllerLegacy   {

	/** @var string		current used task */
	protected $task=null;

	/** @var array		int or array with the choosen list id */
	protected $cid=null;

	function __construct( ){
		parent::__construct();
		$this->registerDefaultTask( 'showUser' );
		$jinput = JFactory::getApplication()->input;

		$this->task =  $jinput->get('task', '', "cmd");
		$this->cid =  $jinput->get('cid', array(0), "array");
		if (!is_array( $this->cid )) {
			$this->cid = array(0);
		}
                $this->cid = ArrayHelper::toInteger($this->cid);

		$this->registerTask( 'overview', 'showUsers' );
		$this->registerTask( 'list', 'showUsers' );
		$this->registerTask( 'edit', 'editUser' );
		$this->registerTask( 'save', 'saveUser' );
		$this->registerTask( 'publish', 'publishUser' );
		$this->registerTask( 'unpublish', 'unpublishUser' );
		$this->registerTask( 'remove', 'removeUser' );

		// Populate common data used by view
		// get the view
		$this->view = $this->getView("user","html");

		// Assign data for view
		$this->view->assignRef('task', $this->task);
	}

	function showUsers() {
		//JLoader::import( 'models.user',JPATH_COMPONENT_ADMINISTRATOR);

		$model	= $this->getModel( 'user' );	
		$this->view->setModel($model,true);

		// Set the layout
		$this->view->setLayout('overview');

		$this->view->display();
	}

	function editUser(  ) {

		////JLoader::import( 'models.user',JPATH_COMPONENT_ADMINISTRATOR);

		if (!JEVHelper::isAdminUser()) {
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);
			$this->redirect();
			return;
		}

		$model	= $this->getModel( 'user' );	
		$this->view->setModel($model,true);
		
		// Set the layout
		$this->view->setLayout('edit');

		$this->view->display();
	}

	function saveUser( ) {
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		if (!JEVHelper::isAdminUser()) {
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);
			$this->redirect();
			return;
		}
		
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid = (int) $cid[0];

		$model = $this->getModel('user');

		if ($model->store($cid,$post)) {
			$msg = JText::_( 'USER_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_USER' );
		}

		$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);
		$this->setRedirect($link, $msg);
		$this->redirect();
		
	}

	function removeUser() {
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );
		
		if (!JEVHelper::isAdminUser()) {
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);
			$this->redirect();
			return;
		}
		
		$model = $this->getModel('user');		
		$users = TableUser::getUsers($this->cid);

		$countdeleted = 0;
		foreach ($users as $user) {
			$countdeleted += $user->delete()?1:0;
		}
		if ($countdeleted = count($users)){
			$msg = JText::_( 'USERS_DELETED' );
		} else {
			$msg = JText::_( 'NOT_ALL_USERS_DELETED' );
		}

		$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);
		$this->setRedirect($link, $msg);
		$this->redirect();
		
	}
	
	function publishUser(  ) {
		$this->changeState("published",1, JText::_( 'USER_ENABLED' ));
	}

	
	function unpublishUser(  ) {
		$this->changeState("published",0, JText::_( 'USER_DISABLED' ));
	}

	function cancreate(  ) {
		$this->changeState("cancreate",1, JText::_( 'USER_CAN_CREATE_EVENTS' ));
	}

	function cannotcreate(  ) {
		$this->changeState("cancreate",0, JText::_( 'USER_CANNOT_CREATE_EVENTS' ));
	}
	
	function canedit(  ) {
		$this->changeState("canedit",1, JText::_( 'USER_CAN_EDIT_EVENTS' ));
	}

	function cannotedit(  ) {
		$this->changeState("canedit",0, JText::_( 'USER_CANNOT_EDIT_EVENTS' ));
	}

	function candeleteown(  ) {
		$this->changeState("candeleteown",1, JText::_( 'USER_CAN_DELETE_OWN' ));
	}

	function cannotdeleteown(  ) {
		$this->changeState("candeleteown",0, JText::_( 'USER_CANNOT_DELETE_OWN' ));
	}
	
	function candeleteall(  ) {
		$this->changeState("candeleteall",1, JText::_( 'USER_CAN_DELETE_ALL' ));
	}

	function cannotdeleteall(  ) {
		$this->changeState("candeleteall",0, JText::_( 'USER_CANNOT_DELETE_ALL' ));
	}
	
	function canpublishown(  ) {
		$this->changeState("canpublishown",1, JText::_( 'USER_CAN_PUBLISH_OWN' ));
	}

	function cannotpublishown(  ) {
		$this->changeState("canpublishown",0, JText::_( 'USER_CANNOT_PUBLISH_OWN' ));
	}


	function canpublishall(  ) {
		$this->changeState("canpublishall",1, JText::_( 'USER_CAN_PUBLISH_ALL' ));
	}

	function cannotpublishall(  ) {
		$this->changeState("canpublishall",0, JText::_( 'USER_CANNOT_PUBLISH_ALL' ));
	}

	function canuploadimages(  ) {
		$this->changeState("canuploadimages",1, JText::_( 'USER_CAN_UPLOAD_IMAGES' ));
	}

	function cannotuploadimages(  ) {
		$this->changeState("canuploadimages",0, JText::_( 'USER_CANNOT_UPLOAD_IMAGES' ));
	}
	
	function canuploadmovies(  ) {
		$this->changeState("canuploadmovies",1, JText::_( 'USER_CAN_UPLOAD_FILES' ));
	}

	function cannotuploadmovies(  ) {
		$this->changeState("canuploadmovies",0, JText::_( 'USER_CANNOT_UPLOAD_FILES' ));
	}

	// These apply to extra attributes - user specific or global
	function cancreateown(  ) {
		$this->changeState("cancreateown",1, JText::_( 'USER_CAN_CREATE_OWN_EXTRAS' ));
	}

	function cannotcreateown(  ) {
		$this->changeState("cancreateown",0, JText::_( 'USER_CANNOT_CREATE_OWN_EXTRAS' ));
	}
	
	function cancreateglobal(  ) {
		$this->changeState("cancreateglobal",1, JText::_( 'USER_CAN_CREATE_GLOBAL_EXTRAS' ));
	}

	function cannotcreateglobal(  ) {
		$this->changeState("cancreateglobal",0, JText::_( 'USER_CANNOT_CREATE_GLOBAL_EXTRAS' ));
	}
	
	
	private function changeState($field, $newstate, $successMessage){
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		if (!JEVHelper::isAdminUser()) {
			$msg = "Not Authorised";
			$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);		
			$this->setRedirect($link, $msg);
			$this->redirect();
			return;
		}
		
		$model = $this->getModel('user');
		$user = $model->getUser();
		$user->$field = $newstate;
		if ($user->store()){
			$msg = $successMessage;
		} else {
			$msg = JText::_( 'ERROR_UPDATING_USER' );
		}

		$link = JRoute::_('index.php?option='.JEV_COM_COMPONENT.'&task=user.list',false);
		$this->setRedirect($link, $msg);
		$this->redirect();
	}
	
}
