<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * Installer adapter for jevents layouts
 * 
 * @version     $Id: jevlayout.php 2706 2011-10-06 13:15:29Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * JevLayout installer
 *
 */
class JInstallerJevlayout extends JObject
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [JInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct(&$parent)
	{
		$this->parent =& $parent;
	}

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
		$db =& $this->parent->getDBO();

		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =& $manifest->document;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the layout name
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$this->set('name', $name);

		// Get the component description
		$description = & $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}

		$basePath = JPATH_SITE;

		// Do the component layout first
		$this->parent->setPath('extension_root', $basePath.'/components/com_jevents/views');

		// Copy all necessary files
		$element =& $this->manifest->getElementByPath('componentfiles');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}
			
		// copy manifest file for versioning information
		$layout = $element->getElementByPath('folder')->data();
		$path['src'] = $this->parent->getPath('manifest');
		$path['dest'] =$this->parent->getPath('extension_root').DS.$layout.DS.basename($path['src'] );
		$this->parent->copyFiles(array ($path), true);
		
		// Now do the modules in turn
		$element =& $this->manifest->getElementByPath('modulefiles');

		// Find modules to copy
		foreach ($element->children() as $child)
		{
			if (is_a($child, 'JSimpleXMLElement')) {
				$modulename = $child->name();

				$this->parent->setPath('extension_root', $basePath.'/modules/'.$modulename."/tmpl");

				// If the layout directory does not exist, lets skip since this module is not installed
				if (!file_exists($this->parent->getPath('extension_root'))) {
					continue;
				}

				// Copy all necessary files
				$modelement =& $element->getElementByPath($modulename);
				if ($this->parent->parseFiles($modelement, -1) === false) {
					// Install failed, roll back changes
					$this->parent->abort();
					return false;
				}
			}
		}



		// Load the language file
		$lang		= & JFactory::getLanguage();
		$lang->load("jevlayout",$basePath);

		// We don't copy the manifest file since there is no uninstall mechanism

		return true;
	}

}
