<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 3543 2012-04-20 08:17:42Z geraint edwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Component\ComponentHelper;

$app    = Factory::getApplication();
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

if ($app->isClient('administrator') && $params->get("customcssbackend", 0))
{

	JEVHelper::loadCustomCSS();
}

if ($app->isClient('administrator') || $params->get("newfrontendediting", 1) || version_compare(JVERSION, '4.0' , 'lt'))
{
	$editPage = $this->loadTemplate('uikit');
	//$editPage = str_replace("hasPopover", "hasYsPopover", $editPage);
	echo $editPage;
	return;
}

if (defined("EDITING_JEVENT"))
	return;
define("EDITING_JEVENT", 1);

// needed for sortable category IDs
HTMLHelper::script('media/com_jevents/js/Sortable.js', array('version' => JeventsHelper::JEvents_Version(false), 'relative' => false));


$input  = $app->input;
// get configuration object
$cfg   = JEVConfig::getInstance();
$assoc = false && Associations::isEnabled() && Factory::getApplication()->isClient('administrator');

// Load Bootstrap

HTMLHelper::_('behavior.keepalive');
//HTMLHelper::_('behavior.calendar');
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
$action = Factory::getApplication()->isClient('administrator') ? "index.php" : Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . JEVHelper::getItemid());

$user         = Factory::getUser();
$accesslevels = $user->getAuthorisedViewLevels();
$accesslevels = "jeval" . implode(" jeval", array_unique($accesslevels));

?>
	<div id="jevents" <?php
	echo (!Factory::getApplication()->isClient('administrator') && $params->get("darktemplate", 0)) ? "class='jeventsdark $accesslevels'" : "class='$accesslevels' ";
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
						if ($this->row->_freq === "none")
						{
							echo Text::_('YOU_ARE_EDITING_AN_ICAL_EVENT_WITH_NO_REPEATS');
						}
						else
						{
							echo Text::_('YOU_ARE_EDITING_AN_ICAL_EVENT');
						}
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
// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
					echo Text::_("JEV_IMPORT_WARNING");
					$native = false;
				}
				else if (isset($thisCal) && $thisCal->icaltype == 1)
				{
// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
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
			ob_start();

				if (!empty($this->requiredtags))
				{
					foreach ($this->requiredtags as $tag)
					{
						echo "JevStdRequiredFields.fields.push({'name':'" . $tag['id'] . "', 'default' :'" . $tag['default_value'] . "' ,'reqmsg':'" . $tag['alert_message'] . "'});\n";
					}
				}
				?>

                Joomla.submitbutton = function (pressbutton) {
                    if (pressbutton.substr(0, 6) == 'cancel' || !(pressbutton == 'icalevent.save' || pressbutton == 'icalrepeat.save' || pressbutton == 'icalevent.savenew' || pressbutton == 'icalevent.savecopy' || pressbutton == 'icalrepeat.savenew' || pressbutton == 'icalevent.apply' || pressbutton == 'icalrepeat.apply')) {
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
						if (version_compare(JVERSION, '4.0', 'lt'))
						{
							$editorcontent = $this->editor->save('jevcontent');
							echo $editorcontent . "\n";
							// Tiny MCE has changed what onSave method does so we need to use onGetContent
							$getContent = $this->editor->getContent('jevcontent');
							if ($getContent){
							?>
	                        // tinyMCE chooses a random editor so we have to specify the one we want
	                        if (typeof tinyMCE != 'undefined') {
	                            try {
	                                tinyMCE.EditorManager.setActive(tinyMCE.get("jevcontent"));
	                            }
	                            catch (e) {
	                            }
	                        }
							<?php
							echo "var getContent =" . $getContent . "\n";
							?>
	                        try {
	                            jevjq('#jevcontent').html(getContent);
	                        }
	                        catch (e) {
	                        }
							<?php
							}
						}
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
                        var x = e;
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
							if (version_compare(JVERSION, '4.0', 'lt'))
							{
								echo $this->editor->save('jevcontent');
							}
							?>
                        }
						<?php
						// Do we have to check for conflicting events i.e. overlapping times etc. BUT ONLY FOR EVENTS INITIALLY
						$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
						if (  $params->get("checkconflicts", 0) ||  $params->get("checkoverlappingrepeats", 1) )
						{
						$checkURL = Uri::root() . "components/com_jevents/libraries/checkconflict.php";
						$urlitemid = JEVHelper::getItemid() > 0 ? "&Itemid=" . JEVHelper::getItemid() : "";
						$ttitemid = JEVHelper::getItemid() > 0 ? "&ttItemid=" . JEVHelper::getItemid() : "";
						$checkURL = Route::_("index.php?option=com_jevents&ttoption=com_jevents&typeaheadtask=gwejson&file=checkconflict&token=" . Session::getFormToken() . $urlitemid . $ttitemid, false);
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

			<?php
			$script = ob_get_clean();
			if (version_compare(JVERSION,'4.0.0',">="))
			{
				Factory::getDocument()->addScriptDeclaration($script);
			}
			else
            {
                ?>
                <script>
                    <?php echo $script; ?>
                </script>
                <?php
            }

			$this->searchtags[]  = "{{HIDDENINFO}}";
			$output              = ob_get_clean();
			$this->replacetags[] = $output;
			echo $output;
			$this->blanktags[] = "";
			?>

			<div class="adminform">
				<?php
				if (!$cfg->get('com_single_pane_edit', 0))
				{/*
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
									if (trim($extraTab['content']) == "")
									{
										continue;
									}
									if (strpos($extraTab['title'], " ") === false && Text::_($extraTab['title']) != $extraTab['title'])
									{
										$extraTab['title'] = Text::_($extraTab['title']);
									}
									?>
									<li <?php JEventsHelper::showOnRel($this->form, 'catid'); ?>><a data-toggle="tab"
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
				*/
								// Tabs
					echo HTMLHelper::_('bootstrap.startTabSet', 'myEditTabs', array('active' => 'common'));
					echo HTMLHelper::_('bootstrap.addTab', 'myEditTabs', "common", Text::_("JEV_TAB_COMMON"));
				}
				?>
				<div class="row jevtitle" <?php JEventsHelper::showOnRel($this->form, 'title'); ?>>
					<div class="span2">
						<?php echo $this->form->getLabel("title"); ?>
					</div>
					<div class="span10">
						<?php echo str_replace("/>", " data-placeholder='xx' />", $this->form->getInput("title")); ?>
					</div>
				</div>
				<?php
				if ($this->form->getInput("priority"))
				{
					?>
					<div class="row jevpriority" <?php JEventsHelper::showOnRel($this->form, 'priority'); ?>>
						<div class="span2">
							<?php echo $this->form->getLabel("priority"); ?>
						</div>
						<div class="span10">
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
					<div class="row jevcreator" <?php JEventsHelper::showOnRel($this->form, 'creator'); ?>>
						<div class="span2">
							<?php echo $this->form->getLabel("creator"); ?>
						</div>
						<div class="span10">
							<?php echo $this->form->getInput("creator"); ?>
						</div>
					</div>
					<?php
				}

				// This could be hidden!
				if ($this->form->getLabel("ics_id"))
				{
					?>
					<div class="row jevcalendar" <?php JEventsHelper::showOnRel($this->form, 'ics_id'); ?>>
						<div class="span2">
							<?php echo $this->form->getLabel("ics_id"); ?>
						</div>
						<div class="span10">
							<?php echo $this->form->getInput("ics_id"); ?>
						</div>
					</div>
					<?php
				}
				else
				{
					echo $this->form->getInput("ics_id");
				}

				if ($this->form->getInput("lockevent"))
				{
					?>
					<div class="row jevlockevent" <?php JEventsHelper::showOnRel($this->form, 'lockevent'); ?>>
						<div class="span2">
							<?php echo $this->form->getLabel("lockevent"); ?>
						</div>
						<div class="span10 radio btn-group">
							<?php echo $this->form->getInput("lockevent"); ?>
						</div>
					</div>
					<?php
				}

				if ($this->form->getLabel("catid"))
				{
					?>
					<div class="row  jevcategory" <?php JEventsHelper::showOnRel($this->form, 'catid'); ?>>
						<?php
						if ($this->form->getLabel("catid"))
						{
							?>
							<div class="span2">
								<?php
								echo $this->form->getLabel("catid");
								?>
							</div>
							<div class="span10 jevcategory">
								<?php echo $this->form->getInput("catid"); ?>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				/*
							if ($this->form->getLabel("primarycatid"))
							{
								?>
								<div class="row  jevprimarycategory" <?php JEventsHelper::showOnRel($this->form, 'primarycatid'); ?>>
									<?php
									if ($this->form->getLabel("primarycatid"))
									{
										?>
										<div class="span2">
											<?php
											echo $this->form->getLabel("primarycatid");
											?>
										</div>
										<div class="span10 jevprimarycategory">
											<?php echo $this->form->getInput("primarycatid"); ?>
										</div>
										<?php
									}
									?>
								</div>
								<?php
							}
				*/
				if ($this->repeatId === 0 && $this->form->getLabel("access"))
				{
					?>
					<div class="row  jevaccess" <?php JEventsHelper::showOnRel($this->form, 'access'); ?>>
						<?php
						if ($this->form->getLabel("access"))
						{
							?>
							<div class="span2">
								<?php
								echo $this->form->getLabel("access");
								?>
							</div>
							<div class="span10 accesslevel ">
								<?php echo $this->form->getInput("access"); ?>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}

				if ($this->repeatId !== 0)
				{
					//Do Nothing, repeats do not yet have states
				}
				else if ($this->form->getLabel("state"))
				{
					?>
					<div class="row jevpublished" <?php JEventsHelper::showOnRel($this->form, 'state'); ?>>
						<div class="span2">
							<?php echo $this->form->getLabel("state"); ?>
						</div>
						<div class="span10">
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
					<div class="row jevcolour" <?php JEventsHelper::showOnRel($this->form, 'color'); ?>>
						<div class="span2">
							<?php echo $this->form->getLabel("color"); ?>
						</div>
						<div class="span10">
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

				<div class="row jev_description" <?php JEventsHelper::showOnRel($this->form, 'jevcontent'); ?>>
					<div class="span2">
						<?php echo $this->form->getLabel("jevcontent"); ?>
					</div>
					<div class="span10" id='jeveditor'>
						<?php
						// There is a TinyMCE issue in Joomla 3.6 where it loads the javascript twice if we do this
						//echo $this->form->getInput("jevcontent");
						// so instead we use the value we already have in the replacetags
						$index = array_search("{{DESCRIPTION}}", $this->searchtags);
						if ($index !== false)
						{
							echo $this->replacetags[$index];
						}
						?>
					</div>
				</div>
				<div class="row jeveditlocation" id="jeveditlocation" <?php JEventsHelper::showOnRel($this->form, 'location'); ?>>
					<div class="span2">
						<?php echo $this->form->getLabel("location"); ?>
					</div>
					<div class="span10">
						<?php echo $this->form->getInput("location"); ?>
					</div>
				</div>
				<div class="row jev_contact" <?php JEventsHelper::showOnRel($this->form, 'contact_info'); ?>>
					<div class="span2">
						<?php echo $this->form->getLabel("contact_info"); ?>
					</div>
					<div class="span10">
						<?php echo $this->form->getInput("contact_info"); ?>
					</div>
				</div>
				<div class="row jev_extrainfo" <?php JEventsHelper::showOnRel($this->form, 'extra_info'); ?>>
					<div class="span2">
						<?php echo $this->form->getLabel("extra_info"); ?>
					</div>
					<div class="span10">
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
					<div class="row jevplugin_<?php echo $key; ?>" <?php echo (isset($this->customfields[$key]["showon"]) && !empty($this->customfields[$key]["showon"])) ? $this->customfields[$key]["showon"] : JEventsHelper::showOnRel($this->form, 'customfields'); ?>>
						<div class="span2">
							<label><?php echo $this->customfields[$key]["label"]; ?></label>
						</div>
						<div class="span10">
							<?php echo $this->customfields[$key]["input"]; ?>
						</div>
					</div>
					<?php
				}

				/*
                if ($cfg->get('joomlatags', 1))
                {
	                ?>
                    <div class="row jev_joomlatags" <?php JEventsHelper::showOnRel($this->form, 'tags'); ?>>
                        <div class="span2">
			                <?php echo $this->form->getLabel("tags"); ?>
                        </div>
                        <div class="span10">
			                <?php echo $this->form->getInput("tags"); ?>
                        </div>
                    </div>
	                <?php
                }
				*/
                if (!$cfg->get('com_single_pane_edit', 0) && !$cfg->get('timebeforedescription', 0))
				{
					echo HTMLHelper::_('bootstrap.endTab');
					echo HTMLHelper::_('bootstrap.addTab', "myEditTabs", "calendar", Text::_("JEV_TAB_CALENDAR"));
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
						if (trim($extraTab['content']) == "")
						{
							continue;
						}

						if (!$cfg->get('com_single_pane_edit', 0))
						{
							echo HTMLHelper::_('bootstrap.endTab');
							echo HTMLHelper::_('bootstrap.addTab', "myEditTabs", $extraTab['paneid'], $extraTab['title']);
						}
						ob_start();
						JEventsHelper::showOnRel($this->form, 'jevcontent');
						$showon = ob_get_clean();
						echo "<div class='jevextrablock' " .  $showon . ">";
						echo $extraTab['content'];
						echo "</div>";
					}
				}


				if (!$cfg->get('com_single_pane_edit', 0))
				{
					echo HTMLHelper::_('bootstrap.endTab');
					if ($assoc)
					{
						echo HTMLHelper::_('bootstrap.addTab', "myEditTabs", "associations"), Text::_('COM_JEVENTS_ITEM_ASSOCIATIONS_FIELDSET_LABEL', true);
						echo $this->loadTemplate('associations');
						echo HTMLHelper::_('bootstrap.endTab');
					}

					echo HTMLHelper::_('bootstrap.endTabSet', 'myEditTabs');
				}
				?>
			</div>
			<?php
			$output = ob_get_clean();
			$app    = Factory::getApplication();
			if (($app->isClient('administrator') && $cfg->get('ignorelayout', 0)) || !$this->loadEditFromTemplate('icalevent.edit_page', $this->row, 0, $this->searchtags, $this->replacetags, $this->blanktags))
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
