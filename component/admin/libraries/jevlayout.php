<?php
/**
 * JEvents Component for Joomla 1.6.x
 *
 * Installer adapter for jevents layouts
 * 
 * @version     $Id: jevlayout_1.6.php 2821 2011-10-17 07:56:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.base.adapterinstance');

/**
 * JevLayout installer
 *
 */
class JInstallerJevlayout extends JAdapterInstance
{

	protected $manifest = null;
	

	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function install()
	{
		// Get a database connector object
		$db =& $this->parent->getDbo();

		// Get the extension manifest object
		$this->manifest = $this->parent->getManifest();

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the layout name
		$name =(string) $this->manifest->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->set('name', $name);

		// Get the component description
		$description = (string) $this->manifest->description;
		if ($description) {
			$this->parent->set('message', JText::_($description));
		}
		else {
			$this->parent->set('message', '');
		}

		$basePath = JPATH_SITE;

		// Do the component layout first
		$this->parent->setPath('extension_root', $basePath.'/components/com_jevents/views');

		// Copy all necessary files
		$element =& $this->manifest->componentfiles;
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// copy manifest file for versioning information
		$layout = $element->folder->data();
		$path['src'] = $this->parent->getPath('manifest');
		$path['dest'] =$this->parent->getPath('extension_root').'/'.$layout.'/'.basename($path['src'] );
		$this->parent->copyFiles(array ($path), true);
				
		// Now do the modules in turn
		$element =& $this->manifest->modulefiles;

		// Find modules to copy
		foreach ($element->children() as $child)
		{
			if ($child) {
				$modulename = $child->name();

				$this->parent->setPath('extension_root', $basePath.'/modules/'.$modulename."/tmpl");

				// If the layout directory does not exist, lets skip since this module is not installed
				if (!file_exists($this->parent->getPath('extension_root'))) {
					continue;
				}

				// Copy all necessary files
				$modelement =& $element->$modulename;
				if ($this->parent->parseFiles($modelement, -1) === false) {
					// Install failed, roll back changes
					$this->parent->abort();
					return false;
				}
			}
		}



		// Load the language file
		$lang		= JFactory::getLanguage();
		$lang->load("jevlayout",$basePath);

		// We don't copy the manifest file since there is no uninstall mechanism

		return true;
	}

}
