<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1331 2010-10-19 12:35:49Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('radio');

/**
 * JEVMenu Field class for the JEvents Component
 *
 * @package        JEvents.fields
 * @subpackage     com_banners
 * @since          1.6
 */
class JFormFieldJevBoolmsg extends JFormFieldRadio
{
	/**
	 * The form field type.s
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'JevBoolmsg';

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	public function getOptions()
	{

		// Must load admin language files
		$lang = Factory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$options   = array();
		$options[] = HTMLHelper::_('select.option', 0, Text::_("Jev_No"));
		$options[] = HTMLHelper::_('select.option', 1, Text::_("jev_Yes"));

		return $options;


	}

	protected function getInput()
	{

		$style = ' class="jev_none" ';
		// if not showing copyright show message
		if (!$this->value)
		{
			$style = ' class="jev_block" ';
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput() . '<div id="jevcopymsg" ' . $style . '>' . Text::_("Before removing the copyright footer please read this important message at the <a href='https://www.jevents.net/hidecopyright?tmpl=component' title='get hide copyright code' class='modal' rel='{handler: \"iframe\", size: {x: 650, y: 450}}'>JEvents website</a>.") . '</div>';
	}
}
