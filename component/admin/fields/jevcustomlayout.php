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

jimport('joomla.form.helper');
FormHelper::loadFieldClass('list');

class JFormFieldJevcustomlayout extends JFormFieldList
{

	protected $type = 'Jevcustomlayout';

	protected
	function getLabel()
	{
		return parent::getLabel();
	}

	protected
	function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());
		$layouttype = $this->getAttribute("layouttype");
		$target = $this->getAttribute("target");
		$csstarget = $this->getAttribute("csstarget");
		$ignorebrtarget = $this->getAttribute("ignorebrtarget");
		$ttop = $this->getAttribute("templatetop");
		$trow = $this->getAttribute("templaterow");
		$tbot = $this->getAttribute("templatebottom");
		$inccss = $this->getAttribute("inccss");
		$version = JEventsVersion::getInstance();
		$release = $version->get("RELEASE", "1.0.0");
		HTMLHelper::script("https://www.jevents.net/jevlayouts/LatestEvents.js?$release");
		//HTMLHelper::script("http://ubu.j33jq.com/jevlayouts/LatestEvents.js?$release");

		$html =  "<script>jQuery(document).ready(function ($){loadJevPreview('$target', '$csstarget', '$ignorebrtarget', '$ttop', '$trow', '$tbot', '$inccss');});</script>";
		$id = $this->id;
        if (version_compare(JVERSION, "4", "gt")) {
            $html .= <<<DROPDOWN
				<div class="dropdown btn-group" id="$id">
				  <button type="button" id="dropdown$target" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
				  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Select Layout
					<span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu" style="min-width:200px" role="menu" aria-labelledby="dropdown$target" id="dropdownUL_$target" role="menu">
					<li role="presentation"><a role="menuitem" 
					class="dropdownpopover dropdown-item" href="#" 
					data-bs-title="Current Customised Value" 
					data-bs-content="Custom Format String customised by you">Current Value</a></li>
					</ul>
				</div>
DROPDOWN;
        } else {
            $html .= <<<DROPDOWN
				<div class="dropdown btn-group" id="$id">
				  <button class="btn btn-default dropdown-toggle" type="button" id="dropdown$target" 
				  data-toggle="dropdown" aria-expanded="false">
					Select Layout
					<span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdown$target" id="dropdownUL_$target" role="menu">
					<li role="presentation"><a role="menuitem" class="dropdownpopover" href="#" 
					data-title="Current Customised Value" 
					data-content="Custom Format String customised by you">Current Value</a></li>
					</ul>
				</div>
DROPDOWN;
        }

		return $html;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	protected function getOptions()
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
