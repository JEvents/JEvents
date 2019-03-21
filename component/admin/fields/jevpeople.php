<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield.list');

class JFormFieldJevpeople extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jevpeople';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
	    $plugin = JPluginHelper::getPlugin('jevents', 'jevpeople');

		$options = array();

	    if (!empty($plugin))
        {

        	$db = JFactory::getDbo();
        	$query = $db->getQuery(true);
	        $query->select("*")
		        ->from("#__jev_peopletypes");
            $db->setQuery($query);
            $types = $db->loadObjectList();
            if (!empty($types))
            {

	            $options[] = JHTML::_('select.option', '0', JText::_('JEV_SELECT_PERSON_TYPE_AS_PERFORMER'));

	            JFactory::getLanguage()->load('plg_jevents_jevfiles', JPATH_ADMINISTRATOR);

	            foreach($types as $type)
                {
	                $options[] = JHTML::_('select.option', $type->type_id, JText::_($type->title) );
                }
            }
            else
            {
	            $options[] = JHTML::_('select.option', '0', JText::_('JEV_STRUCTURED_DATA_OUTPUT_REQUIRES_PEOPLE_TYPES_SET_UP'));
            }
        }
        else
        {
	        $options[] = JHTML::_('select.option', '0', JText::_('JEV_STRUCTURED_DATA_OUTPUT_REQUIRES_IMAGES_ADDON'));

        }


        return $options;

	}
}