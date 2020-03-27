<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * This file based on Joomla config component Copyright (C) 2005 - 2008 Open Source Matters.
 *
 * @version     $Id: params.php 2214 2011-06-20 13:42:27Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');

// on some servers with Xcode both classes seem to be 'compiled' and it throws an error but if we add this second test its ok - go figure .
if (!defined("JEVADPARMOD"))
{
	define("JEVADPARMOD", 1);

	class AdminParamsModelParams extends AdminModel
	{

		/**
		 * Get the params for the configuration variables
		 */
		function &getParams()
		{

			static $instance;
			$input = Factory::getApplication()->input;

			if ($instance == null)
			{
				$component = JEV_COM_COMPONENT;

				$table = Table::getInstance('extension');
				//if (!$table->loadByOption( $component ))
				if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
				{
					Factory::getApplication()->enqueueMessage('500 - ' . Text::_('JEV_NOT_A_VALID_COM'), 'warning');

					return false;
				}

				// work out file path
				if ($path = $input->getString('path'))
				{
					$path = Path::clean(JPATH_SITE . '/' . $path);
					Path::check($path);
				}
				else
				{
					$option = preg_replace('#\W#', '', isset($table->element) ? $table->element : $table->option);
					$path   = JPATH_ADMINISTRATOR . '/' . 'components' . '/' . $option . '/' . 'config.xml';
				}

				// Use our own class to add more functionality!
				include_once(JEV_ADMINLIBS . "jevparams.php");
				if (file_exists($path))
				{
					$instance = new JevParameter($table->params, $path);
				}
				else
				{
					$instance = new JevParameter($table->params);
				}
			}

			return $instance;

		}

		/**
		 * Method to get the record form.
		 *
		 * @param    array   $data     Data for the form.
		 * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
		 *
		 * @return    mixed    A Form object on success, false on failure
		 * @since    1.6
		 */
		public function getForm($data = array(), $loadData = true)
		{

			// Get the form.
			$form = $this->loadForm('com_jevents.params', 'config', array('control' => 'jform', 'load_data' => $loadData), false, "//config");
			if (empty($form))
			{
				return false;
			}

			return $form;

		}

		/**
		 * Method to save the configuration data.
		 *
		 * @param    array    An array containing all global config data.
		 *
		 * @return    bool    True on success, false on failure.
		 * @since    1.6
		 */
		public function save($data)
		{

			$table = Table::getInstance('extension');

			// Save the rules.
			if (isset($data['params']) && isset($data['params']['rules']))
			{
				jimport('joomla.access.rules');
				$rules = new Rules($data['params']['rules']);
				$asset = Table::getInstance('asset');

				if (!$asset->loadByName($data['option']))
				{
					$root = Table::getInstance('asset');
					$root->loadByName('root.1');
					$asset->name  = $data['option'];
					$asset->title = $data['option'];
					$asset->setLocation($root->id, 'last-child');
				}
				$asset->rules = (string) $rules;

				if (!$asset->check() || !$asset->store())
				{
					$this->setError($asset->getError());

					return false;
				}
				// We don't need this anymore
				unset($data['option']);
				unset($data['params']['rules']);
			}

			// Load the previous Data
			if (!$table->load($data['id']))
			{
				$this->setError($table->getError());

				return false;
			}
			unset($data['id']);

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());

				return false;
			}

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}

			// Clean the cache.
			$cache = Factory::getCache('com_config');
			$cache->clean();

			return true;

		}

		public function saveRules($data)
		{

			$table = Table::getInstance('extension');

			// Save the rules.
			if (isset($data['params']) && isset($data['params']['rules']))
			{
				jimport('joomla.access.rules');
				$rules = new Rules($data['params']['rules']);
				$asset = Table::getInstance('asset');

				if (!$asset->loadByName($data['option']))
				{
					$root = Table::getInstance('asset');
					$root->loadByName('root.1');
					$asset->name  = $data['option'];
					$asset->title = $data['option'];
					$asset->setLocation($root->id, 'last-child');
				}
				$asset->rules = (string) $rules;

				if (!$asset->check() || !$asset->store())
				{
					$this->setError($asset->getError());

					return false;
				}
				// We don't need this anymore
				unset($data['option']);
				unset($data['params']['rules']);
			}

			// Clean the cache.
			$cache = Factory::getCache('com_config');
			$cache->clean();

			return true;

		}

	}

}
