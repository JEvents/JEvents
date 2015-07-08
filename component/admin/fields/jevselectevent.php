<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevselectevent.php 3503 2012-04-10 11:04:26Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJEVselectEvent extends JFormField
{

	protected $type = 'JEVselectevent';

	protected function getInput()
	{
		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		if ($this->fieldname=="rp_id"){
			// The active event id field.
			if (0 == (int)$this->value) {
				$value = '';
			} else {
				$value = (int)$this->value;
			}
			return  '<input type="hidden" id="selectedrepeat"   name="'.$this->name.'" value="'.$value.'" />';
		}

		// Load the modal behavior script.
		JLoader::register('JevModal',JPATH_LIBRARIES."/jevents/jevmodal/jevmodal.php");
		JevModal::modal("a.selectmodal");

		$js = "
		function jSelectEvent_".$this->id."(link, title, Itemid, evid, rpid) {
			jQuery('#selectedeventtitle').val( title);
			jQuery('#selectedevent').val( evid);
			jQuery('#selectedrepeat').val( rpid);
			jQuery('#selectEvent').modal('hide');
			return false;
		}";
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration($js);


		// Setup variables for display.
		$html	= array();
		$link = 'index.php?option=com_jevents&amp;task=icalevent.select&amp;tmpl=component&amp;'.JSession::getFormToken().'=1&amp;nomenu=1&function=jSelectEvent_'.$this->id;

		// get the repeat id
		$rpidfield = $this->form->getField("rp_id", "request");
		$rp_id = $rpidfield->value;
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT det.summary as title' .
			' FROM #__jevents_vevdetail as det ' .
			' LEFT JOIN #__jevents_repetition as rep ON rep.eventdetail_id = det.evdet_id' .
			' WHERE rep.rp_id = '.(int) $rp_id
		);
		$title = $db->loadResult();
		echo $db->getErrorMsg();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_JEVENTS_FIELD_SELECT_EVENT_LABEL');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		$html[] = '<div class="fltlft input-append" >';
		$html[] = '  <input type="text" id="selectedeventtitle" value="'.$title.'" disabled="disabled" size="50" />';

		$link  = "javascript:jevModalPopup('selectEvent', '".$link ."', '". JText::_("COM_JEVENTS_CHANGE_EVENT_BUTTON" ,  array('jsSafe'=>true) ) . "'); ";

		// The user select button.
		$html[] = '	<a class="selectmodal btn btn-primary" title="'.JText::_('COM_JEVENTS_CHANGE_EVENT').'"  href="'.$link.'" ><span class="icon-list icon-white"></span>'.JText::_('COM_JEVENTS_CHANGE_EVENT_BUTTON').'</a>';
		$html[] = '</div>';

		// The active event id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="selectedevent"  '.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}