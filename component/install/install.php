<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * ---------------------------------------------------------------------------------------------
 * Finalization and Cleanup Section
 * ---------------------------------------------------------------------------------------------
 */

// Lastly, we will copy the manifest file to its appropriate place.
if (!$this->parent->copyManifest()) {
	// Install failed, rollback changes
	$this->parent->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
	return false;
}

// Redirect to JEvents CPanel
global $mainframe;
$mainframe->redirect(JURI::root()."administrator/index.php?option=com_jevents");