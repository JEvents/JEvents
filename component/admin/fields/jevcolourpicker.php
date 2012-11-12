<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

include_once(JPATH_ADMINISTRATOR."/components/com_jevents/jevents.defines.php");
include_once(JEV_ADMINLIBS."/colorMap.php");

class JFormFieldJevcolourpicker extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jevcolourpicker';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$html[] = '<input type="text" name="'.$this->name.'" value="'.$this->value.'" id="pick1064797275field"/><br/>';
		$html[]  = '<div class="clr"></div>';
		$html[]  = '<iframe id="fred"  frameborder="" src="'.JURI::root()."administrator/components/com_jevents/libraries/colours.html?id=fred&j16=1".'" style="min-height:250px!important;height:250px;min-width:300px!important;width:300px;z-index:9999;right:0px;top:0px;overflow:visible!important;"></iframe>';
		$html[]  = '<div class="clr"></div>';

		// add script to auto open the basic options tab!
		$doc = JFactory::getDocument();
		$script = <<<SCRIPT
window.addEvent('load', function() {
	var basicoptions = document.getElement('#basic-options')
	if (basicoptions && !basicoptions.hasClass('pane-toggler-down')) {
	   basicoptions.fireEvent('click', basicoptions, 1000);
	};
});
SCRIPT;
		$doc->addScriptDeclaration($script);
		
		return implode($html);
	}

	protected function getLabel() {
		$html = array();
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];

		$html[]  = '<label id="pick1064797275" for="'.$this->id.'" class="hasTip"  style="color:'.JevMapColor($this->value).';background-color:'.$this->value.';"		'
		.' title="'.htmlspecialchars(trim(JText::_($text), ':').'::'
		.	JText::_($this->description), ENT_COMPAT, 'UTF-8').'" >' ;
		$html[]  = '<a id="colorPickButton" name ="colorPickButton" href="javascript:void(0)"   style="color:inherit;background-color:inherit;display:block;font-weight:bold;padding:3px;">'. JText::_('JEV_COLOR_PICKER').'</a>';
		$html[]  = '</label>';
		return implode($html);

	}

}