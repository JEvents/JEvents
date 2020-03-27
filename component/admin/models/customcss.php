<?php
/**
 * @package     JEvents
 * @subpackage  com_jjevents
 *
 * @copyright   Copyright (C) 2017 - -JEVENTS_COPYRIGHT GWESystems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * JEvents Custom CSS overview Model
 *
 * @since  3.4.29
 */
class CustomcssModelCustomcss extends FormModel
{
	public function getForm($data = array(), $loadData = true)
	{

		$app = Factory::getApplication();

		// Codemirror or Editor None should be enabled
		$db    = $this->getDbo();
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
			$app->enqueueMessage(Text::_('COM_TEMPLATES_ERROR_EDITOR_DISABLED'), 'warning');
		}

		// Get the form.
		$form = $this->loadForm('com_jevents.customcss', 'customcss', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function save($data)
	{

		jimport('joomla.filesystem.file');
		$app = Factory::getApplication();

		$fileName    = 'jevcustom.css';
		$filepath    = JPATH_ROOT . '/components/com_jevents/assets/css/' . $fileName;
		$srcfilepath = $filepath . '.new';

		if (!File::exists($filepath))
		{
			//Create the new file so we have a base file to save to
			Jfile::copy($srcfilepath, $filepath);
		}

		$filePath = Path::clean($filepath);

		$user = get_current_user();
		chown($filePath, $user);
		Path::setPermissions($filePath, '0644');

		// Try to make the template file writable.
		if (!is_writable($filePath))
		{
			$app->enqueueMessage(Text::_('COM_JEVENTS_CUSTOM_CSS_FILE_NOT_WRITEABLE'), 'warning');
			$app->enqueueMessage(Text::sprintf('COM_JEVENTS_CUSTOM_CSS_FILE_NOT_WRITEABLE_PERMISSIONS_ISSUE', Path::getPermissions($filePath)), 'warning');

			if (!Path::isOwner($filePath))
			{
				$app->enqueueMessage(Text::spritf('COM_JEVENTS_CUSTOM_CSS_FILE_CHECK_OVWNERSHIP', $filePath), 'warning');
			}

			return false;
		}

		// Make sure EOL is Unix
		$data['source'] = str_replace(array("\r\n", "\r"), "\n", $data['source']);

		$return = File::write($filePath, $data['source']);

		if (!$return)
		{
			$app->enqueueMessage(Text::sprintf('COM_JEVENTS_CUSTOM_CSS_FILE_FAILED_TO_SAVE', $fileName), 'error');

			return false;
		}

		// Get the extension of the changed file. - May use later with a compiler.
		$explodeArray = explode('.', $fileName);
		$ext          = end($explodeArray);

		return true;
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

		$app  = Factory::getApplication();
		$item = new stdClass;

		//Define a check for both locations
		if (File::exists(JEVHelper::CustomCSSFile()))
		{
			$new_filePath = Path::check(JEVHelper::CustomCSSFile());
		}
		else
		{
			$new_filePath = Path::check(JEVHelper::CustomCSSFile() . '.new');
		}

		try
		{
			$filePath = $new_filePath;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_('COM_JEVENTS_CUSTOM_CSS_SOURCE_NOT_FOUND'), 'error');

			return;
		}

		//We know the file already exists as we try/catch above. Load it in.
		$item->filename = 'jevcustom.css';
		$item->source   = file_get_contents($filePath);

		return $item;
	}
}
