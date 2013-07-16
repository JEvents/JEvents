<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.html.parameter.element');
class JElementJevhtml extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevhtml';

	function fetchElement($name, $value, &$node, $control_name)
	{

		$editor  = JFactory::getEditor( null);
		
		$rows = $node->attributes('rows');
		$cols = $node->attributes('cols');
		$height = ((string) $node->attributes('width')) ? (string) $node->attributes('width') : '250';
		$width   = ((string) $node->attributes('height')) ? (string) $node->attributes('height') : '600';

		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		$buttons = false;
		//$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
		$value = str_replace('\n', "<br/>", JText::_($value));
		//$value = str_replace("\n", "<br/>", $value);

		$html =  $editor->display($control_name.'['.$name.']', $value , $width, $height, $cols, $rows, $buttons , $control_name.$name);
				
		if (JRequest::getCmd("task")=="templates.edit"){			
			ob_start();
			?>
			<div>
			<h4 style='display:inline'><?php echo JText::_("RSVP_SELECT_FIELD_TO_INSERT");?> : </h4>
			<select onchange="messagesEditorPlugin.insert('<?php echo $control_name.$name;?>fields' )" id="<?php echo $control_name.$name;?>fields" class="messagesEditorPlugin"  >
				<option value="Select ...:">Select ...</option>
				<optgroup label="<?php echo JText::_("RSVP_EVENT_FIELDS",true);?>" >
					<option value="EVENT"><?php echo JText::_("RSVP_EVENT_TITLE");?></option>
					<option value="LINK"><?php echo JText::_("RSVP_EVENT_LINK");?></option>						
					<option value="DATE}%Y %m %d{/DATE"><?php echo JText::_("RSVP_EVENT_DATE");?></option>
					<option value="LOCATION}"><?php echo JText::_("RSVP_EVENT_LOCATION");?></option>
					<?php if ($name=="manpaymessage" || $name=="pplpaymessage" )  {?>
					<option value="TRANSACTIONID}%010s{/TRANSACTIONID"><?php echo JText::_("RSVP_TRANSACTION_NUMBER");?></option>
					<option value="AMOUNTPAID"><?php echo JText::_("RSVP_PAYMENTAMOUNT");?></option>
					<option value="TIMEPAID}%d %B %Y{/TIMEPAID"><?php echo JText::_("RSVP_TIMEPAYMENTMADE");?></option>
					<?php } ?>
					<?php 
					// Bad choices of variable names !
					// templatebody is manual payment gateway layout
					//  is paypal payment gateway layout				
					if ($name=="templatebody" || $name=="template" || $name=="paypaltemplate" || $name=="manualtemplate" )  {?>
					<option value="TOTALFEES"><?php echo JText::_("RSVP_TOTALFEES");?></option>						
					<option value="FEESPAID"><?php echo JText::_("RSVP_FEESPAID");?></option>						
					<option value="BALANCE"><?php echo JText::_("RSVP_BALANCE");?></option>						
					<option value="FORM"><?php echo JText::_("RSVP_PAYMENT_FORM");?></option>
					<?php } ?>
					<option value="CREATOR"><?php echo JText::_("RSVP_EVENT_CREATOR");?></option>						
					<option value="CUSTOM"><?php echo JText::_("RSVP_EVENT_CUSTOMFIELD_SUMMARY");?></option>						
					<option value="REPEATSUMMARY"><?php echo JText::_("RSVP_EVENT_REPEATSUMMARY");?></option>						
					<option value="WAITINGMESSAGE"><?php echo JText::_("RSVP_WAITINGMESSAGE");?></option>						
					
				</optgroup>
				<optgroup label="<?php echo JText::_("RSVP_TEMPLATE_FIELDS",true);?>" class="templatefields">
				</optgroup>
			</select>
			</div>
			<?php
			$html .= ob_get_clean();
		}
		return $html;
		//return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$value.'</textarea>';
		
	}

}
