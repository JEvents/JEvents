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

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('radio');

/**
 * JEVMenu Field class for the JEvents Component
 *
 * @package		JEvents.fields
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldJEVBoolean extends JFormFieldRadio
{
	/**
	 * The form field type.s
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'JEVBoolean';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{		
		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);
		
		$options = array ();
		$options[] = JHTML::_('select.option', 0, JText::_("Jev_No"));
		$options[] = JHTML::_('select.option', 1, JText::_("jev_Yes"));

		return $options;

		
	}
}
