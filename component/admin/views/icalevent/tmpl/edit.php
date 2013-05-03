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

// Load Bookstrap
JHtml::_('behavior.framework',true);
JHtml::_('bootstrap.framework');
JHtmlBootstrap::loadCss();

/*
  // New version that uses JForm
  JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR."/models/forms/");
  $xpath = false;
  // leave form control blank since we want the fields as ev_id and not jform[ev_id]
  $form = JForm::getInstance("jevents.edit.icalevent",'icalevent', array('control' => '', 'load_data' => false), false, $xpath);

  foreach ($this->row as $k => $v) {
  if (strpos($k, "_")===0){
  $newk = substr($k, 1);
  $this->row->$newk =$v;
  }
  }
  $form->bind($this->row);

  echo $form->getInput("demo")."<br/>";
  echo $form->getInput("ev_id")."<br/>";
  echo $form->getInput("usergroup")."<br/>";
  echo $form->getInput("show_title")."<br/>";
  echo $form->getInput("radiodemo")."<br/>";
 */

global $task, $catid;
$db = & JFactory::getDBO();
$editor = & JFactory::getEditor();
if ($editor->get("_name")=="codemirror"){
	$editor =  JFactory::getEditor("none");
	JFactory::getApplication()->enqueueMessage(JText::_("JEV_CODEMIRROR_NOT_COMPATIBLE_EDITOR","WARNING"));
}

// clean any existing cache files
$cache = & JFactory::getCache(JEV_COM_COMPONENT);
$cache->clean(JEV_COM_COMPONENT);
// use JRoute to preseve language selection
$action = JFactory::getApplication()->isAdmin() ? "index.php" : JRoute::_( "index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . JEVHelper::getItemid());

// load any custom fields
$dispatcher = & JDispatcher::getInstance();
$customfields = array();
$res = $dispatcher->trigger('onEditCustom', array(&$this->row, &$customfields));

// I need $year,$month,$day So that I can return to an appropriate date after saving an event (the repetition ids have all changed so I can't go back there!!)
list($year, $month, $day) = JEVHelper::getYMD();
if (!isset($this->ev_id))
{
	$this->ev_id = $this->row->ev_id();
}

if ($this->editCopy)
{
	$this->old_ev_id = $this->ev_id;
	$this->ev_id = 0;
	$this->repeatId = 0;
	$this->rp_id = 0;
	unset($this->row->_uid);
	$this->row->id(0);
}

$catid = $this->row->catid();
if ($catid == 0 && $this->defaultCat > 0)
{
	$catid = $this->defaultCat;
}
if ($this->row->catids)
{
	$catid = $this->row->catids;
}
?>
<div id="jevents" <?php
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
echo (!JFactory::getApplication()->isAdmin() && $params->get("darktemplate", 0)) ? "class='jeventsdark'" : "";
?>>
	<form action="<?php echo $action; ?>" method="post" name="adminForm" enctype='multipart/form-data' id="adminForm"   class="form-horizontal" >
		<?php
// get configuration object
		$cfg = & JEVConfig::getInstance();

		JHTML::_('behavior.tooltip');

// these are needed for front end admin
		?>
		<input type="hidden" name="jevtype" value="icaldb" />
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
			echo "</div>";


			if (isset($this->row->_uid))
			{
				?>
				<input type="hidden" name="uid" value="<?php echo $this->row->_uid; ?>" />
				<?php
			}

// need rp_id for front end editing cancel to work note that evid is the repeat id for viewing detail 
			?>
			<input type="hidden" name="rp_id" value="<?php echo isset($this->rp_id) ? $this->rp_id : -1; ?>" /> 
			<input type="hidden" name="year" value="<?php echo $year; ?>" /> 
			<input type="hidden" name="month" value="<?php echo $month; ?>" /> 
			<input type="hidden" name="day" value="<?php echo $day; ?>" /> 

			<input type="hidden" name="state" id="state" value="<?php echo $this->row->state(); ?>" />
			<input type="hidden" name="evid" id="evid" value="<?php echo $this->ev_id; ?>" />
			<input type="hidden" name="valid_dates" id="valid_dates" value="1"  />
			<?php
			if ($this->editCopy)
			{
				?>
				<input type="hidden" name="old_evid" id="old_evid" value="<?php echo $this->old_ev_id; ?>" />
				<?php
			}
			?>
			<script type="text/javascript" language="Javascript">
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
<?php echo $editor->save('jevcontent'); ?>

		try {

			if (!JevrRequiredFields.verify(document.adminForm)){
				return;
			}
		}
		catch (e){

		}
		// do field validation
		if (form.title.value == "") {
			alert ( "<?php echo html_entity_decode(JText::_('JEV_E_WARNTITLE')); ?>" );
		}
		else if (form.catid && form.catid.value==0 && form.catid.options && form.catid.options.length){
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
echo $editor->save('jevcontent');
// Do we have to check for conflicting events i.e. overlapping times etc. BUT ONLY FOR EVENTS INITIALLY
$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("checkclashes", 0) || $params->get("noclashes", 0))
{
	$checkURL = JURI::root() . "components/com_jevents/libraries/checkconflict.php";
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

			</script>
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
			?>
			<div class="adminform" align="left">


				<?php
// Plugins CAN BE LAYERED IN HERE
				global $params;
// append array to extratabs keys content, title, paneid
				$extraTabs = array();
				$dispatcher->trigger('onEventEdit', array(&$extraTabs, &$this->row, &$params), true);

				if (!$cfg->get('com_single_pane_edit', 0))
				{
					?>
					<ul class="nav nav-tabs" id="myEditTabs">
						<li class="active"><a data-toggle="tab" href="#common"><?php echo JText::_("JEV_TAB_COMMON"); ?></a></li>
						<?php
						if (!$cfg->get('com_single_pane_edit', 0) && !$cfg->get('timebeforedescription', 0))
						{
							?>
							<li ><a data-toggle="tab" href="#calendar"><?php echo JText::_("JEV_TAB_CALENDAR"); ?></a></li>
							<?php
						}
						if (!$cfg->get('com_single_pane_edit', 0))
						{
							if (count($extraTabs) > 0)
							{
								foreach ($extraTabs as $extraTab)
								{
									?>
									<li ><a data-toggle="tab" href="#<?php echo $extraTab['paneid'] ?>"><?php echo $extraTab['title']; ?></a></li>
									<?php
								}
							}
						}
						?>
					</ul>
					<?php
				}

// Tabs
				echo JHtml::_('bootstrap.startPane', 'myEditTabs', array('active' => 'common'));
				echo JHtml::_('bootstrap.addPanel', 'myEditTabs', "common");

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
				?>
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('JEV_EVENT_TITLE'); ?>:</label>
					<div class="controls">
						<input class="inputbox" type="text" name="title" size="50" maxlength="255" value="<?php echo JEventsHtml::special($this->row->title()); ?>" />
					</div>
					<?php
					$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
					$showpriority = $params->get("showpriority", 0);
					if ($this->setPriority && $showpriority)
					{
						?>
						<label class="control-label"><?php echo JText::_('JEV_EVENT_PRIORITY'); ?>:</label>
						<div class="controls">
							<?php echo $this->priority; ?>
						</div>
						<?php
					}
					else
					{
						?>
						<input type="hidden" name="priority" value="0" />
					<?php } ?>
				</div>
				<?php
				if (isset($this->users))
				{
					?>
					<div class="control-group jevcreator">
						<label class="control-label"><?php echo JText::_('JEV_EVENT_CREATOR'); ?>:</label>
						<div class="controls">
							<?php echo $this->users; ?>
						</div>
					</div>			
				<?php } ?>
				<div class="control-group">
					<?php
					if ($native && $this->clistChoice)
					{
						?>
						<label class="control-label"><?php echo JText::_('SELECT_ICAL'); ?>:</label>
						<div class="controls">
							<script type="text/javascript" language="Javascript">
								function preselectCategory(select){
									var lookup = new Array();
									lookup[0]=0;
	<?php
	foreach ($this->nativeCals as $nc)
	{
		echo 'lookup[' . $nc->ics_id . ']=' . $nc->catid . ';';
	}
	?>
			document.adminForm['catid'].value=lookup[select.value];
		}
							</script>
						</div>
						<div class="controls">
							<?php echo $this->clist; ?>
						</div>
						<?php
					}
					else if ($this->clistChoice)
					{
						?>
						<label class = "control-label"><?php echo JText::_('SELECT_ICAL'); ?>:</label>
						<div class="controls">
							<?php echo $this->clist; ?>
						</div>
						<?php
					}
					else
					{
						?>
						<div class="controls">
							<?php echo $this->clist; ?>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if (isset($this->offerlock) && $this->offerlock == 1)
				{
					?>
					<div class="control-group">
						<label class="control-label"><?php echo JText::_("JEV_LOCK_EVENT"); ?></label>
						<div class="controls radio btn-group">
							<label class="radio btn"  for="lockevent1">
								<?php echo JText::_("JEV_YES"); ?>
								<input type="radio" name="lockevent" id="lockevent1" value="1" <?php echo ($this->row->lockevent() ? "checked='checked'" : "") ?> />
							</label>
							<label class="radio btn" for="lockevent0">
								<?php echo JText::_("JEV_NO"); ?>
								<input type="radio" name="lockevent" id="lockevent0" value="0" <?php echo (!$this->row->lockevent() ? "checked='checked'" : ""); ?> />
							</label>
						</div>					
					</div>					
					<?php
				}
				?>
				<div class="control-group">
					<?php
					if ($this->repeatId == 0)
					{
						?>
						<label class="control-label jevcategory"><?php echo JText::_("JEV_EVENT_CATEGORY"); ?></label>
						<div class="controls jevcategory">
							<?php
							echo JEventsHTML::buildCategorySelect($catid, 'id="catid" ', $this->dataModel->accessibleCategoryList(), $this->with_unpublished_cat, true, 0, 'catid', JEV_COM_COMPONENT, $this->excats, "ordering", true);
							?>
						</div>					
						<?php
					}
					if (isset($this->glist))
					{
						?>
						<label class="control-label accesslevel"><?php echo JText::_("JEV_EVENT_ACCESSLEVEL"); ?></label>
						<div class="controls accesslevel ">
							<?php echo $this->glist; ?>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if (JFactory::getApplication()->isAdmin())
				{
					if ($this->ev_id == 0)
					{
// published by default	
						$this->row->state(1);
					}

					$poptions = array();
					$poptions[] = JHTML::_('select.option', 0, JText::_("JUNPUBLISHED"));
					$poptions[] = JHTML::_('select.option', 1, JText::_("JPUBLISHED"));
					?>
					<div class="control-group jevpublished">
						<label class="control-label "><?php echo JText::_("JSTATUS"); ?></label>
						<div class="controls">
							<?php
							echo JHTML::_('select.genericlist', $poptions, 'state', 'class="inputbox" size="1"', 'value', 'text', $this->row->state());
							?>
						</div>
					</div>
					<?php
				}

				if (($cfg->get('com_calForceCatColorEventForm', 0) == 1) && (!JFactory::getApplication()->isAdmin()))
				{
					$hideColour = true;
				}
				else if ($cfg->get('com_calForceCatColorEventForm', 0) == 2)
				{
					$hideColour = true;
				}
				else
					$hideColour = false;
				if (!$hideColour)
				{
					include_once(JEV_ADMINLIBS . "/colorMap.php");
					?>
					<div class="control-group">
						<label class="control-label "><?php echo JText::_('JEV_EVENT_COLOR'); ?></label>
						<div class="controls">
							<table id="pick1064797275" style="background-color:<?php echo $this->row->color() . ';color:' . JevMapColor($this->row->color()); ?>;border:solid 1px black;">
								<tr>	
									<td  nowrap="nowrap">
										<input type="hidden" id="pick1064797275field" name="color" value="<?php echo $this->row->color(); ?>"/>
										<a id="colorPickButton" name ="colorPickButton" href="javascript:void(0)"  onclick="document.getElementById('fred').style.visibility='visible';"	  style="visibility:visible;color:<?php echo JevMapColor($this->row->color()); ?>;font-weight:bold;"><?php echo JText::_('JEV_COLOR_PICKER'); ?></a>
									</td>
									<td>
										<div style="position:relative;z-index:9999;">
											<iframe id="fred" frameborder="0" src="<?php echo JURI::root() . "administrator/components/" . JEV_COM_COMPONENT . "/libraries/colours.html?id=fred"; ?>" style="position:absolute;width:300px!important;max-width:300px!important;height:250px!important;visibility:hidden;z-index:9999;left:20px;top:-60px;overflow:visible!important;"></iframe>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<?php
				}

				if ($cfg->get('timebeforedescription', 0))
				{
					echo $this->loadTemplate("datetime");
				}
				?>

				<div class="control-group jev_description">
					<label class="control-label ">
						<?php echo JText::_('JEV_EVENT_ACTIVITY'); ?>
					</label>
					<div class="controls" id='jeveditor' >
						<?php
						if ($cfg->get('com_show_editor_buttons'))
						{
							$t_buttons = explode(',', $cfg->get('com_editor_button_exceptions'));
						}
						else
						{
// hide all
							$t_buttons = false;
						}
// parameters : areaname, content, hidden field, width, height, rows, cols
						echo $editor->display('jevcontent', JEventsHtml::special($this->row->content()), "100%", 250, '70', '10', $t_buttons, 'jevcontent', JEV_COM_COMPONENT);
						?>
					</div>
				</div>
				<div class="control-group jeveditlocation" id="jeveditlocation">
					<label class="control-label "><?php echo JText::_('JEV_EVENT_ADRESSE'); ?></label>
					<div class="controls" >
						<?php
						$res = $dispatcher->trigger('onEditLocation', array(&$this->row));
						if (count($res) == 0 || !$res[0])
						{
							?>
							<input class="inputbox" type="text" name="location" size="80" maxlength="120" value="<?php echo JEventsHtml::special($this->row->location()); ?>" />
							<?php
						}
						?>
					</div>
				</div>
				<div class="control-group jev_contact">
					<label class="control-label "><?php echo JText::_('JEV_EVENT_CONTACT'); ?></label>
					<div class="controls" >
						<input class="inputbox" type="text" name="contact_info" size="80" maxlength="120" value="<?php echo JEventsHtml::special($this->row->contact_info()); ?>" />
					</div>
				</div>
				<div class="control-group jev_extrainfo">
					<label class="control-label "><?php echo JText::_('JEV_EVENT_EXTRA'); ?></label>
					<div class="controls" >
						<textarea class="text_area" name="extra_info" id="extra_info" cols="50" rows="4" wrap="virtual" ><?php echo JEventsHtml::special($this->row->extra_info()); ?></textarea>
					</div>
				</div>
				<?php
				foreach ($customfields as $key => $val)
				{
					?>
					<div class="control-group jevplugin_<?php echo $key; ?>">
						<label class="control-label "><?php echo $customfields[$key]["label"]; ?></label>
							<div class="controls" >
								<?php echo $customfields[$key]["input"]; ?>
							</div>
					</div>
					<?php
				}

				if (!$cfg->get('com_single_pane_edit', 0) && !$cfg->get('timebeforedescription', 0))
				{
					echo JHtml::_('bootstrap.endPanel');
					echo JHtml::_('bootstrap.addPanel', "myEditTabs", "calendar");
				}
				if (!$cfg->get('timebeforedescription', 0))
				{
					echo $this->loadTemplate("datetime");
				}


				if (count($extraTabs) > 0)
				{
					foreach ($extraTabs as $extraTab)
					{
						if (!$cfg->get('com_single_pane_edit', 0))
						{
							echo JHtml::_('bootstrap.endPanel');
							echo JHtml::_('bootstrap.addPanel', "myEditTabs", $extraTab['paneid']);
						}
						echo "<div class='jevextrablock'>";
						echo $extraTab['content'];
						echo "</div>";
					}
				}

				if (!$cfg->get('com_single_pane_edit', 0))
				{
					echo JHtml::_('bootstrap.endPanel');
					echo JHtml::_('bootstrap.endPane', 'myEditTabs');
				}
				?>
			</div>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="updaterepeats" value="0"/>
			<input type="hidden" name="task" value="icalevent.edit" />
			<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	</form>
</div>
