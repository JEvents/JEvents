<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevextras.php 1785 2011-03-14 14:28:17Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filesystem\Path;

jimport('joomla.filesystem.folder');
jimport('joomla.html.html');
jimport('joomla.form.formfield');

FormHelper::loadFieldClass('text');

include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");

#[\AllowDynamicProperties]
class JFormFieldJevfilters extends JFormFieldText
{

	protected
		$type = 'JEVFilters';

	function __construct($form = null)
	{

		// Must load admin language files
		$lang = Factory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		parent::__construct($form);

	}

	protected
	function getInput()
	{

		// Mkae sure jQuery is loaded
		HTMLHelper::_('jquery.framework');
		HTMLHelper::script('media/com_jevents/js/Sortable.js', array('version' => JeventsHelper::JEvents_Version(false), 'relative' => false));

		jimport('joomla.filesystem.folder');

		//$invalue = str_replace(" ", "", $this->value);
        $invalue = $this->value;
		$invalue = explode(",", $invalue);

		$pluginsDir = JPATH_ROOT . '/plugins/jevents';
		$filterpath = $pluginsDir . "/filters";

		$this->filterpath = array();
		if (Folder::exists($filterpath))
		{
			$this->filterpath[] = $filterpath;
		}

		$this->filterpath[] = JPATH_SITE . "/components/com_jevents/libraries/filters";

		if (Folder::exists(JPATH_SITE . "/plugins/jevents"))
		{
			$others = Folder::folders(JPATH_SITE . "/plugins/jevents", 'filters', true, true);
			if (is_array($others))
			{
				$this->filterpath = array_merge($this->filterpath, $others);
			}
		}

		$filters = array();
		include_once(JPATH_SITE . "/components/com_jevents/libraries/filters.php");
		foreach ($this->filterpath as $path)
		{
			foreach (Folder::files($path, ".php") as $filtername)
			{
				if (!array_key_exists($filtername, $filters))
				{
					if (strpos($filtername, "-") > 0 || strpos($filtername, ".zip") > 0 || strpos($filtername, ".php") != StringHelper::strlen($filtername) - 4)
						continue;
					$filterpath = $path . "/" . $filtername;
					$filtername = StringHelper::substr($filtername, 0, StringHelper::strlen($filtername) - 4);
					// skip special function filters
					if ($filtername == "startdate" || $filtername == "Startdate")
						continue;
					$filter = "jev" . ucfirst($filtername) . "Filter";
					if (!class_exists($filter))
					{
						try
						{
							include_once($filterpath);
						}
						catch (Exception $e)
						{
							continue;
						}
					}
					if (!class_exists($filter))
					{
						continue;
					}
                    if ($filtername == "Customfield")
                    {
                        $jevcustomfields = PluginHelper::getPlugin("jevents", 'jevcustomfields');
                        JLoader::register('jevFilterProcessing', JEV_PATH . "/libraries/filters.php");
                        JLoader::register('plgJEventsJevcustomfields', JPATH_ROOT . '/plugins/jevents/jevcustomfields/jevcustomfields.php');

                        $customFilters = jevFilterProcessing::getInstance(array('Customfield'), JPATH_ROOT . '/plugins/jevents/filters/');

                        $filterFile = 'Customfield.php';

                        $filterFilePath = Path::find(JPATH_ROOT . '/plugins/jevents/jevcustomfields/filters/', $filterFile);

                        if ($filterFilePath)
                        {
                            include_once($filterFilePath);
                        }
                        else
                        {
                            echo "Missing filter file $filterFile<br/>";
                            continue;
                        }

                        $theFilter       = new jevCustomfieldfilter("", $filtername);
                        $filterHTML = $theFilter->_createfilterHTML(true);
                        foreach ($filterHTML['merge'] as $key => $cffilter)
                        {
                            $filters[$filtername . ":" . str_replace("_", " ", $key)] = $path . "/" . $filter . ":" . $key;
                        }
                    }
                    else
                    {
                        $filters[$filtername] = $path . "/" . $filter;
                    }
				}
			}
		}

        $validvalues = array();
		$input       = '<div style="clear:left"></div><table><tr valign="top">
			<td><div style="font-weight:bold">' . Text::_("JEV_CLICK_TO_ADD_FILTER") . '</div>
			<div id="filterchoices" style="width:300px;margin-top:10px;height:100px;border:solid 1px #ccc;overflow-y:auto" >';
		foreach ($filters as $filter => $filterpath)
		{
			if (!in_array($filter, $invalue) && !in_array(strtolower($filter), $invalue))
			{
				$input          .= '<div>' . $filter . "<span style='display:none'>$filter</span></div>";
				$validvalues [] = $filter;
			}
		}
		$validvalue = implode(",", $validvalues);
		$input      .= '</div></td>
		<td><div  style="font-weight:bold">' . Text::_("JEV_FILTER_CLICK_TO_REMOVE") . '</div>
			<div id="filtermatches" style="margin:10px 0px 0px 10px;">';
		$invalues   = array();
		foreach ($invalue as $filter)
		{
			if (array_key_exists($filter, $filters) || array_key_exists(ucfirst($filter), $filters))
			{
				$filter     = ucfirst($filter);
				$input      .= '<div id="filter' . $filter . '">' . $filter . "</div>";
				$invalues[] = $filter;
			}
		}
		$invalues = implode(",", $invalues);

		$input .= '</div></td>
			</tr></table>';

		// Include jQuery
		HTMLHelper::_('jquery.framework');

		JEVHelper::script('modules/mod_jevents_filter/fields/filterSelect.js');

		// Initialize some field attributes.
		$size      = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $size      = "";
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class     = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly  = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled  = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return $input . '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';

	}

}
