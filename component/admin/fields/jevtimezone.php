<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevtimezone.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJevtimezone extends JFormField
{

	protected $type = 'Jevtimezone';

	protected function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		if (class_exists("DateTimeZone"))
		{
			$params     = ComponentHelper::getParams("com_jevents");
			$choosefrom = $params->get("offeredtimezones", array());
			//? explode(",",$this->getAttribute("choosefrom", "")) : array();

			if (!is_array($choosefrom) || (count($choosefrom) == 1 && $choosefrom[0] == "") || Factory::getApplication()->input->getCmd("task") == "params.edit")
			{
				$choosefrom = array();
			}
			$zones = DateTimeZone::listIdentifiers();
			static $options;
			if (!isset($options))
			{
				$options   = array();
				$options[] = HTMLHelper::_('select.option', '', '- ' . JText::_('SELECT_TIMEZONE') . ' -');
				foreach ($zones as $zone)
				{
					if (strpos($zone, "/") === false && strpos($zone, "UTC") === false)
						continue;
					if (strpos($zone, "Etc") === 0)
						continue;
					if (count($choosefrom) && !in_array($zone, $choosefrom))
					{
						continue;
					}
					$zonevalue      = $zone;
					$translatezone  = str_replace("/", "_", $zone);
					$translatedzone = JText::_($translatezone);
					if ($translatezone != $translatedzone)
					{
						$zone = $translatedzone;
					}
					$options[] = HTMLHelper::_('select.option', $zonevalue, $zone);
				}
			}
			$attr = array('list.attr'   => 'class="' . $this->class . '" ',
			              'list.select' => $this->value,
			              'option.key'  => 'value',
			              'option.text' => 'text',
			              'id'          => $this->id
			);

			$attr["list.attr"] .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
			$attr["list.attr"] .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
			$attr["list.attr"] .= $this->getAttribute("style", false) ? "style='" . $this->getAttribute("style") . "'" : '';
			$attr["list.attr"] .= $this->multiple ? ' multiple="multiple" ' : '';
			if (($this->value == "" || $this->value == -1) && $this->multiple)
			{
				unset($attr["list.select"]);
			}

			//$input = HTMLHelper::_('select.groupedlist', $optionsGroup, $this->name,$attr);

			return HTMLHelper::_('select.genericlist', $options, $this->name, $attr); //'class="inputbox"', 'value', 'text', $this->value, $this->id);
		}
		else
		{
			/*
			 * Required to avoid a cycle of encoding &
			 * html_entity_decode was used in place of htmlspecialchars_decode because
			 * htmlspecialchars_decode is not compatible with PHP 4
			 */

			$value = htmlspecialchars(html_entity_decode($this->value, ENT_QUOTES), ENT_QUOTES);

			return '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" />';
		}

	}

}