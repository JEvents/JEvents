<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;

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
	    $plugin = PluginHelper::getPlugin('jevents', 'jevpeople');

		$options = array();

	    if (!empty($plugin))
        {

        	$db = Factory::getDbo();
        	$query = $db->getQuery(true);
	        $query->select("*")
		        ->from("#__jev_peopletypes");
            $db->setQuery($query);
            $types = $db->loadObjectList();
            if (!empty($types))
            {

	            $options[] = JHTML::_('select.option', '0', Text::_('JEV_SELECT_PERSON_TYPE_AS_PERFORMER'));

	            Factory::getLanguage()->load('plg_jevents_jevfiles', JPATH_ADMINISTRATOR);

	            foreach($types as $type)
                {
	                $options[] = JHTML::_('select.option', $type->type_id, Text::_($type->title) );
                }
            }
            else
            {
	            $options[] = JHTML::_('select.option', '0', Text::_('JEV_STRUCTURED_DATA_OUTPUT_REQUIRES_PEOPLE_TYPES_SET_UP'));
            }
        }
        else
        {
	        $options[] = JHTML::_('select.option', '0', Text::_('JEV_STRUCTURED_DATA_OUTPUT_REQUIRES_IMAGES_ADDON'));

        }


        return $options;

	}

	protected
	function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}

}
