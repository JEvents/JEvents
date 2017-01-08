<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevcategorynew.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJevcustomlayout extends JFormFieldList
{

	protected $type = 'Jevcustomlayout';

	protected
			function getLabel()
	{
		if (!JevJoomlaVersion::isCompatible("3.4")){
			return "";
		}
		return parent::getLabel();
	}

	protected
			function getInput()
	{
		if (!JevJoomlaVersion::isCompatible("3.4")){
			return "";
		}
		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());
		$layouttype = $this->getAttribute("layouttype");
		$target = $this->getAttribute("target");
		$csstarget = $this->getAttribute("csstarget");
		JHtml::script("https://www.jevents.net/jevlayouts/LatestEvents.js");
		$html =  "<script>jQuery(document).ready(function ($){loadJevPreview('$target', '$csstarget');});</script>";
		$id = $this->id;
		$html .= <<<DROPDOWN
<div class="dropdown btn-group" id="$id">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdown$target" data-toggle="dropdown" aria-expanded="false">
    Select Layout
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdown$target" id="dropdownUL_$target" role="menu">
    <li role="presentation"><a role="menuitem" class="dropdownpopover" href="#" data-title="Current Customised Value" data-content="Custom Format String customised by you">Current Value</a></li>
    </ul>
</div>
DROPDOWN;
		return $html;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{ 
		// Initialize variables.
		$session = JFactory::getSession();
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
				$options = JHtml::_('category.options', $extension, array('filter.published' => explode(',', $published)));
			}
			else
			{
				$options = JHtml::_('category.options', $extension);
			}

			// Verify permissions.  If the action attribute is set, then we scan the options.
			if ($action = (string) $this->element['action'])
			{

				// Get the current user object.
				$user = JFactory::getUser();

				// TODO: Add a preload method to JAccess so that we can get all the asset rules in one query and cache them.
				// eg JAccess::preload('core.create', 'com_content.category')
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
			JFactory::getApplication()->enqueueMessage('500 - ' . JText::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'), 'warning');
		}

		// if no value exists, try to load a selected filter category from the old category filters
		if (!$this->value && ($this->form instanceof JForm))
		{
			$context = $this->form->getName();
			$this->value =  array();
			for($i=0; $i<20; $i++){
				if ($this->form->getValue("catid$i","params",0)){
					$this->value[] =  $this->form->getValue("catid$i","params",0);
				}
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;

	}

}
