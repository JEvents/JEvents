<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevextras.php 1785 2011-03-14 14:28:17Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

use Joomla\String\StringHelper;


jimport('joomla.filesystem.folder');
jimport('joomla.html.html');
jimport('joomla.form.formfield');

include_once(JPATH_SITE . '/libraries/joomla/form/fields/text.php');
include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");

class JFormFieldJevfilters extends JFormFieldText
{

	protected
			$type = 'JEVFilters';

	function __construct($form = null)
	{
		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		parent::__construct($form);

	}

	protected
			function getInput()
	{
            
		// Mkae sure jQuery is loaded
                JHtml::_('jquery.framework');
                JHtml::_('jquery.ui', array("core","sortable"));
            
		jimport('joomla.filesystem.folder');

		$invalue = str_replace(" ","",$this->value);
		$invalue = explode(",",$invalue);

		$pluginsDir = JPATH_ROOT . '/plugins/jevents';
		$filterpath = $pluginsDir . "/filters";

		$this->filterpath = array();
		if (JFolder::exists($filterpath)){
			$this->filterpath[] = $filterpath;
		}

		$this->filterpath[] = JPATH_SITE . "/components/com_jevents/libraries/filters";

		if (JFolder::exists(JPATH_SITE . "/plugins/jevents"))
		{
			$others = JFolder::folders(JPATH_SITE . "/plugins/jevents", 'filters', true, true);
			if (is_array($others))
			{
				$this->filterpath = array_merge($this->filterpath, $others);
			}
		}

		$filters = array();
		include_once(JPATH_SITE . "/components/com_jevents/libraries/filters.php");
		foreach ($this->filterpath as $path)
		{
			foreach (JFolder::files($path, ".php") as $filtername)
			{
				if (!array_key_exists($filtername, $filters))
				{
					if (strpos($filtername, "-") > 0 || strpos($filtername, ".zip") > 0 || strpos($filtername, ".php") != JString::strlen($filtername) - 4)
						continue;
					$filterpath = $path."/".$filtername;
					$filtername = JString::substr($filtername, 0, JString::strlen($filtername) - 4);
					// skip special function filters
					if ($filtername=="startdate" || $filtername=="Startdate")
						continue;
					$filter = "jev" . ucfirst($filtername) . "Filter";
					if (!class_exists($filter))
					{
						include_once($filterpath);
					}
					if (!class_exists($filter))
					{
						continue;
					}
					$filters[$filtername] = $path . "/" . $filter;
				}
			}
		}

		$validvalues = array();
		$input = '<div style="clear:left"></div><table><tr valign="top">
			<td><div style="font-weight:bold">' . JText::_("JEV_CLICK_TO_ADD_FILTER") . '</div>
			<div id="filterchoices" style="width:150px;margin-top:10px;height:100px;;border:solid 1px #ccc;overflow-y:auto" >';
		foreach ($filters as $filter => $filterpath)
		{
			if (!in_array($filter, $invalue) &&  !in_array(strtolower($filter), $invalue))
			{
				$input.='<div>' . $filter . "<span style='display:none'>$filter</span></div>";
				$validvalues [] = $filter;
			}
		}
		$validvalue = implode(",", $validvalues);
		$input .= '</div></td>
		<td><div  style="font-weight:bold">' . JText::_("JEV_FILTER_CLICK_TO_REMOVE") . '</div>
			<div id="filtermatches" style="margin:10px 0px 0px 10px;">';
		$invalues = array();
		foreach ($invalue as  $filter)
		{
			if (array_key_exists($filter, $filters) || array_key_exists(ucfirst($filter), $filters) )
			{
				$filter = ucfirst($filter);
				$input.='<div id="filter' . $filter. '">' . $filter . "</div>";
				$invalues[] = $filter;
			}
		}
		$invalues = implode(",", $invalues);

		$input .= '</div></td>
			</tr></table>';

		// Include jQuery
		JHtml::_('jquery.framework');

		JEVHelper::script('modules/mod_jevents_filter/fields/filterSelect.js' );
		
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return $input. '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';

	}

}
