<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevcategory.php 1987 2011-04-28 09:53:46Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Form\Field\ListField;

jimport('joomla.form.formfield');

class JFormFieldJevmultilist extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Jevmultilist';

	protected function getInput()
	{

		if (is_string($this->value))
		{
			$this->value = explode(",", $this->value);
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}


}
