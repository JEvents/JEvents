<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevcategory.php 1196 2010-09-27 08:26:32Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */


defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * JevCategory Field class for the JEvents Component
 *
 */
class JFormFieldJEvCategory extends JFormFieldList
{

	protected $type = 'JEvCategory';

	public function getOptions()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_jevlocations/elements/jevcategory.php';
		if (file_exists($file) ) {
			include_once($file);
		} else {
			die ("JEvents Locations Fields\n<br />This module needs the JEvents Locations component");
		}		

		$results = JElementJevcategory::fetchElement($this->name, $this->value, $this->element, $this->type, true);  // RSH 10/4/10 - Use the original code for J!1.6
		
		$options = array(); //new stdClass();
		$i = 0;
		foreach ($results AS $result)
		{
			$options[$i] = new stdClass; //
			$options[$i]->value = $result->id;
			$options[$i]->text = $result->ctitle;
			$i++;
		}
		
		array_unshift($options, JHTML::_('select.option', '0', '- ' . JText::_('Select Category') . ' -'));
		
		return $options;
	}
}
