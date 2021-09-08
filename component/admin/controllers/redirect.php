<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: redirect.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\TagsHelper;

class AdminRedirectController extends Joomla\CMS\MVC\Controller\AdminController
{

	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerDefaultTask("display");

	}

	/**
	 * Do the safe redirect
	 *
	 */
	function display($cachable = false, $urlparams = array())
	{
		$app    = Factory::getApplication();
		$input = $app->input;

		$task = str_replace('redirect.', '',$input->getCmd('task', 'redirect.com_categories'));

		$tasks = array('com_jevlocations', 'com_rsvppro', 'com_jevpeople', 'com_jeventstags', 'com_categories');
		if (!in_array($task, $tasks))
		{
			$app->redirect(Route::_('index.php?option=com_jevents', false));
		}
		else
		{
			if ($task === 'com_categories')
			{
				$app->redirect(Route::_('index.php?option=' . $task . '&extension=com_jevents', false));
			}
			else
			{
				if (ComponentHelper::isEnabled($task))
				{
					$app->redirect(Route::_('index.php?option=' . $task, false));
				}
				else
				{
					$app->enqueueMessage(Text::sprintf("COM_JEVENTS_DISABLED_OPTION_DESC_WITH_LINK", "https://www.jevents.net/join-club-jevents"), 'warning');
					$app->redirect(Route::_('index.php?option=com_jevents', false));
				}
			}
		}

	}

}
