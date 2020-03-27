<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: user.php 1406 2010-11-09 11:48:51Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.application.component.model');
JLoader::import("jevuser", JPATH_COMPONENT_ADMINISTRATOR . "/tables/");

/**
 * @package        Joom!Fish
 * @subpackage     User
 */
class AdminUserModelUser extends BaseDatabaseModel
{
	/**
	 * @var string    name of the current model
	 * @access private
	 */
	var $_modelName = 'user';

	/**
	 * @var array list of current users
	 * @access private
	 */
	var $_users = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * default constrcutor
	 */
	function __construct()
	{

		parent::__construct();
		$input = Factory::getApplication()->input;

		$app    = Factory::getApplication();
		$option = $input->get('option', '');
		// Get the pagination request variables
		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * return the model name
	 */
	function getName()
	{

		return $this->_modelName;
	}

	/**
	 * Method to get a pagination object for the weblinks
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new \Joomla\CMS\Pagination\Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function gettotal()
	{

		return TableUser::getUserCount();
	}

	/**
	 * generic method to load the user related data
	 * @return array of users
	 */
	function getUsers()
	{

		TableUser::checkTable();
		if ($this->_users == null)
		{
			$this->_loadUsers();
		}

		return $this->_users;
	}

	/**
	 * Method to load the users in the system
	 *
	 * @return void
	 */
	function _loadUsers()
	{

		$this->_users = TableUser::getUsers();
	}

	/**
	 * generic method to load the user related data
	 * @return array of users
	 */
	function getUser()
	{
		$input  = Factory::getApplication()->input;

		$cid = $input->get("cid", array(), "array");
		$cid = ArrayHelper::toInteger($cid);
		if (count($cid) > 0)
		{
			$id = $cid[0];
		}
		else $id = 0;
		$user = new TableUser();
		if ($id > 0)
		{
			$user->load($id);
		}

		return $user;
	}

	/**
	 * Method to store user information
	 */
	function store($cid, $data)
	{

		$isNew  = 1;
		$user   = new TableUser();

		if ($cid > 0)
		{
			$isNew = 0;
			$user->load($cid);
		}
		// fix the calendars and categories fields
		if ($data['calendars'] == 'select') $data['calendars'] = array();
		if ($data['categories'] == 'select') $data['categories'] = array();
		$success = $user->save($data);

		$user->isNew = $isNew;

		if ($success)
		{
			PluginHelper::importPlugin("jevents");
			$set        = Factory::getApplication()->triggerEvent('afterSaveUser', array($user));
		}

		return $success;
	}


}
