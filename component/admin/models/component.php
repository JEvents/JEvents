<?php
/**
 * @package        Joomla.Administrator
 * @subpackage     com_config
 * @copyright      Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');

use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;

/**
 * @package        Joomla.Administrator
 * @subpackage     com_config
 */
class AdminparamsModelComponent extends FormModel
{
	/**
	 * Method to get a form object.
	 *
	 * @param    array   $data     Data for the form.
	 * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A Form object on success, false on failure
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{

		if ($path = $this->getState('component.path'))
		{
			// Add the search path for the admin component config.xml file.
			Form::addFormPath($path);
		}
		else
		{
			// Add the search path for the admin component config.xml file.
			Form::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $this->getState('component.option'));
		}

		// Get the form.
		$form = $this->loadForm(
			'com_config.component',
			'config',
			array('control' => 'jform', 'load_data' => $loadData),
			false,
			'/config'
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Get the component information.
	 *
	 * @return    object
	 * @since    1.6
	 */
	function getComponent()
	{

		// Initialise variables.
		$option = $this->getState('component.option');

		// Load common and local language files.
		$lang = Factory::getLanguage();
		$lang->load($option, JPATH_BASE, null, false, false)
		|| $lang->load($option, JPATH_BASE . "/components/$option", null, false, false)
		|| $lang->load($option, JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load($option, JPATH_BASE . "/components/$option", $lang->getDefault(), false, false);

		$result = ComponentHelper::getComponent($option);

		return $result;
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

		// Clean the component cache.
		$this->cleanCache('_system');

		return true;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return    void
	 * @since    1.6
	 */
	protected function populateState()
	{

		// Set the component (option) we are dealing with.
		$input    = Factory::getApplication()->input;
		$component = $input->getCmd('option');
		$this->setState('component.option', $component);

		// Set an alternative path for the configuration file.
		if ($path = $input->getString('path'))
		{
			$path = Path::clean(JPATH_SITE . '/' . $path);
			Path::check($path);
			$this->setState('component.path', $path);
		}
	}
}
