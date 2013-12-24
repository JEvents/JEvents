<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * This file based on Joomla config component Copyright (C) 2005 - 2008 Open Source Matters.
 *
 * @version     $Id: params.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class AdminParamsController extends JControllerAdmin
{

	/**
	 * Custom Constructor
	 */
	function __construct($default = array())
	{
		$user = JFactory::getUser();

		if (!JEVHelper::isAdminUser())
		{
			JFactory::getApplication()->redirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be admin");
			return;
		}

		$default['default_task'] = 'edit';
		parent::__construct($default);

		$this->registerTask('apply', 'save');

	}

	/**
	 * Show the configuration edit form
	 * @param string The URL option
	 */
	function edit($key = NULL, $urlVar = NULL)
	{

		// get the view
		$this->view = $this->getView("params", "html");

		//$model = $this->getModel('params');
		$model = $this->getModel('component');
		$table =  JTable::getInstance('extension');
		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		// Backwards compatatbility
		$table->id = $table->extension_id;
		$table->option = $table->element;

		// Set the layout
		$this->view->setLayout('edit');

		$this->view->assignRef('component', $table);
		$this->view->setModel($model, true);
		$this->view->display();

	}

	/**
	 * Save the configuration
	 */
	function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		//echo $this->getTask();
		//exit;
		$component = JEV_COM_COMPONENT;

		$model = $this->getModel('params');
		$table =  JTable::getInstance('extension');
		//if (!$table->loadByOption( $component ))
		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		

		$post = JRequest::get('post');
		$post['params'] = JRequest::getVar('jform', array(), 'post', 'array');
		$post['option'] = $component;
		$table->bind($post);

		// pre-save checks
		if (!$table->check())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// if switching from single cat to multi cat then reset the table entries
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("multicategory", 0) && isset($post["params"]['multicategory']) && $post["params"]['multicategory'] == 1)
		{
			$db = JFactory::getDbo();
			$sql = "DELETE FROM #__jevents_catmap";
			$db->setQuery($sql);
			$db->query();			
			
			$sql = "REPLACE INTO #__jevents_catmap (evid, catid) SELECT ev_id, catid from #__jevents_vevent WHERE catid in (SELECT id from #__categories where extension='com_jevents')";
			$db->setQuery($sql);
			$db->query();
		}

		// save the changes
		if (!$table->store())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// Now save the form permissions data
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$option = JEV_COM_COMPONENT;
		$comp = JComponentHelper::getComponent(JEV_COM_COMPONENT);
		$id = $comp->id;
		// Validate the posted data.
		JForm::addFormPath(JPATH_COMPONENT);
		JForm::addFieldPath(JPATH_COMPONENT . '/elements');

		$form = $model->getForm();
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
			$app = JFactory::getApplication();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);
			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . '&task=params.edit', false));
			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id' => $id,
			'option' => $option
		);
		$return = $model->saveRules($data);
		
		// Clear cache of com_config component.
		$this->cleanCache('_system');

		// If caching is enabled then remove the component params from the cache!
		// Bug fixed in Joomla 3.2.1 ??
		$joomlaconfig = JFactory::getConfig();
		if ($joomlaconfig->get("caching",0)){
			$cacheController = JFactory::getCache('_system', 'callback');
			$cacheController->cache->remove("com_jevents");
		}

		//SAVE AND APPLY CODE FROM PRAKASH
		switch ($this->getTask()) {
			case 'apply':
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=params.edit', JText::_('CONFIG_SAVED'));
				break;
			default:
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=cpanel.cpanel", JText::_('CONFIG_SAVED'));
				break;
		}
		//$this->setRedirect( 'index.php?option='.JEV_COM_COMPONENT."&task=cpanel.cpanel", JText::_( 'CONFIG_SAVED' ) );
		//$this->setMessage(JText::_( 'CONFIG_SAVED' ));
		//$this->edit();

	}

	/**
	 * Apply the configuration
	 */
	function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$component = JEV_COM_COMPONENT;

		$model = $this->getModel('params');
		$table =  JTable::getInstance('extension');
		//if (!$table->loadByOption( $component ))
		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		

		$post = JRequest::get('post');
		$post['option'] = $component;
		$table->bind($post);

		// pre-save checks
		if (!$table->check())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// save the changes
		if (!$table->store())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// Now save the form permissions data
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$option = JEV_COM_COMPONENT;
		$comp = JComponentHelper::getComponent(JEV_COM_COMPONENT);
		$id = $comp->id;
		// Validate the posted data.
		JForm::addFormPath(JPATH_COMPONENT);
		JForm::addFieldPath(JPATH_COMPONENT . '/elements');
		$form = $model->getForm();
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			$app = JFactory::getApplication();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);
			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . '&task=params.edit', false));
			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id' => $id,
			'option' => $option
		);
		$return = $model->saveRules($data);
		
		// Clear cache of com_config component.
		$this->cleanCache('_system');
				
		$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=params.edit", JText::_('CONFIG_SAVED'));
		//$this->setMessage(JText::_( 'CONFIG_SAVED' ));
		//$this->edit();

	}

	/**
	 * Cancel operation
	 */
	function cancel($key=NULL)
	{
		$this->setRedirect('index.php');

	}


	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$conf = JFactory::getConfig();
		$dispatcher = JDispatcher::getInstance();

		$options = array(
			'defaultgroup' => ($group) ? $group : (isset($this->option) ? $this->option : JFactory::getApplication()->input->get('option')),
			'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$this->event_clean_cache = 'onContentCleanCache';
		$dispatcher->trigger($this->event_clean_cache, $options);
	}
	
}
