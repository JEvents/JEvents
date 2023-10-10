<?php

/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevinfo.php 1331 2010-10-19 12:35:49Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('spacer');

// Must load admin language files
$lang = Factory::getLanguage();
$lang->load("com_jevents", JPATH_ADMINISTRATOR);

/**
 * JEVMenu Field class for the JEvents Component
 *
 * @package        JEvents.fields
 * @subpackage     com_banners
 * @since          1.6
 */
class JFormFieldJEVEditlayout extends JFormFieldSpacer
{

	/**
	 * The form field type.s
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected
		$type = 'JEVEditlayout';

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	public
	function getInput()
	{

		$input = Factory::getApplication()->input;
		$id = $input->getInt('id', 0);

		$node         = $this->element;
		if ($id)
		{
			//$value    = "<a href='index.php?option=com_jevents&task=defaults.edit&type=module.latest_event&modid=$id' target='_blank' ><span class='icon-pencil' ></span></a>";
            $value    = "<button type='button' class='btn btn-primary' onclick=\"window.open('index.php?option=com_jevents&task=defaults.edit&type=module.latest_event&modid=$id');return false;\" ><span class='icon-pencil' ></span></button>";
			return $value;
		}
		else
		{
			$alttext = (string) $this->getAttribute('alttext');
			return Text::_($alttext);
		}

	}


}
