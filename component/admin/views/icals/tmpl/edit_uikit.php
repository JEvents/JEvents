<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 3229 2012-01-30 12:06:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

global $task, $catid;
$db = Factory::getDbo();

HTMLHelper::_('jquery.framework');
JEVHelper::script('showon.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

$uEditor = Factory::getUser()->getParam('editor', Factory::getConfig()->get('editor', 'none'));

$editor = \Joomla\CMS\Editor\Editor::getInstance($uEditor);

// clean any existing cache files
$cache = Factory::getCache(JEV_COM_COMPONENT);
$cache->clean(JEV_COM_COMPONENT);
$action = Factory::getApplication()->isClient('administrator') ? "index.php" : "index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . JEVHelper::getItemid();
?>
<div id="jevents">
	<form action="<?php echo $action; ?>" method="post" name="adminForm" accept-charset="UTF-8"
	      enctype="multipart/form-data" id="adminForm" class="form-horizontal">
		<div >

			<?php
			global $task;

			if (isset($this->editItem->ics_id))
			{
				$id       = $this->editItem->ics_id;
				$catid    = $this->editItem->catid;
				$access   = $this->editItem->access;
				$srcURL   = $this->editItem->srcURL;
				$filename = $this->editItem->filename;
				$overlaps = $this->editItem->overlaps;
				$label    = htmlspecialchars($this->editItem->label);
				$icaltype = $this->editItem->icaltype;

				if ($srcURL == "")
				{
					$filemessage = Text::_("COM_JEVENTS_MANAGE_CALENDARS_OVERVIEW_LOADED_FROM_LOCAL_FILE_CALLLED") . " ";
				}
				else
				{
					$filemessage = Text::_('FROM_FILE');
				}
			}
			else
			{
				$id          = 0;
				$catid       = 0;
				$access      = 0;
				$srcURL      = "";
				$filename    = "";
				$overlaps    = 0;
				$label       = "";
				$icaltype    = 2;
				$filemessage = Text::_('FROM_FILE');
			}


			// build the html select list
			$glist = JEventsHTML::buildAccessSelect($access, 'class="gsl-select gsl-form-width-large" size="1"', "", "access");

			$disabled = "";
			echo JEventsHTML::buildScriptTag('start');
			// leave this as submit button since our submit buttons use the old functional form
			?>
			function submitbutton(pressbutton) {
			if (pressbutton.substr(0, 10) == 'icals.list') {
			Joomla.submitform( pressbutton );
			return;
			}

			var form = document.adminForm;
			catid = form.catid.value;
			icsid = form.icsid.value;

			if (icsid == "0" && catid != "0") {
			// replace the input
			form.catid.setAttribute("name", "catid");
			}

			if (catid == "0"){
			alert( "<?php echo html_entity_decode(Text::_('JEV_E_WARNCAT')); ?>" );
			return(false);
			} else {
			//alert('about to submit the form');
			Joomla.submitform(pressbutton);
			}
			}
			<?php
			echo JEventsHTML::buildScriptTag('end');

			?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_("Unique_Identifier"); ?>
				</div>
				<div class="controls">
					<input class="gsl-input gsl-form-width-large" type="text" name="icsLabel" id="icsLabel" value="<?php echo $label; ?>"
					       size="80"/>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_("JEV_CALENDAR_OWNER"); ?>
				</div>
				<div class="controls">
					<?php echo $this->users; ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('JEV_EVENT_ACCESSLEVEL'); ?>
				</div>
				<div class="controls">
					<?php echo $glist; ?>
				</div>
			</div>

			<?php
			if (!isset($this->editItem->createnewcategories) || $this->editItem->createnewcategories == 0)
			{
				$checked0 = ' checked="checked"';
				$checked1 = '';
			}
			else
			{
				$checked1 = ' checked="checked"';
				$checked0 = '';
			}
			?>
			<div class="control-group">
				<div class="control-label">
					<label title="" class="hasTip" for="createnewcategories"
					       id="createnewcategories-lbl"><?php echo Text::_('JEV_CREATE_NEW_IMPORTED_CATEGORIES'); ?></label>
				</div>
				<div class="controls">
					<fieldset class="radio gsl-button-group" id="createnewcategories">
						<input id="createnewcategories0" type="radio" class='gsl-hidden' value="0"
						       name="createnewcategories" <?php echo $checked0; ?>/>
						<label for="createnewcategories0" class="gsl-button <?php echo empty($checked0) ? "gsl-button-default" : "gsl-button-danger";?> gsl-button-small"><?php echo Text::_('JEV_NO'); ?></label>
						<input id="createnewcategories1" type="radio" class='gsl-hidden' value="1"
						       name="createnewcategories" <?php echo $checked1; ?>/>
						<label for="createnewcategories1" class="gsl-button <?php echo empty($checked1) ? "gsl-button-default" : "gsl-button-primary";?> gsl-button-small"><?php echo Text::_('JEV_YES'); ?></label>
					</fieldset>
				</div>
			</div>

			<div class="control-group"   data-showon-gsl='[{"field":"createnewcategories","values":["0"],"sign":"=","op":""}]' >
				<div class="control-label">
					<strong><?php echo Text::_("JEV_FALLBACK_CATEGORY"); ?></strong><br>
					<?php echo Text::_("JEV_FALLBACK_CATEGORY_2"); ?>
				</div>
				<div class="controls">
					<?php echo JEventsHTML::buildCategorySelect($catid, "", null, $this->with_unpublished_cat, true, 0, 'catid'); ?>
				</div>
			</div>

			<?php
			if (!isset($this->editItem->ignoreembedcat) || $this->editItem->ignoreembedcat == 0)
			{
				$checked0 = ' checked="checked"';
				$checked1 = '';
			}
			else
			{
				$checked1 = ' checked="checked"';
				$checked0 = '';
			}
			?>
			<div class="control-group"  data-showon-gsl='[{"field":"createnewcategories","values":["0"],"sign":"=","op":""}]' >
				<div class="control-label">
					<label title="" class="hasTip" for="ignoreembedcat"
					       id="ignoreembedcat-lbl"><?php echo Text::_('JEV_IGNORE_EMBEDDED_CATEGORIES'); ?></label>
				</div>
				<div class="controls">
					<fieldset class="radio gsl-button-group" id="ignoreembedcat">
						<input id="ignoreembedcat0" type="radio" class='gsl-hidden' value="0"
						       name="ignoreembedcat" <?php echo $checked0; ?>/>
						<label for="ignoreembedcat0" class="gsl-button <?php echo empty($checked0) ? "gsl-button-default" : "gsl-button-danger";?> gsl-button-small"><?php echo Text::_('JEV_NO'); ?></label>
						<input id="ignoreembedcat1" type="radio" class='gsl-hidden' value="1"
						       name="ignoreembedcat" <?php echo $checked1; ?>/>
						<label for="ignoreembedcat1" class="gsl-button <?php echo empty($checked1) ? "gsl-button-default" : "gsl-button-primary";?> gsl-button-small"><?php echo Text::_('JEV_YES'); ?></label>
					</fieldset>
				</div>
			</div>

			<?php if ($id == 0) { ?>
				<ul class="config gsl-tab  gsl-width-auto " id="myicalTabs" gsl-tab="connect: #jvts-ical-tabs">
					<li class="active"><a href="#from_scratch"><?php echo Text::_("FROM_SCRATCH"); ?></a></li>
					<li><a href="#from_file"><?php echo Text::_("FROM_FILE"); ?></a></li>
					<li><a href="#from_url"><?php echo Text::_("FROM_URL"); ?></a></li>
				</ul>
				<?php
			}
			// Tabs
			?>
			<!-- Tabs themselves //-->
			<div class=" gsl-margin-remove gsl-card-body gsl-card-default gsl-padding gsl-width-expand">
				<?php
				if ($id == 0)
				{
					?>
				<ul class="gsl-switcher" id="jvts-ical-tabs">
					<?php
				}
					if ($id == 0 || $icaltype == 2)
					{
						if ($id == 0)
						{
					?>
					<li>
						<?php
						}
						if (!isset($this->editItem->isdefault) || $this->editItem->isdefault == 0)
						{
							$checked0 = ' checked="checked"';
							$checked1 = '';
						}
						else
						{
							$checked1 = ' checked="checked"';
							$checked0 = '';
						}
						if (!isset($this->editItem->overlaps) || $this->editItem->overlaps == 0)
						{
							$overlaps0 = ' checked="checked"';
							$overlaps1 = '';
						}
						else
						{
							$overlaps1 = ' checked="checked"';
							$overlaps0 = '';
						}
						?>
						<div class="control-group">
							<div class="control-label">
								<?php echo Text::_("JEV_EVENT_ISDEFAULT"); ?>
							</div>
							<div class="controls">
								<fieldset class="radio gsl-button-group" id="ignoreembedcat">
									<input id="isdefault0" type="radio" class='gsl-hidden'value="0"
									       name="isdefault" <?php echo $checked0; ?>/>
									<label for="isdefault0" class="gsl-button <?php echo empty($checked0) ? "gsl-button-default" : "gsl-button-danger";?> gsl-button-small"><?php echo Text::_('JEV_NO'); ?></label>
									<input id="isdefault1" type="radio" class='gsl-hidden'value="1"
									       name="isdefault" <?php echo $checked1; ?>/>
									<label for="isdefault1" class="gsl-button <?php echo empty($checked1) ? "gsl-button-default" : "gsl-button-primary";?> gsl-button-small"><?php echo Text::_('JEV_YES'); ?></label>
								</fieldset>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
								<?php echo Text::_("JEV_BLOCK_OVERLAPS"); ?>
							</div>
							<div class="controls">
								<fieldset class="radio gsl-button-group" id="ignoreembedcat">
									<input id="overlaps0" type="radio" class='gsl-hidden'value="0"
									       name="overlaps" <?php echo $overlaps0; ?>/>
									<label for="overlaps0" class="gsl-button <?php echo empty($checked0) ? "gsl-button-default" : "gsl-button-danger";?> gsl-button-small"><?php echo Text::_('JEV_NO'); ?></label>
									<input id="overlaps1" type="radio" class='gsl-hidden'value="1"
									       name="overlaps" <?php echo $overlaps1; ?>/>
									<label for="overlaps1" class="gsl-button <?php echo empty($checked1) ? "gsl-button-default" : "gsl-button-primary";?> gsl-button-small"><?php echo Text::_('JEV_YES'); ?></label>
								</fieldset>
							</div>
						</div>


						<?php
						if ($id == 0)
						{
							?>
							<button name="newical" title="Create New"
							        onclick="submitbutton('icals.new');return false;"><?php echo Text::_("CREATE_FROM_SCRATCH"); ?></button>
							<?php
						}
						}

						if ($id == 0 || $icaltype == 1)
						{
							if ($id == 0)
							{
								?>
						</li>
						<li>
							<h3><?php echo $filename; ?></h3>
							<input class="gsl-input gsl-form-width-large" type="file" name="upload" id="upload" size="80"/><br/><br/>
							<button name="loadical" title="Load Ical"
							        onclick="var icalfile=document.getElementById('upload').value;if (icalfile.length==0)return false; else submitbutton('icals.save');return false;"><?php echo Text::_('LOAD_ICAL_FROM_FILE'); ?></button>
							<?php
							}
						}

						if ($id == 0 || $icaltype == 0)
						{
							if ($id == 0)
							{
						?>
					</li>
					<li>
						<?php
						}
						$urlsAllowed = ini_get("allow_url_fopen");
						if (!$urlsAllowed && !is_callable("curl_exec"))
						{
							echo "<h3>" . Text::_("JEV_ICAL_IMPORTDISABLED") . "</h3>";
							echo "<p>" . Text::_("JEV_SAVEFILELOCALLY") . "</p>";
							$disabled = "disabled";
						}
						else
						{
							$disabled = "";
						}

						if (!isset($this->editItem->autorefresh) || $this->editItem->autorefresh == 0)
						{
							$checked0 = ' checked="checked"';
							$checked1 = '';
						}
						else
						{
							$checked1 = ' checked="checked"';
							$checked0 = '';
						}
						?>

						<div class="control-group">
							<div class="control-label">
								<?php echo Text::_("JEV_EVENT_AUTOREFRESH"); ?>
							</div>
							<div class="controls">
								<fieldset class="radio gsl-button-group" id="ignoreembedcat">
									<input id="autorefresh0" type="radio" class='gsl-hidden'value="0" name="autorefresh" <?php echo $checked0; ?>/>
									<label for="autorefresh0" class="gsl-button <?php echo empty($checked0) ? "gsl-button-default" : "gsl-button-danger";?> gsl-button-small"><?php echo Text::_('JEV_NO'); ?></label>
									<input id="autorefresh1" type="radio" class='gsl-hidden'value="1"
									       name="autorefresh" <?php echo $checked1; ?>/>
									<label for="autorefresh1" class="gsl-button <?php echo empty($checked1) ? "gsl-button-default" : "gsl-button-primary";?> gsl-button-small"><?php echo Text::_('JEV_YES'); ?></label><br/><br/>
								</fieldset>
							</div>
						</div>

						<input class="gsl-input gsl-form-width-large" type="text" name="uploadURL" id="uploadURL" <?php echo $disabled; ?>
						       size="120"
						       value="<?php echo $srcURL; ?>"/><br/><br/>
						<?php if ($id == 0) { ?>
							<button name="loadical" title="Load Ical" <?php echo $disabled; ?>
							        onclick="var icalfile=document.getElementById('uploadURL').value;if (icalfile.length==0)return false; else submitbutton('icals.save');return false;"><?php echo Text::_('LOAD_ICAL_FROM_URL'); ?></button>
							<?php
						}
						}
						if ($id == 0)
						{
						?>
					</li>
				</ul>
				<?php
				}
				?>
				<input type="hidden" name="icsid" id="icsid" <?php echo $disabled; ?> value="<?php echo $id; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
				<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
				<input type="hidden" name="task" value="icals.edit"/>
				<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
			</div>
		</div>
	</form>
</div>
