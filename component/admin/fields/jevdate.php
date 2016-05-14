<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevdate.php 1300 2010-10-13 14:49:16Z royceharding $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('calendar');

class JFormFieldJEVDate extends JFormFieldCalendar
{
	public $type = 'JEVDate';  // must be public as per JFormFieldCalendar!! 
	
	public function getInput()
	{
		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());
		parent::getInput();
	}

}
