<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevdate.php 1300 2010-10-13 14:49:16Z royceharding $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\CalendarField;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJEVDate extends CalendarField
{
	public $type = 'JEVDate';  // must be public as per CalendarField!!

	public function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());
		parent::getInput();
	}

}
