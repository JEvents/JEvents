<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

if (defined("EDITING_JEVENT"))
	return;
define("EDITING_JEVENT", 1);

ob_start();

JHTML::_('behavior.tooltip');

$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
// get configuration object
$cfg = JEVConfig::getInstance();

// use JRoute to preseve language selection
$action = JFactory::getApplication()->isAdmin() ? "index.php" : JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . JEVHelper::getItemid());

$document = JFactory::getDocument();
$js = '	window.addEvent(\'load\', function(){ document.getElements(\'dt.event\').each(function(tab){ tab.fireEvent(\'click\'); })  });';
$document->addScriptDeclaration($js);
?>
<div id="jevents" <?php
echo (!JFactory::getApplication()->isAdmin() && $params->get("darktemplate", 0)) ? "class='jeventsdark'" : "";
?>>
	<form action="<?php echo $action; ?>" method="post" name="adminForm" enctype='multipart/form-data' id="adminForm"  >
		<?php
		ob_start();

		// these are needed for front end admin
		ob_start();
		?>
		<div class="jev_edit_event_notice">
			<?php
			if ($this->editCopy)
			{
				$repeatStyle = "";
				echo "<h3>" . JText::_('YOU_ARE_EDITING_A_COPY_ON_AN_ICAL_EVENT') . "</h3>";
			}
			else if ($this->repeatId == 0)
			{
				$repeatStyle = "";
				// Don't show warning for new events
				if ($this->ev_id > 0)
				{
					echo JText::_('YOU_ARE_EDITING_AN_ICAL_EVENT');
				}
			}
			else
			{
				$repeatStyle = "style='display:none;'";
				?>
				<h3><?php echo JText::_('YOU_ARE_EDITING_AN_ICAL_REPEAT'); ?></h3>
				<input type="hidden" name="cid[]" value="<?php echo $this->rp_id; ?>" />
				<?php
			}
			?>
		</div>
		<?php
		if ($params->get("checkclashes", 0) || $params->get("noclashes", 0))
		{
			?>
			<div id='jevoverlapwarning'>
				<div><?php echo JText::_("JEV_OVERLAPPING_EVENTS_WARNING"); ?></div>
				<div id="jevoverlaps"></div>
			</div>
			<?php
		}

		$native = true;
		if ($this->row->icsid() > 0)
		{
			$thisCal = $this->dataModel->queryModel->getIcalByIcsid($this->row->icsid());
			if (isset($thisCal) && $thisCal->icaltype == 0)
			{
				// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
				echo JText::_("JEV_IMPORT_WARNING");
				$native = false;
			}
			else if (isset($thisCal) && $thisCal->icaltype == 1)
			{
				// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
				echo JText::_("JEV_IMPORT_WARNING2");
				$native = false;
			}
		}

		$this->searchtags[] = "{{MESSAGE}}";
		$output = ob_get_clean();
		$this->replacetags[] = $output;
		echo $output;
		$this->blanktags[] = "";

		ob_start();
		if (isset($this->row->_uid))
		{
			?>
			<input type="hidden" name="uid" value="<?php echo $this->row->_uid; ?>" />
			<?php
		}

		// I need $year,$month,$day So that I can return to an appropriate date after saving an event (the repetition ids have all changed so I can't go back there!!)
		list($year, $month, $day) = JEVHelper::getYMD();
		// need rp_id for front end editing cancel to work note that evid is the repeat id for viewing detail 
		?>
		<input type="hidden" name="jevtype" value="icaldb" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="updaterepeats" value="0"/>
		<input type="hidden" name="task" value="<?php echo JRequest::getCmd("task", "icalevent.edit"); ?>" />
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
		<input type="hidden" name="rp_id" value="<?php echo isset($this->rp_id) ? $this->rp_id : -1; ?>" /> 
		<input type="hidden" name="year" value="<?php echo $year; ?>" /> 
		<input type="hidden" name="month" value="<?php echo $month; ?>" /> 
		<input type="hidden" name="day" value="<?php echo $day; ?>" /> 

		<input type="hidden" name="state" id="state" value="<?php echo $this->row->state(); ?>" />
		<input type="hidden" name="evid" id="evid" value="<?php echo $this->ev_id; ?>" />
		<input type="hidden" name="valid_dates" id="valid_dates" value="1"  />
		<?php if (!JFactory::getApplication()->isAdmin()) { ?>
		<input type="hidden" name="Itemid" id="Itemid" value="<?php echo  JEVHelper::getItemid();?>"  />
		<?php } ?>
		<?php
		if ($this->editCopy)
		{
			?>
			<input type="hidden" name="old_evid" id="old_evid" value="<?php echo $this->old_ev_id; ?>" />
			<?php
		}
		?>
		<script type="text/javascript" >
                    <?php
                    $control_name="title";

                    if (!empty($this->requiredtags))
                    {
                            foreach ($this->requiredtags as $tag)
                            {
                                    echo "JevStdRequiredFields.fields.push({'name':'".$tag['id']."', 'default' :'".$tag['default_value']."' ,'reqmsg':'".$tag['alert_message']."'});\n";
                            }
                    }
                    ?>
			Joomla.submitbutton = function (pressbutton) {

				if (pressbutton.substr(0, 6) == 'cancel' || !(pressbutton == 'icalevent.save' || pressbutton == 'icalrepeat.save' || pressbutton == 'icalevent.savenew' || pressbutton == 'icalrepeat.savenew'   || pressbutton == 'icalevent.apply'  || pressbutton == 'icalrepeat.apply')) {
					if (document.adminForm['catid']){
						// restore catid to input value
						document.adminForm['catid'].value=0;
						document.adminForm['catid'].disabled=true;
					}
					submitform( pressbutton );
					return;
				}
				var form = document.adminForm;
				var editorElement = $('jevcontent');
				if (editorElement)
				{
					<?php
					echo $this->editor->save('jevcontent');
					?>
				}
		try {
			if (!JevStdRequiredFields.verify(document.adminForm)){
				return;
			}
			if (!JevrRequiredFields.verify(document.adminForm)){
				return;
			}
		}
		catch (e){
		
		}
		// do field validation
		if (form.catid && form.catid.value==0 && form.catid.options && form.catid.options.length){
			alert ( '<?php echo JText::_('JEV_SELECT_CATEGORY', true); ?>' );
		}
		else if (form.ics_id.value == "0"){
			alert( "<?php echo html_entity_decode(JText::_('JEV_MISSING_ICAL_SELECTION', true)); ?>" );
		}
		else if (form.valid_dates.value =="0"){
			alert( "<?php echo JText::_("JEV_INVALID_DATES", true); ?>");
		}
		else {
<?php
// in case editor is toggled off - needed for TinyMCE
echo $this->editor->save('jevcontent');
// Do we have to check for conflicting events i.e. overlapping times etc. BUT ONLY FOR EVENTS INITIALLY
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("checkclashes", 0) || $params->get("noclashes", 0))
{
	$checkURL = JURI::root() . "components/com_jevents/libraries/checkconflict.php";
	if (JEVHelper::getItemid()>0){
		$checkURL .=  "?Itemid=".JEVHelper::getItemid();
	}

	?>
					// reformat start and end dates  to Y-m-d format
					reformatStartEndDates();
					checkConflict('<?php echo $checkURL; ?>',pressbutton, '<?php echo JSession::getFormToken(); ?>', '<?php echo JFactory::getApplication()->isAdmin() ? 'administrator' : 'site'; ?>', <?php echo $this->repeatId; ?> );
	<?php
}
else
{
	?>
					// reformat start and end dates  to Y-m-d format
					reformatStartEndDates();
					submit2(pressbutton);
	<?php
}
?>
		}
	}

	function submit2(pressbutton){
		// sets the date for the page after save
		resetYMD();
		submitform(pressbutton);	
	}
		//-->
		</script>

		<?php
		$this->searchtags[] = "{{HIDDENINFO}}";
		$output = ob_get_clean();
		$this->replacetags[] = $output;
		echo $output;
		$this->blanktags[] = "";
		?>
		<div class="adminform" align="left">			
			<?php
			// if we enter date/time before description then force single pane editing.
			// get configuration object
			$cfg = JEVConfig::getInstance();

			if ($cfg->get('timebeforedescription', 0))
			{
				// $cfg->set('com_single_pane_edit', 1);
			}
			if (!$cfg->get('com_single_pane_edit', 0))
			{
				echo JHtml::_('tabs.start', 'tabs');
				echo JHtml::_('tabs.panel', JText::_("JEV_TAB_COMMON"), "event");
				echo '<div style="clear:left;margin:0px;padding:0px;height:0px;"></div>';
			}
			?>
			<table cellpadding="5" cellspacing="2" border="0"  class="adminform" id="jevadminform">
				<tr>
					<td align="left"><?php echo $this->form->getLabel("title"); ?></td>
					<td><?php echo $this->form->getInput("title"); ?></td>
					<?php
					if ($this->form->getInput("priority"))
					{
						?>
						<td  align="left"><?php echo $this->form->getLabel("priority"); ?></td>
						<td ><?php echo $this->form->getInput("priority"); ?></td>
					<?php } ?>
				</tr>
				<?php
				if ($this->form->getInput("creator"))
				{
					?>
					<tr class="jevcreator">
						<td align="left"><?php echo $this->form->getLabel("creator"); ?></td>
						<td colspan="3"><?php echo $this->form->getInput("creator"); ?></td>
					</tr>			
					<?php
				}

				if ($this->form->getInput("ics_id"))
				{
					?>
					<tr class="jevical">
						<td align="left"><?php echo $this->form->getLabel("ics_id"); ?></td>
						<td colspan="3"><?php echo $this->form->getInput("ics_id"); ?></td>
					</tr>
					<?php
				}

				if ($this->form->getInput("lockevent"))
				{
					?>
					<tr>
						<td><?php echo $this->form->getLabel("lockevent"); ?></td>
						<td colspan='3'><?php echo $this->form->getInput("lockevent"); ?></td>
					</tr>
					<?php
				}

				if ($this->form->getLabel("catid") || $this->form->getLabel("access"))
				{
					?>
					<tr>
						<?php
						if ($this->form->getLabel("catid"))
						{
							?>
							<td valign="top" align="left" class="jevcategory"><?php echo $this->form->getLabel("catid"); ?></td>
							<td style="width:200px"  class="jevcategory"><?php echo $this->form->getInput("catid"); ?></td>
							<?php
						}

						if ($this->form->getLabel("access"))
						{
							?>
							<td align="left" class="accesslevel">	<?php echo $this->form->getLabel("access"); ?>	</td>
							<td class="accesslevel"><?php echo $this->form->getInput("access"); ?></td>
							<?php
						}
						?>
					</tr>
					<?php
				}

				if ($this->form->getLabel("state"))
				{
					?>
					<tr class="jevpublished">
						<td><?php echo $this->form->getLabel("state"); ?></td>
						<td colspan="3"><?php echo $this->form->getInput("state"); ?></td>
					</tr>
					<?php
				}

				if ($this->form->getInput("color"))
				{
					?>
					<tr>
						<td valign="top" align="left"><?php echo $this->form->getLabel("color"); ?></td>
						<td colspan="3"><?php echo $this->form->getInput("color"); ?>	</td>
					</tr>
					<?php
				}

				$cfg = JEVConfig::getInstance();
				if ($cfg->get('timebeforedescription', 0))
				{
					?>
					<tr>
						<td valign="top" align="left" colspan="4">
							<?php
							ob_start();
							echo $this->loadTemplate("datetime");
							$this->searchtags[] = "{{CALTAB}}";
							$output = ob_get_clean();
							$this->replacetags[] = $output;
							echo $output;
							$this->blanktags[] = "";
							?>
						</td>
					</tr>
					<?php
				}
				?>

				<tr  class="jev_description">
					<td valign="top" align="left"><?php echo $this->form->getLabel("jevcontent"); ?></td>
					<td colspan="3"><?php echo $this->form->getInput("jevcontent"); ?></td>
				</tr>

				<tr id="jeveditlocation">
					<td width="130" align="left"  valign="top"><?php echo $this->form->getLabel("location"); ?></td>
					<td colspan="3"><?php echo $this->form->getInput("location"); ?></td>
				</tr>
				<tr class="jev_contact">
					<td align="left"><?php echo $this->form->getLabel("contact_info"); ?></td>
					<td colspan="3"><?php echo $this->form->getInput("contact_info"); ?></td>
				</tr>
				<tr class="jev_extrainfo">
					<td align="left" valign="top"><?php echo $this->form->getLabel("extra_info"); ?></td>
					<td colspan="3"><?php echo $this->form->getInput("extra_info"); ?></td>
				</tr>						
				<?php
				foreach ($this->customfields as $key => $val)
				{
                                        // skip custom fields that are already displayed on other tabs
                                        if (isset($val["group"]) && $val["group"]!="default"){
                                            continue;
                                        }

					?>
					<tr class="jevplugin_<?php echo $key; ?>">
						<td valign="top"  width="130" align="left">
							<?php
							echo $this->customfields[$key]["label"];
							?>
						</td>
						<td colspan="3">
							<?php
							echo $this->customfields[$key]["input"];
							?>
						</td>
					</tr>
					<?php
				}
				?>

			</table>
			<?php
			$cfg = JEVConfig::getInstance();
			if (!$cfg->get('com_single_pane_edit', 0) && !$cfg->get('timebeforedescription', 0))
			{
				echo JHtml::_('tabs.panel', JText::_("JEV_TAB_CALENDAR"), "calendar");
				echo '<div style="clear:left;margin:0px;padding:0px;height:0px;"></div>';
			}
			if (!$cfg->get('timebeforedescription', 0))
			{
				ob_start();
				echo $this->loadTemplate("datetime");
				$this->searchtags[] = "{{CALTAB}}";
				$output = ob_get_clean();
				$this->replacetags[] = $output;
				echo $output;
				$this->blanktags[] = "";
			}

			if (count($this->extraTabs) > 0)
			{
				foreach ($this->extraTabs as $extraTab)
				{
					$cfg = JEVConfig::getInstance();
					if (!$cfg->get('com_single_pane_edit', 0))
					{
						echo JHtml::_('tabs.panel', $extraTab['title'], $extraTab['paneid']);
					}
					echo "<div class='jevextrablock'>";
					echo $extraTab['content'];
					echo "</div>";
				}
			}

			if (!$cfg->get('com_single_pane_edit', 0))
			{
				echo JHtml::_('tabs.end');
			}
			?>
		</div>
		<?php
		$output = ob_get_clean();
		if (!$this->loadEditFromTemplate('icalevent.edit_page', $this->row, 0, $this->searchtags, $this->replacetags, $this->blanktags))
		{
			echo $output;
		}   // if (!$this->loadedFromTemplate('icalevent.edit_page', $this->row, 0)){
		?>
	</form>
</div>

<?php
$editpagecode = ob_get_clean();
if (!JFactory::getApplication()->isAdmin()) {
// fix for bad HTML in tabs!
	$editpagecode = str_replace("<span><h3>", "<span><strong>", $editpagecode);
	$editpagecode = str_replace("</h3></span>", "</strong></span>", $editpagecode);
// Remove chosen/bootstrap styling if required
	$editpagecode = str_replace(" btn-group", "", $editpagecode);
	$editpagecode = str_replace(" btn-success", "", $editpagecode);
	$editpagecode = str_replace(" btn", "", $editpagecode);
	$editpagecode = str_replace("btn ", "", $editpagecode);
}
echo $editpagecode;

$app = JFactory::getApplication();
if ($app->isSite()) {
    if ($params->get('com_edit_toolbar', 0) == 1 || $params->get('com_edit_toolbar', 0) == 2 ) {
        //Load the toolbar at the bottom!
        $bar = JToolBar::getInstance('toolbar');
        $barhtml = $bar->render();
        echo $barhtml;
    }
}