<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevcategory.php 1987 2011-04-28 09:53:46Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');

class JFormFieldJeveditionrequiredfields extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Jeveditionrequiredfields';

	protected function getInput()
	{
		parent::getOptions();
                                    
		$availableFields = array();

		$jevplugins = JPluginHelper::getPlugin("jevents");
		//we dinamically get the size of the select box
		$size = 5;
		//$options['CATEGORY'] =  JText::_("JEV_FIELD_CATEGORY",true);
		// title is always required
		//$options['TITLE'] = JText::_("JEV_FIELD_TITLE",true);
		$options['DESCRIPTION'] = JText::_("JEV_FIELD_DESCRIPTION",true);
		$options['LOCN'] = JText::_("JEV_FIELD_LOCATION",true);
		$options['CONTACT'] = JText::_("JEV_FIELD_CONTACT",true);
		$options['EXTRA'] = JText::_("JEV_FIELD_EXTRAINFO",true);
		$group = array();
		$group['value'] =JText::_("JEV_CORE_DATA",true);
		$group['text'] =JText::_("JEV_CORE_DATA",true);

		$group['items'] = $options;
		$optionsGroup[]=$group;
		unset($options);

		foreach ($jevplugins as $jevplugin)
		{
			// At present we only support JEvents, Agenda & Minutes, CCK plugin, Standard Images and Files, Resources Manager, Metatags and Tags
			if (!in_array($jevplugin->name, array("agendaminutes", "jevcck","jevfiles", "jevmetatags", "jevpeople" , "jevtags"))) continue;

			$classname = "plgJevents".ucfirst($jevplugin->name);
			if (is_callable(array($classname,"fieldNameArray")))
			{
				$lang = JFactory::getLanguage();
				$lang->load("plg_jevents_".$jevplugin->name,JPATH_ADMINISTRATOR);
				$fieldNameArray = call_user_func(array($classname,"fieldNameArray"), "edit");
				if (!isset($fieldNameArray['labels'])) continue;
				$fieldNameArrayCount = count($fieldNameArray['labels']);
				if($fieldNameArrayCount>0)
				{
					$size +=  $fieldNameArrayCount;
					for ($i=0;$i<$fieldNameArrayCount;$i++)
					{
						if ($fieldNameArray['labels'][$i]=="" || $fieldNameArray['labels'][$i]==" Label")  continue;
						if ($fieldNameArray['values'][$i]=='people_selfallocation')  continue;
						$options[$fieldNameArray['values'][$i]]=$fieldNameArray['labels'][$i];
						$availableFields[$jevplugin->name][]= JHtml::_('select.option',$fieldNameArray['values'][$i],$fieldNameArray['labels'][$i]);
					}
					$group = array();
					$group['value'] = $fieldNameArray['group'];
					$group['text']  = $fieldNameArray['group'];
					$group['items'] = $options;
					$optionsGroup[]=$group;
					unset($options);
				}
			}
		}
		if (!empty($optionsGroup))
		{
			$size = ($size<10)?$size:10;
			$attr = array('list.attr' => 'multiple="true"'.'size="'.$size.'"',
						'list.select' => $this->value, 'id' => $this->id);
     
			$input = JHTML::_('select.groupedlist', $optionsGroup, $this->name,$attr);
		}

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return $input;
	}
}
