<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3546 2012-04-20 09:08:44Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controlleradmin');

class CustomCssController extends JControllerAdmin
{

	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('show', 'customcss');
		$this->registerDefaultTask("customcss");

	}

	public function display($cachable = false, $urlparams = array())
	{
		// get the view
		$this->view = $this->getView('customcss', 'html', 'customcssView');

		$mainframe = JFactory::getApplication();

		//Hold on... Are you a super user?
		$user = JFactory::getUser();

		// Get/Create the model
		if ($model = $this->getModel()) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		if (!$user->authorise('core.admin')) {
			$msg = JTExt::_('JEV_ERROR_NOT_AUTH_CSS');
			$msgType = 'error';
			$mainframe->enqueueMessage($msg, $msgType);
			$mainframe->redirect('index.php?option=com_jevents&msg=' . $msg . '&msgtype=' . $msgType . '');
			return;
		}

		// Set the layout
		$this->view->setLayout('default');
		$this->view->assign('title', JText::_('JEV_CUSTOM_CSS'));

		$this->view->display();
	}
}
