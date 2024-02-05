<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevcategorynew.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.form.helper');
FormHelper::loadFieldClass('text');

class JFormFieldJevcolumns extends JFormFieldText
{

	protected
		$type = 'Jevcolumns';

	protected
	function getInput()
	{

		// Must also load frontend language files
		$lang = Factory::getLanguage();
		$lang->load(JEV_COM_COMPONENT, JPATH_SITE);

		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		// Mkae sure jQuery is loaded
		HTMLHelper::_('jquery.framework');
		HTMLHelper::script('media/com_jevents/js/Sortable.js', array('version' => JeventsHelper::JEvents_Version(false), 'relative' => false));
		HTMLHelper::_('bootstrap.framework');
		JEVHelper::script("jQnc.js", "components/com_jevents/assets/js/");

		JEVHelper::script("columns.js", "administrator/components/com_jevents/assets/js/");

		$user = Factory::getUser();

		$collist   = array();
		$collist[] = array(Text::_("JEV_CORE_DATA", true), "disabled");

		$collist[] = array(Text::_("JEV_FIELD_TITLE", true), "TITLE");
		$collist[] = array(Text::_("JEV_FIELD_TITLE_LINK", true), "TITLE_LINK");
		$collist[] = array(Text::_("JEV_FIELD_REPEATSUMMARY", true), "REPEATSUMMARY");
		$collist[] = array(Text::_("JEV_FIELD_STARTDATE", true), "STARTDATE");
		$collist[] = array(Text::_("JEV_FIELD_STARTTIME", true), "STARTTIME");
		$collist[] = array(Text::_("JEV_FIELD_ISOSTARTTIME", true), "ISOSTART");
		$collist[] = array(Text::_("JEV_FIELD_ENDDATE", true), "ENDDATE");
		$collist[] = array(Text::_("JEV_FIELD_ENDTIME", true), "ENDTIME");
		$collist[] = array(Text::_("JEV_FIELD_ISOENDTIME", true), "ISOEND");
		$collist[] = array(Text::_("JEV_FIELD_MULTIENDDATE", true), "MULTIENDDATE");
		$collist[] = array(Text::_("JEV_FIRSTREPEATSTART", true), "FIRSTREPEATSTART");
		$collist[] = array(Text::_("JEV_LASTREPEATEND", true), "LASTREPEATEND");
		$collist[] = array(Text::_("JEV_FIELD_DURATION", true), "DURATION");
		$collist[] = array(Text::_("JEV_FIELD_PREVIOUSNEXT", true), "PREVIOUSNEXT");
		$collist[] = array(Text::_("JEV_FIELD_FIRSTREPEAT", true), "FIRSTREPEAT");
		$collist[] = array(Text::_("JEV_FIELD_LASTREPEAT", true), "LASTREPEAT");
		$collist[] = array(Text::_("JEV_FIELD_CREATOR_LABEL", true), "CREATOR_LABEL");
		$collist[] = array(Text::_("JEV_FIELD_CREATOR", true), "CREATOR");
		$collist[] = array(Text::_("JEV_FIELD_HITS", true), "HITS");
		$collist[] = array(Text::_("JEV_FIELD_DESCRIPTION", true), "DESCRIPTION");
		$collist[] = array(Text::_("JEV_FIELD_LOCATION_LABEL", true), "LOCATION_LABEL");
		$collist[] = array(Text::_("JEV_FIELD_LOCATION", true), "LOCATION");
		$collist[] = array(Text::_("JEV_FIELD_CONTACT_LABEL", true), "CONTACT_LABEL");
		$collist[] = array(Text::_("JEV_FIELD_CONTACT", true), "CONTACT");
		$collist[] = array(Text::_("JEV_FIELD_EXTRAINFO", true), "EXTRAINFO");
		$collist[] = array(Text::_("JEV_FIELD_CATEGORY", true), "CATEGORY");
		$collist[] = array(Text::_("JEV_FIELD_ALL_CATEGORIES", true), "ALLCATEGORIES");
		$collist[] = array(Text::_("JEV_FIELD_CATEGORY_LINK", true), "CATEGORYLNK");
		$collist[] = array(Text::_("JEV_FIELD_CATEGORY_LINK_RAW", true), "CATEGORYLNK_RAW");
		$collist[] = array(Text::_("JEV_FIELD_CATEGORY_IMAGE", true), "CATEGORYIMG");
		$collist[] = array(Text::_("JEV_FIELD_CATEGORY_IMAGES", true), "CATEGORYIMGS");
		$collist[] = array(Text::_("JEV_FIELD_CATEGORY_DESCRIPTION", true), "CATDESC");
		$collist[] = array(Text::_("JEV_FIELD_COLOUR", true), "COLOUR");
		$collist[] = array(Text::_("JEV_FIELD_CALENDAR", true), "CALENDAR");
		$collist[] = array(Text::_("JEV_FIELD_CREATIONDATE", true), "CREATED");
		$collist[] = array(Text::_("JEV_FIELD_LINK_RAW", true), "LINK");
		$collist[] = array(Text::_("JEV_FIELD_LINKSTART", true), "LINKSTART");
		$collist[] = array(Text::_("JEV_FIELD_LINKEND", true), "LINKEND");
		$collist[] = array(Text::_("JEV_FIELD_URL", true), "URL");
		$collist[] = array(Text::_("JEV_ACCESS_LEVEL", true), "ACCESS");
		$collist[] = array(Text::_("JEV_EVENT_PRIORITY", true), "PRIORITY");
		$collist[] = array(Text::_("JEV_FIELD_ICALBUTTON", true), "ICALBUTTON");
		$collist[] = array(Text::_("JEV_FIELD_TODAY",true), "TODAY");
		$collist[] = array(Text::_("JEV_FIELD_TOMORROW",true), "TOMORROW");
		$collist[] = array(Text::_("JEV_FIELD_CREATOR_NAME_BEFORE_FIRST_SPACE",true), "CREATOR_DISPLAY_AFTER_FIRST_SPACE");
		$collist[] = array(Text::_("JEV_FIELD_CREATOR_NAME_AFTER_FIRST_SPACE",true), "CREATOR_DISPLAY_BEFORE_FIRST_SPACE");
        $collist[] = array(Text::_("JEV_FIELD_RPID",true), "RPID");
        $collist[] = array(Text::_("JEV_FIELD_EVID",true), "EVID");

		// get list of enabled plugins
		$jevplugins = PluginHelper::getPlugin("jevents");
		foreach ($jevplugins as $jevplugin)
		{
			if (PluginHelper::importPlugin("jevents", $jevplugin->name))
			{
				$classname = "plgJevents" . ucfirst($jevplugin->name);
				if (is_callable(array($classname, "fieldNameArray")))
				{
					$lang = Factory::getLanguage();
					$lang->load("plg_jevents_" . $jevplugin->name, JPATH_ADMINISTRATOR);
					$fieldNameArray = call_user_func(array($classname, "fieldNameArray"), 'list');
					if (!isset($fieldNameArray['labels'])) continue;

					$collist[] = array($fieldNameArray["group"], "disabled");

					for ($i = 0; $i < count($fieldNameArray['labels']); $i++)
					{
						if ($fieldNameArray['labels'][$i] == "" || $fieldNameArray['labels'][$i] == " Label") continue;
						$collist[] = array(str_replace(":", " ", $fieldNameArray['labels'][$i]), $fieldNameArray['values'][$i]);
					}
				}
			}
		}

		$invalue       = array();
		$indexedgroups = array();
		if ($this->value != "")
		{
			$ingroups = explode("||", $this->value);
			foreach ($ingroups as $group)
			{
				$group = explode("|", $group);
				if ($group[0] == "")
				{
					continue;
				}
				$invalue[] = $group[0];

				if (count($group) < 3)
				{
					$group[2] = $group[0];
				}
				list($id, $fieldlabel, $label) = $group;
				$col                = new stdClass();
				$col->fieldlabel    = $fieldlabel;
				$col->id            = $id;
				$col->label         = $label;
				$col->raw           = implode("|", $group);
				$indexedgroups[$id] = $col;

			}
		}

		if (version_compare(JVERSION, "4", "gt"))
		{
			$input = '<div style="clear:left"></div><div style="font-weight:bold" >' . Text::_("JEV_CLICK_TO_ADD_COLUMN")
				. '</div><div id="columnchoices" style="margin-top:10px;padding:5px;min-width:45%;height:150px;border:solid 1px #cccccc;overflow-y:auto" >';
		}
		else
		{
			$input = '<div style="clear:left"></div><table><tr valign="top">
			<td><div style="font-weight:bold" >' . Text::_("JEV_CLICK_TO_ADD_COLUMN") . '</div>
			<div id="columnchoices" style="margin-top:10px;padding:5px;min-width:45%;height:150px;border:solid 1px #ccc;overflow-y:auto" >';
		}
		foreach ($collist as $col)
		{
			if (count($col) < 3)
			{
				$col[2] = $col[0];
			}
			list($fieldlabel, $id, $label) = $col;

			if (!in_array($id, $invalue))
			{
				// we can't handle parameters yet
				if (strpos($id, ":"))
				{
					continue;
				}
				if ($id == "disabled")
				{
					$input .= '<div><strong>' . $fieldlabel . "</strong></div>\n";
				}
				else
				{
					$input .= '<div>' . $fieldlabel . "<span style='display:none'>$id</span></div>\n";
				}
			}
		}
		if (version_compare(JVERSION, "4", "gt"))
		{
		$input .= '</div>
		<div  style="font-weight:bold;margin-left:20px;">' . Text::_("JEV_COLUMNS_DRAG_TO_REORDER_OR_CLICK_TO_REMOVE") . '</div>
			<div id="columnmatches" style="margin:10px 0px 0px 20px;padding-top:5px;min-width:250px;">';
		$input .= '<div id="columnmatches_heading" style="clear:left;">'
			. '<div style="width:45%;display:inline-block;font-weight:bold">' . Text::_("JEV_COLUMNS_SELECTED_FIELD_NAME") . "</div>"
			. '<div style="width:45%;display:inline-block;font-weight:bold;margin-left:20px;">' . Text::_("JEV_COLUMNS_SELECTED_FIELD_LABEL") . "</div>"
			. "</div>";
	}
	else
		{
			$input .= '</div></td>
		<td><div  style="font-weight:bold;margin-left:20px;">' . Text::_("JEV_COLUMNS_DRAG_TO_REORDER_OR_CLICK_TO_REMOVE") . '</div>
			<div id="columnmatches" style="margin:10px 0px 0px 20px;padding-top:5px;min-width:250px;">';
			$input .= '<div id="columnmatches_heading" style="clear:left;">'
				. '<div style="width:45%;display:inline-block;font-weight:bold">' . Text::_("JEV_COLUMNS_SELECTED_FIELD_NAME") . "</div>"
				. '<div style="width:45%;display:inline-block;font-weight:bold;margin-left:20px;">' . Text::_("JEV_COLUMNS_SELECTED_FIELD_LABEL") . "</div>"
				. "</div>";
		}
		$invalues = array();
		foreach ($invalue as $col)
		{
			$input      .= '<div id="column' . $col . '" style="clear:left;"><div style="width:45%;display:inline-block;" class="sortablehandle">' . $indexedgroups[$col]->fieldlabel . "</div>"
						."<input type='text' value='" . $indexedgroups[$col]->label . "' style='margin-left:5%;width:45%;' /></div>";
			$invalues[] = $indexedgroups[$col]->raw;
		}
		$invalues = implode("||", $invalues);

		if (version_compare(JVERSION, "4", "gt"))
		{
			$input .= '</div>';
		}
		else
		{
			$input .= '</div></td>
		</tr></table>';

		}
		$input .= '<textarea style="display:none;margin-top:10px;"  name="' . $this->name . '"  id="jevcolumns">' . $invalues . '</textarea>';
		$input .= '<div style="clear:left"></div>';

		$input .= '<script type="text/javascript">document.addEventListener("DOMContentLoaded", () => {setupColumnChoices(true);setupColumnLis(true);})</script>';

		return $input;

	}

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	protected
	function getOptions()
	{

		// Initialize variables.
		$session = Factory::getSession();
		$options = array();

		// Initialize some field attributes.
		$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
		$published = (string) $this->element['published'];

		// OLD values
		// Load the category options for a given extension.
		if (!empty($extension))
		{

			// Filter over published state or not depending upon if it is present.
			if ($published)
			{
				$options = HTMLHelper::_('category.options', $extension, array('filter.published' => explode(',', $published)));
			}
			else
			{
				$options = HTMLHelper::_('category.options', $extension);
			}

			// Verify permissions.  If the action attribute is set, then we scan the options.
			if ($action = (string) $this->element['action'])
			{

				// Get the current user object.
				$user = Factory::getUser();

				// TODO: Add a preload method to Access so that we can get all the asset rules in one query and cache them.
				// eg Access::preload('core.create', 'com_content.category')
				foreach ($options as $i => $option)
				{
					// Unset the option if the user isn't authorised for it.
					if (!$user->authorise($action, $extension . '.category.' . $option->value))
					{
						unset($options[$i]);
					}
				}
			}
		}
		else
		{
			Factory::getApplication()->enqueueMessage('500 - ' . Text::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'), 'warning');
		}

		// if no value exists, try to load a selected filter category from the old category filters
		if (!$this->value && ($this->form instanceof Form))
		{
			$context     = $this->form->getName();
			$this->value = array();
			for ($i = 0; $i < 20; $i++)
			{
				if ($this->form->getValue("catid$i", "params", 0))
				{
					$this->value[] = $this->form->getValue("catid$i", "params", 0);
				}
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;

	}

}
