<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: customcss.php
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

jimport('joomla.application.component.controlleradmin');

class CustomCssController extends Joomla\CMS\MVC\Controller\BaseController
{

	/**
	 * Controler for the Custom CSS Editor
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);

		$this->registerTask('show', 'customcss');
		$this->registerTask('apply', 'save');
		$this->registerDefaultTask("customcss");
	}


	public function display($cachable = false, $urlparams = array())
	{

		// get the view
		$this->view = $this->getView('customcss', 'html', 'customcssView');

		$mainframe = Factory::getApplication();

		//Hold on... Are you a super user?
		$user = Factory::getUser();

		// Get/Create the model
		if ($model = $this->getModel())
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		if (!$user->authorise('core.admin'))
		{
			$msg     = JTExt::_('JEV_ERROR_NOT_AUTH_CSS');
			$msgType = 'error';
			$mainframe->enqueueMessage($msg, $msgType);
			$mainframe->redirect('index.php?option=com_jevents&msg=' . $msg . '&msgtype=' . $msgType . '');

			return;
		}

		// Set the layout
		$this->view->setLayout('default');
		$this->view->title = Text::_('JEV_CUSTOM_CSS');

		$this->view->display();
	}

	//Cancel Function
	public function cancel()
	{

		$this->setRedirect(Route::_('index.php?option=com_jevents&view=cpanel', false));
	}

	//Save, apply, Save & Close Function
	public function save()
	{

		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$app          = Factory::getApplication();
		$data         = $this->input->post->get('jform', array(), 'array');
		$task         = $this->getTask();
		$model        = $this->getModel();
		$fileName     = $app->input->get('file');
		$explodeArray = explode(':', base64_decode($fileName));

		// Access check.
		if (!$this->allowSave())
		{
			$app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');

			return false;
		}

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		$data = $model->validate($form, $data);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the edit screen.
			$url = 'index.php?option=com_templates&view=customcss';
			$this->setRedirect(Route::_($url, false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data))
		{
			// Redirect back to the edit screen.
			$this->setMessage(Text::sprintf('JERROR_SAVE_FAILED', $model->getError()), 'warning');
			$url = 'index.php?option=com_jevents&view=customcss';
			$this->setRedirect(Route::_($url, false));

			return false;
		}

		$this->setMessage(Text::_('COM_JEVENTS_CUSTOM_CSS_FILE_SAVE_SUCCESS'));

		// Redirect the user based on the chosen task.
		if ($task === 'apply')
		{
			// Redirect back to the edit screen.
			$url = 'index.php?option=com_jevents&view=customcss';
			$this->setRedirect(Route::_($url, false));
		}
		else
		{
			// Redirect to the list screen.
			$url = 'index.php?option=com_jevents';
			$this->setRedirect(Route::_($url, false));
		}

		return '';
	}

	protected function allowSave()
	{

		return $this->allowEdit();
	}

	protected function allowEdit()
	{

		return Factory::getUser()->authorise('core.admin', 'com_jevents');
	}
}
