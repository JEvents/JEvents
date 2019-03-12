<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield.list');

class JFormFieldJevimagename extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jevimagename';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
	    $plugin = JPluginHelper::getPlugin('jevents', 'jevfiles');

		$options = array();

	    if (!empty($plugin))
        {

	        $params = new JRegistry($plugin->params);
	        if ($params->get('imnum', 0))
            {
	            $options[] = JHTML::_('select.option', '0', JText::_('JEV_STRUCTURED_DATA_SELECT_IMAGE'));

	            JFactory::getLanguage()->load('plg_jevents_jevfiles', JPATH_ADMINISTRATOR);

	            for ($i=1; $i<=$params->get('imnum', 0); $i++)
                {
	                $options[] = JHTML::_('select.option', $i, JText::_('JEV_STANDARD_IMAGE_' . $i) );
                }
            }
            else
            {
	            $options[] = JHTML::_('select.option', '0', JText::_('JEV_STRUCTURED_DATA_OUTPUT_REQUIRES_IMAGES_ADDON_CONFIGURED_PROPERLY'));
            }
        }
        else
        {
	        $options[] = JHTML::_('select.option', '0', JText::_('JEV_STRUCTURED_DATA_OUTPUT_REQUIRES_IMAGES_ADDON'));

        }


        return $options;

	}
}