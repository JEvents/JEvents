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

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJevcategory extends JFormFieldList
{

	protected $type = 'Jevcategory';

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

			array_unshift($options, JHTML::_('select.option', '0', '- ' . JText::_( 'JEV_SELECT_CATEGORY' ) . ' -'));
		}
		else
		{

			JFactory::getApplication()->enqueueMessage('500 - ' . JText::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'), 'warning');
		}

		// if no value exists, try to load a selected filter category from the list view
		if (!$this->value && ($this->form instanceof JForm))
		{
			$context = $this->form->getName();
			$this->value = $session->get($context . '.filter.category_id', $this->value);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;

	}

}
