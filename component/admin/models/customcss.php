<?php
/**
 * @package     JEvents
 * @subpackage  com_jjevents
 *
 * @copyright   Copyright (C) 2017 - 2017 GWE Systems Ltd. All rights reserved.
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

class CustomcssModelCustomcss extends JModelForm
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

		//Define a check for both locations
		if (JFile::exists(JEVHelper::CustomCSSFile())) {
			$new_filePath = JPath::check(JEVHelper::CustomCSSFile());
		} else {
			$new_filePath = JPath::check(JEVHelper::CustomCSSFile() . '.new');
		}

		try
		{
			$filePath = $new_filePath;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_('COM_JEVENTS_CUSTOM_CSS_SOURCE_NOT_FOUND'), 'error');
			return;
		}

		//We know the file already exists as we try/catch above. Load it in.
		$item->filename = 'jevcustom.css';
		$item->source = file_get_contents($filePath);

		return $item;
	}

	public function save($data)
	{
		jimport('joomla.filesystem.file');
		$app = JFactory::getApplication();

		$fileName = 'jevcustom.css';
		$filepath       = JPATH_ROOT . '/components/com_jevents/assets/css/' . $fileName;
		$srcfilepath    = $filepath . '.new';

		if (!JFile::exists($filepath))
		{
			//Create the new file so we have a base file to save to
			Jfile::copy($srcfilepath, $filepath);
		}

		$filePath = JPath::clean($filepath);

		$user = get_current_user();
		chown($filePath, $user);
		JPath::setPermissions($filePath, '0644');

		// Try to make the template file writable.
		if (!is_writable($filePath))
		{
			$app->enqueueMessage(JText::_('COM_JEVENTS_CUSTOM_CSS_FILE_NOT_WRITEABLE'), 'warning');
			$app->enqueueMessage(JText::sprintf('COM_JEVENTS_CUSTOM_CSS_FILE_NOT_WRITEABLE_PERMISSIONS_ISSUE', JPath::getPermissions($filePath)), 'warning');

			if (!JPath::isOwner($filePath))
			{
				$app->enqueueMessage(JText::spritf('COM_JEVENTS_CUSTOM_CSS_FILE_CHECK_OVWNERSHIP', $filePath), 'warning');
			}

			return false;
		}

		// Make sure EOL is Unix
		$data['source'] = str_replace(array("\r\n", "\r"), "\n", $data['source']);

		$return = JFile::write($filePath, $data['source']);

		if (!$return)
		{
			$app->enqueueMessage(JText::sprintf('COM_JEVENTS_CUSTOM_CSS_FILE_FAILED_TO_SAVE', $fileName), 'error');

			return false;
		}

		// Get the extension of the changed file. - May use later with a compiler.
		$explodeArray = explode('.', $fileName);
		$ext = end($explodeArray);

		return true;
	}
}