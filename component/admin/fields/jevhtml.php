<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1331 2010-10-19 12:35:49Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

include_once(JPATH_SITE."/libraries/joomla/form/fields/editor.php");
jimport('joomla.html.editor');

class JFormFieldJevhtml extends JFormFieldEditor
{
	protected function getInput()
	{
		$this->value = str_replace('<br />', "\n", JText::_($this->value));
		return parent::getInput();
	}
	
}