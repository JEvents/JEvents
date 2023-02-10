<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

if (defined("EDITING_JEVENT"))
	return;
define("EDITING_JEVENT", 1);

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Component\ComponentHelper;

$app    = Factory::getApplication();
$input  = $app->input;
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
// get configuration object
$cfg   = JEVConfig::getInstance();
$assoc = false && Associations::isEnabled() && $app->isClient('administrator');

// Load Bootstrap
JevHtmlBootstrap::framework();
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.calendar');
//HTMLHelper::_('behavior.formvalidation');
if ($params->get("bootstrapchosen", 1))
{
	$jversion = new Joomla\CMS\Version;
	if (!$jversion->isCompatible('4.0'))
	{
		HTMLHelper::_('formbehavior.chosen', '#jevents select:not(.notchosen)');
	}
	// Use this as a basis for setting the primary category
	/*
	$script = <<< SCRIPT
window.setTimeout(function() {
	jQuery("#catid").chosen().change(
		function() {
			if (jQuery("#catid_chzn li.search-choice")) {
				jQuery("#catid_chzn li.search-choice").on('mousedown', function() {
					alert(this);
					return true;
				});
			}
		}
	);
}, 1000);
SCRIPT;
	Factory::getDocument()->addScriptDeclaration($script);
	 */
}
JevHtmlBootstrap::loadCss();

// use Route to preseve language selection
$action = $app->isClient('administrator') ? "index.php" : Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . JEVHelper::getItemid());
?>
	<div id="jevents" <?php
	echo ($app->isClient('site') && $params->get("darktemplate", 0)) ? "class='jeventsdark'" : "";
	?> >
	<div id="jevents_body">
		<form action="<?php echo $action; ?>" method="post" name="adminForm" enctype='multipart/form-data'
		      id="adminForm" class="form-horizontal jevbootstrap">
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
					echo "<h3>" . Text::_('YOU_ARE_EDITING_A_COPY_ON_AN_ICAL_EVENT') . "</h3>";
				}
				else if ($this->repeatId == 0)
				{
					$repeatStyle = "";
					// Don't show warning for new events
					if ($this->ev_id > 0)
					{
						echo Text::_('YOU_ARE_EDITING_AN_ICAL_EVENT');
					}
				}
				else
				{
					$repeatStyle = "style='display:none;'";
					?>
					<h3><?php echo Text::_('YOU_ARE_EDITING_AN_ICAL_REPEAT'); ?></h3>
					<input type="hidden" name="cid[]" value="<?php echo $this->rp_id; ?>"/>
					<?php
				}
				?>
			</div>
			<?php

			if ($params->get("checkconflicts", 0))
			{
				?>
				<div id='jevoverlapwarning'>
					<div><?php echo Text::_("JEV_OVERLAPPING_EVENTS_WARNING"); ?></div>
					<?php
					// event deletors get the right to override this
					if (JEVHelper::isEventPublisher(true) && Text::_("JEV_OVERLAPPING_EVENTS_OVERRIDE") != "JEV_OVERLAPPING_EVENTS_OVERRIDE")
					{
						?>
						<div>
							<strong>
								<label><?php echo Text::_("JEV_OVERLAPPING_EVENTS_OVERRIDE"); ?>
									<!-- not checked by default !!! //-->
									<input type="checkbox" name="overlapoverride" value="1"/>
								</label>
							</strong>
						</div>
						<?php
					}
					?>
					<div id="jevoverlaps"></div>
				</div>
				<?php
			}
			?>
			<div id='jevoverlaprepeatwarning'>
				<div><?php echo Text::_("JEV_CHECK_OVERLAPPING_REPEATS"); ?></div>
				<div>
					<strong>
						<label><?php echo Text::_("JEV_OVERLAPPING_REPEATS_OVERRIDE"); ?>
							<!-- not checked by default !!! //-->
							<input type="checkbox" name="overlaprepeatoverride" value="1"/>
						</label>
					</strong>
				</div>
			</div>
			<?php

			$native = true;
			if ($this->row->icsid() > 0)
			{
				$thisCal = $this->dataModel->queryModel->getIcalByIcsid($this->row->icsid());
				if (isset($thisCal) && $thisCal->icaltype == 0)
				{
					// Note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
					echo Text::_("JEV_IMPORT_WARNING");
					$native = false;
				}
				else if (isset($thisCal) && $thisCal->icaltype == 1)
				{
					// Note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
					echo Text::_("JEV_IMPORT_WARNING2");
					$native = false;
				}
			}

			$this->searchtags[]  = "{{MESSAGE}}";
			$output              = ob_get_clean();
			$this->replacetags[] = $output;
			echo $output;
			$this->blanktags[] = "";

			ob_start();
			if (isset($this->row->_uid))
			{
				?>
				<input type="hidden" name="uid" value="<?php echo $this->row->_uid; ?>"/>
				<?php
			}

			// need rp_id for front end editing cancel to work note that evid is the repeat id for viewing detail
			// I need $year,$month,$day So that I can return to an appropriate date after saving an event (the repetition ids have all changed so I can't go back there!!)
			list($year, $month, $day) = JEVHelper::getYMD();
			?>
			<input type="hidden" name="jevtype" value="icaldb"/>
			<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
			<input type="hidden" name="updaterepeats" value="0"/>
			<input type="hidden" name="task" value="<?php echo $input->getCmd("task", "icalevent.edit"); ?>"/>
			<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
			<input type="hidden" name="rp_id" value="<?php echo isset($this->rp_id) ? $this->rp_id : -1; ?>"/>
			<input type="hidden" name="year" value="<?php echo $year; ?>"/>
			<input type="hidden" name="month" value="<?php echo $month; ?>"/>
			<input type="hidden" name="day" value="<?php echo $day; ?>"/>
			<input type="hidden" name="evid" id="evid" value="<?php echo $this->ev_id; ?>"/>
			<input type="hidden" name="valid_dates" id="valid_dates" value="1"/>
			<?php if ($app->isClient('site')) { ?>
				<input type="hidden" name="Itemid" id="Itemid" value="<?php echo JEVHelper::getItemid(); ?>"/>
			<?php } ?>
			<?php
			if ($this->editCopy)
			{
				?>
				<input type="hidden" name="old_evid" id="old_evid" value="<?php echo $this->old_ev_id; ?>"/>
				<?php
			}
			?>
			<script type="text/javascript">
				<?php
				if (!empty($this->requiredtags))
				{
					foreach ($this->requiredtags as $tag)
					{
						echo "JevStdRequiredFields.fields.push({'name':'" . $tag['id'] . "', 'default' :'" . $tag['default_value'] . "' ,'reqmsg':'" . $tag['alert_message'] . "'});\n";
					}
				}
				?>

                Joomla.submitbutton = function (pressbutton) {
                    if (pressbutton.substr(0, 6) == 'cancel' || !(pressbutton == 'icalevent.save' || pressbutton == 'icalrepeat.save' || pressbutton == 'icalevent.savenew' || pressbutton == 'icalrepeat.savenew' || pressbutton == 'icalevent.apply' || pressbutton == 'icalrepeat.apply')) {
                        if (document.adminForm['catid']) {
                            // restore catid to input value
                            document.adminForm['catid'].value = 0;
                            document.adminForm['catid'].disabled = true;
                        }
                        Joomla.submitform(pressbutton);
                        return;
                    }
                    var form = document.adminForm;
                    var editorElement = jevjq('#jevcontent');
                    if (editorElement.length) {
						<?php
						$editorcontent = $this->editor->save('jevcontent');
						if (!$editorcontent ) {
						// These are problematic editors like JCKEditor that don't follow the Joomla coding patterns !!!
						$editorcontent = $this->editor->getContent('jevcontent');
						echo "var editorcontent =" . $editorcontent . "\n";
						?>
                        try {
                            jevjq('#jevcontent').html(editorcontent);
                        }
                        catch (e) {
                        }
						<?php
						}
						echo $editorcontent;
						?>
                    }
                    try {
                        if (!JevStdRequiredFields.verify(document.adminForm)) {
                            return;
                        }
                        if (!JevrRequiredFields.verify(document.adminForm)) {
                            return;
                        }
                    }
                    catch (e) {

                    }
                    // do field validation
                    if (form.catid && form.catid.value == 0 && form.catid.options && form.catid.options.length) {
                        alert('<?php echo Text::_('JEV_SELECT_CATEGORY', true); ?>');
                    }
                    else if (form.ics_id.value == "0") {
                        alert("<?php echo html_entity_decode(Text::_('JEV_MISSING_ICAL_SELECTION', true)); ?>");
                    }
                    else if (form.valid_dates.value == "0") {
                        alert("<?php echo Text::_("JEV_INVALID_DATES", true); ?>");
                    }
                    else {

                        if (editorElement.length) {
							<?php
							// in case editor is toggled off - needed for TinyMCE
							echo $this->editor->save('jevcontent');
							?>
                        }
						<?php
						// Do we have to check for conflicting events i.e. overlapping times etc. BUT ONLY FOR EVENTS INITIALLY
						$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	                    if (  $params->get("checkconflicts", 0) ||  $params->get("checkoverlappingrepeats", 1) )
						{
						$checkURL = Uri::root() . "components/com_jevents/libraries/checkconflict.php";
						$urlitemid = JEVHelper::getItemid() > 0 ? "&Itemid=" . JEVHelper::getItemid() : "";
						$checkURL = Route::_("index.php?option=com_jevents&ttoption=com_jevents&typeaheadtask=gwejson&file=checkconflict&token=" . Session::getFormToken() . $urlitemid, false);
						?>
                        // reformat start and end dates  to Y-m-d format
                        reformatStartEndDates();
                        checkConflict('<?php echo $checkURL; ?>', pressbutton, '<?php echo Session::getFormToken(); ?>', '<?php echo Factory::getApplication()->isClient('administrator') ? 'administrator' : 'site'; ?>', <?php echo $this->repeatId; ?>);
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
                };

                function submit2(pressbutton) {
                    // sets the date for the page after save
                    resetYMD();
                    Joomla.submitform(pressbutton);
                }

                //-->
			</script>

			<?php
			$this->searchtags[]  = "{{HIDDENINFO}}";
			$output              = ob_get_clean();
			$this->replacetags[] = $output;
			echo $output;
			$this->blanktags[] = "";
			?>

			<div class="adminform form-horizontal">
				<?php
				if (!$cfg->get('com_single_pane_edit', 0))
				{
					?>
					<ul class="nav nav-tabs" id="myEditTabs">
						<li class="active"><a data-toggle="tab"
						                      href="#common"><?php echo Text::_("JEV_TAB_COMMON"); ?></a></li>
						<?php
						if (!$cfg->get('com_single_pane_edit', 0) && !$cfg->get('timebeforedescription', 0))
						{
							?>
							<li><a data-toggle="tab" href="#calendar"><?php echo Text::_("JEV_TAB_CALENDAR"); ?></a>
							</li>
							<?php
						}
						if (!$cfg->get('com_single_pane_edit', 0))
						{
							if (count($this->extraTabs) > 0)
							{
								foreach ($this->extraTabs as $extraTab)
								{
									?>
									<li><a data-toggle="tab"
									       href="#<?php echo $extraTab['paneid'] ?>"><?php echo $extraTab['title']; ?></a>
									</li>
									<?php
								}
							}
						}
						if ($assoc)
						{
							?>
							<li><a data-toggle="tab"
							       href="#associations"><?php echo Text::_('COM_JEVENTS_ITEM_ASSOCIATIONS_FIELDSET_LABEL', true); ?></a>
							</li>
							<?php
						}
						?>
					</ul>
					<?php
					// Tabs
					echo HTMLHelper::_('bootstrap.startTabSet', 'myEditTabs', array('active' => 'common'));
					echo HTMLHelper::_('bootstrap.addTab', 'myEditTabs', "common");
				}
				?>
				<div class="control-group jevtitle">
					<div class="control-label span3">
						<?php echo $this->form->getLabel("title"); ?>
					</div>
					<div class="span9">
						<?php echo str_replace("/>", " data-placeholder='xx' />", $this->form->getInput("title")); ?>
					</div>
				</div>
				<?php
				if ($this->form->getInput("priority"))
				{
					?>
					<div class="control-group jevpriority">
						<div class="control-label span3">
							<?php echo $this->form->getLabel("priority"); ?>
						</div>
						<div class="span9">
							<?php echo $this->form->getInput("priority"); ?>
						</div>
					</div>
					<?php
				}
				?>
				<?php
				if ($this->form->getInput("creator"))
				{
					?>
					<div class="control-group jevcreator">
						<div class="control-label span3">
							<?php echo $this->form->getLabel("creator"); ?>
						</div>
						<div class="span9">
							<?php echo $this->form->getInput("creator"); ?>
						</div>
					</div>
					<?php
				}

				if ($this->form->getInput("ics_id"))
				{
					?>
					<div class="control-group jevcalendar">
						<div class="control-label span3">
							<?php echo $this->form->getLabel("ics_id"); ?>
						</div>
						<div class="span9">
							<?php echo $this->form->getInput("ics_id"); ?>
						</div>
					</div>
					<?php
				}

				if ($this->form->getInput("lockevent"))
				{
					?>
					<div class="control-group jevlockevent">
						<div class="control-label span3">
							<?php echo $this->form->getLabel("lockevent"); ?>
						</div>
						<div class="span9 radio btn-group">
							<?php echo $this->form->getInput("lockevent"); ?>
						</div>
					</div>
					<?php
				}

				if ($this->form->getLabel("catid"))
				{
					?>
					<div class="control-group  jevcategory">
						<?php
						if ($this->form->getLabel("catid"))
						{
							?>
							<div class="control-label span3">
								<?php
								echo $this->form->getLabel("catid");
								?>
							</div>
							<div class="span9 jevcategory">
								<?php echo $this->form->getInput("catid"); ?>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				if ($this->form->getLabel("access"))
				{
					?>
					<div class="control-group  jevaccess">
						<?php
						if ($this->form->getLabel("access"))
						{
							?>
							<div class="control-label span3">
								<?php
								echo $this->form->getLabel("access");
								?>
							</div>
							<div class="span9 accesslevel ">
								<?php echo $this->form->getInput("access"); ?>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}

				if ($this->form->getLabel("state"))
				{
					?>
					<div class="control-group jevpublished">
						<div class="control-label span3">
							<?php echo $this->form->getLabel("state"); ?>
						</div>
						<div class="span9">
							<?php echo $this->form->getInput("state"); ?>
						</div>
					</div>
					<?php
				}
				else
				{
					// hidden field!
					echo $this->form->getInput("state");
				}

				if ($this->form->getInput("color"))
				{
					?>
					<div class="control-group jevcolour">
						<div class="control-label span3">
							<?php echo $this->form->getLabel("color"); ?>
						</div>
						<div class="span9">
							<?php echo $this->form->getInput("color"); ?>
						</div>
					</div>
					<?php
				}

				if ($cfg->get('timebeforedescription', 0))
				{
					ob_start();
					echo $this->loadTemplate("datetime");
					$this->searchtags[]  = "{{CALTAB}}";
					$output              = ob_get_clean();
					$this->replacetags[] = $output;
					echo $output;
					$this->blanktags[] = "";
				}
				?>

				<div class="control-group jev_description">
					<div class="control-label span3">
						<?php echo $this->form->getLabel("jevcontent"); ?>
					</div>
					<div class="span9" id='jeveditor'>
						<?php echo $this->form->getInput("jevcontent"); ?>
					</div>
				</div>
				<div class="control-group jeveditlocation" id="jeveditlocation">
					<div class="control-label span3">
						<?php echo $this->form->getLabel("location"); ?>
					</div>
					<div class="span9">
						<?php echo $this->form->getInput("location"); ?>
					</div>
				</div>
				<div class="control-group jev_contact">
					<div class="control-label span3">
						<?php echo $this->form->getLabel("contact_info"); ?>
					</div>
					<div class="span9">
						<?php echo $this->form->getInput("contact_info"); ?>
					</div>
				</div>
				<div class="control-group jev_extrainfo">
					<div class="control-label span3">
						<?php echo $this->form->getLabel("extra_info"); ?>
					</div>
					<div class="span9">
						<?php echo $this->form->getInput("extra_info"); ?>
					</div>
				</div>

				<?php
				foreach ($this->customfields as $key => $val)
				{
					// skip custom fields that are already displayed on other tabs
					if (isset($val["group"]) && $val["group"] != "default")
					{
						continue;
					}

					?>
					<div class="control-group jevplugin_<?php echo $key; ?>">
						<div class="control-label span3">
							<label><?php echo $this->customfields[$key]["label"]; ?></label>
						</div>
						<div class="span9">
							<?php echo $this->customfields[$key]["input"]; ?>
						</div>
					</div>
					<?php
				}

				if (!$cfg->get('com_single_pane_edit', 0) && !$cfg->get('timebeforedescription', 0))
				{
					echo HTMLHelper::_('bootstrap.endTab');
					echo HTMLHelper::_('bootstrap.addTab', "myEditTabs", "calendar");
				}
				if (!$cfg->get('timebeforedescription', 0))
				{
					ob_start();
					echo $this->loadTemplate("datetime");
					$this->searchtags[]  = "{{CALTAB}}";
					$output              = ob_get_clean();
					$this->replacetags[] = $output;
					echo $output;
					$this->blanktags[] = "";
				}


				if (count($this->extraTabs) > 0)
				{
					foreach ($this->extraTabs as $extraTab)
					{
						if (!$cfg->get('com_single_pane_edit', 0))
						{
							echo HTMLHelper::_('bootstrap.endTab');
							echo HTMLHelper::_('bootstrap.addTab', "myEditTabs", $extraTab['paneid']);
						}
						echo "<div class='jevextrablock'>";
						echo $extraTab['content'];
						echo "</div>";
					}
				}


				if (!$cfg->get('com_single_pane_edit', 0))
				{
					echo HTMLHelper::_('bootstrap.endTab');
					if ($assoc)
					{
						echo HTMLHelper::_('bootstrap.addTab', "myEditTabs", "associations");
						echo $this->loadTemplate('associations');
						echo HTMLHelper::_('bootstrap.endTab');
					}

					echo HTMLHelper::_('bootstrap.endTabSet', 'myEditTabs');
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
	</div>
<?php
$app = Factory::getApplication();
if ($app->isClient('site'))
{
	if ($params->get('com_edit_toolbar', 0) == 1 || $params->get('com_edit_toolbar', 0) == 2)
	{
		//Load the toolbar at the bottom!
		$bar     = JToolBar::getInstance('toolbar');
		$barhtml = $bar->render();
		echo $barhtml;
	}
}
