<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * JEvents Custom CSS overview Model
 *
 * @since  3.4.29
 */

class CustomcssModelCustomCss extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		// Codemirror or Editor None should be enabled
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__extensions as a')
			->where(
				'(a.name =' . $db->quote('plg_editors_codemirror') .
				' AND a.enabled = 1) OR (a.name =' .
				$db->quote('plg_editors_none') .
				' AND a.enabled = 1)'
			);
		$db->setQuery($query);
		$state = $db->loadResult();

		if ((int) $state < 1)
		{
			$app->enqueueMessage(JText::_('COM_TEMPLATES_ERROR_EDITOR_DISABLED'), 'warning');
		}

		// Get the form.
		$form = $this->loadForm('com_jevents.customcss', 'customcss', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = $this->getSource();

		$this->preprocessData('com_jevents.customcss', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function &getSource()
	{
		$app = JFactory::getApplication();
		$item = new stdClass;

		try
		{
			$filePath = JPath::check(JPATH_ROOT . '/components/com_jevents/assets/css/jevcustom.css');
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_FOUND'), 'error');
			return;
		}

		if (file_exists($filePath))
		{
			$item->extension_id = $this->getState('extension.id');
			$item->filename = 'jevcustom.css';
			$item->source = file_get_contents($filePath);
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_FOUND'), 'error');
		}


		return $item;
	}
}