<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield.list');

class JFormFieldJevcustomfield extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jevcustomfield';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
	    $plugin = PluginHelper::getPlugin('jevents', 'jevcustomfields');

		$options = array();
		$customfields = array();
		$fieldtype = (string) $this->element['fieldtype'];

	    if (!empty($plugin))
        {

	        // New parameterised fields
	        $plugin->params = new Joomla\Registry\Registry($plugin->params);
	        $template = $plugin->params->get("template", "");
	        $customfields = array();
	        if ($template != "")
	        {
		        $xmlfile = JPATH_PLUGINS . "/jevents/jevcustomfields/customfields/templates/" . $template;
		        if (file_exists($xmlfile))
		        {

			        $params = JevCfForm::getInstance("com_jevent.customfields", $xmlfile, array('control' => 'jform', 'load_data' => true), true, "/form");
			        $customfields = array();

			        // Slimmer method since we don't need the elements
			        $groups = $params->getFieldsetsBasic();
			        foreach ($groups as $group => $elementNotNeeded)
			        {
				        $extracustomfields = $params->renderToBasicArray('params', $group);
				        if ($extracustomfields)
				        {
					        $customfields = array_merge($customfields, $extracustomfields);
				        }
			        }
		        }
	        }


            if (!empty($customfields))
            {

	            Factory::getLanguage()->load('plg_jevents_jevcustomfields', JPATH_ADMINISTRATOR);
	            foreach ($customfields as $customfield)
	            {

	            	if ($customfield['fieldtype'] == $fieldtype )
		            {
			            $options[] = JHTML::_('select.option', $customfield['name'], $customfield['label']);
		            }
	            }

	            if (count($options) > 1)
	            {
		            array_unshift($options, JHTML::_('select.option', '0', Text::_( (string) $this->element['selectmessage'] )));
	            }

            }
            else
            {
	            $options[] = JHTML::_('select.option', '0', Text::_((string) $this->element['badconfigmessage'] ));
            }
        }
        if (count($options) == 0)
        {
	        $options[] = JHTML::_('select.option', '0',  Text::_((string) $this->element['badconfigmessage'] ));

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
