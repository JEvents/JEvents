<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevtimezone.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJevtimezone extends JFormField
{

	protected $type = 'Jevtimezone';

	protected function getInput()
	{
		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		if (class_exists("DateTimeZone"))
		{
                        $params = JComponentHelper::getParams("com_jevents");
                        $choosefrom =  $params->get("offeredtimezones", array());
                        //? explode(",",$this->getAttribute("choosefrom", "")) : array();

                        if (!is_array($choosefrom) || (count($choosefrom)==1 && $choosefrom[0]=="")) {
                            $choosefrom = array();
                        }
			$zones = DateTimeZone::listIdentifiers();
			static $options;
			if (!isset($options))
			{
				$options = array();
				$options[] = JHTML::_('select.option', '', '- ' . JText::_('SELECT_TIMEZONE') . ' -');
				foreach ($zones as $zone)
				{
					if (strpos($zone, "/") === false && strpos($zone, "UTC") === false)
						continue;
					if (strpos($zone, "Etc") === 0)
						continue;
                                        if (count($choosefrom) && !in_array($zone,$choosefrom)){
						continue;                                            
                                        }
					$options[] = JHTML::_('select.option', $zone, $zone);
				}
			}
			$attr = array('list.attr' => 'class="'.$this->class.'" ',
                                        'list.select' => $this->value, 
                                        'option.key' => 'value',
                                        'option.text' => 'text',
                                        'id' => $this->id
                                );                                               
     
            		$attr["list.attr"] .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
                        $attr["list.attr"] .= !empty($this->onchange) ? ' onchange="' . $this->onchange. '"' : '';
                        $attr["list.attr"] .= $this->getAttribute("style", false) ?  "style='".$this->getAttribute("style")."'" : '';
                	$attr["list.attr"] .= $this->multiple ? ' multiple="multiple" ' : '';
                        if (($this->value=="" || $this->value==-1)  && $this->multiple){
                            unset($attr["list.select"]);
                        }
                        
			//$input = JHTML::_('select.groupedlist', $optionsGroup, $this->name,$attr);

			return JHTML::_('select.genericlist', $options, $this->name, $attr); //'class="inputbox"', 'value', 'text', $this->value, $this->id);
		}
		else
		{
			/*
			 * Required to avoid a cycle of encoding &
			 * html_entity_decode was used in place of htmlspecialchars_decode because
			 * htmlspecialchars_decode is not compatible with PHP 4
			 */
			$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

			return '<input type="text" name="' . $this->name . '" id="' . $this->id. '" value="' . $this->value . '" />';
		}

	}

}