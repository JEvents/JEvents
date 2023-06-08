<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit_datetime.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

// get configuration object
$cfg = JEVConfig::getInstance();
if ($this->editCopy || $this->repeatId == 0)
{
	$repeatStyle = " class='jeveditrepeats jevdatetime ' ";
}
else
{
	$repeatStyle = "style='display:none;' class='jeveditrepeats jevdatetime' hidden ";
}

// Disable event repeats for non-full editors if disable repeats is enabled
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("disablerepeats", 0) && !JEVHelper::isEventEditor())
{
	$repeatStyle = "style='display:none;' class='jeveditrepeats' ";
}
?>
<div style="clear:both;" class="jevdatetime" <?php  JEventsHelper::showOnRel($this->form, 'allDayEvent');?> >
	<fieldset class="jev_sed">
		<legend><?php echo Text::_("Start_End_Duration"); ?></legend>
		<?php
		if ($params->get("showtimezone", 0))
		{
			?>
			<div style="margin:0px;clear:left;">
				<div class="jevtimezone" <?php JEventsHelper::showOnRel($this->form, 'tzid'); ?>>
					<div class="gsl-width-1-6">
						<?php echo $this->form->getLabel("tzid"); ?>
					</div>
					<div class="gsl-width-5-6">
						<?php echo $this->form->getInput("tzid"); ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>

		<div class=" allDayEvent">
			<div class='alldayinput' style="margin:10px 20px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'allDayEvent'); ?>>
				<div style="display:inline-block;">
					<?php echo $this->form->getLabel("allDayEvent"); ?>
				</div>
				<div style="display:inline-block;">
					<?php echo $this->form->getInput("allDayEvent"); ?>
				</div>
			</div>
			<div class='checkbox12h' style="margin:10px 0px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'view12Hour'); ?>>
				<div style="display:inline-block;">
					<?php echo $this->form->getLabel("view12Hour"); ?>
				</div>
				<div style="display:inline-block;">
					<?php echo $this->form->getInput("view12Hour"); ?>
				</div>
			</div>
		</div>

		<?php
		if ($params->get("disablemultiday", 0))
		{
			?>
			<div style="margin:0px">
				<div class="jevstartdate" style="margin:10px 20px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'publish_up'); ?>>
					<?php echo $this->form->getLabel("publish_up"); ?>
					<?php echo $this->form->getInput("publish_up"); ?>
				</div>

				<div class='jevstarttime' style="margin:10px 0px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'start_time'); ?>>
					<?php echo $this->form->getLabel("start_time"); ?>
					<?php echo $this->form->getInput("start_time"); ?>
				</div>

				<div class='jevendtime' style="margin:10px 20px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'end_time'); ?>>
					<?php echo $this->form->getLabel("end_time"); ?>
					<?php echo $this->form->getInput("end_time"); ?>
				</div>

				<div class='jevnoeendtime' style="margin:10px 0px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'noendtime'); ?>>
					<?php echo $this->form->getLabel("noendtime"); ?>
					<?php echo $this->form->getInput("noendtime"); ?>
				</div>

				<div class="jevenddate" style="display:none" <?php JEventsHelper::showOnRel($this->form, 'publish_down'); ?>>
					<?php echo $this->form->getLabel("publish_down"); ?>
					<?php echo $this->form->getInput("publish_down"); ?>
				</div>

			</div>
			<?php
		}
		else
		{
			?>
			<div style="margin:0px">
				<div class="jevstartdate" style="margin:10px 20px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'publish_up'); ?>>
					<?php echo $this->form->getLabel("publish_up"); ?>
					<?php echo $this->form->getInput("publish_up"); ?>
				</div>

				<div class='jevstarttime' style="margin:10px 0px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'start_time'); ?>>
					<?php echo $this->form->getLabel("start_time"); ?>
					<?php echo $this->form->getInput("start_time"); ?>
				</div>
			</div>

			<div style="margin:0px">
				<div class="jevenddate" style="margin:10px 20px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'publish_down'); ?>>
					<?php echo $this->form->getLabel("publish_down"); ?>
					<?php echo $this->form->getInput("publish_down"); ?>
				</div>

				<div class='jevendtime' style="margin:10px 20px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'end_time'); ?>>
					<?php echo $this->form->getLabel("end_time"); ?>
					<?php echo $this->form->getInput("end_time"); ?>
				</div>

				<div class='jevnoeendtime' style="margin:10px 0px 0px 0px ;display:inline-block;" <?php JEventsHelper::showOnRel($this->form, 'noendtime'); ?>>
					<div style="display:inline-block;">
						<?php echo $this->form->getLabel("noendtime"); ?>
					</div>
					<div style="display:inline-block;">
						<?php echo $this->form->getInput("noendtime"); ?>
					</div>
				</div>

			</div>
			<?php
		}
		?>


		<div id="jevmultiday"
		     style="display:<?php echo $this->row->endDate() > $this->row->startDate() ? "block" : "none"; ?>"
			>

			<label style="font-weight:bold;"><?php echo Text::_('JEV_EVENT_MULTIDAY'); ?></label><br/>
			<div style="float:left;margin-right:20px!important;"><?php echo Text::_('JEV_EVENT_MULTIDAY_LONG') . "&nbsp;"; ?></div>
			<div class="gsl-button-group" >
				<label for="yes" class=" gsl-button gsl-button-small gsl-button-default <?php echo $this->row->multiday() ? ' gsl-button-primary' : ''; ?>">
					<input type="radio" id="yes" name="multiday" class="gsl-hidden"
					       value="1" <?php echo $this->row->multiday() ? 'checked="checked"' : ''; ?>
					       onclick="updateRepeatWarning();"/>
					<?php echo Text::_("JEV_YES"); ?>
				</label>
				<label for="no" class="gsl-button gsl-button-small gsl-button-default <?php echo $this->row->multiday() ? '' : ' gsl-button-danger' ; ?>">
					<input type="radio" id="no" name="multiday"  class="gsl-hidden"
					       value="0" <?php echo $this->row->multiday() ? '' : 'checked="checked"'; ?>
					       onclick="updateRepeatWarning();"/>
					<?php echo Text::_("JEV_NO"); ?>
				</label>
			</div>
		</div>
	</fieldset>
</div>

<div <?php echo $repeatStyle; ?> <?php JEventsHelper::showOnRel($this->form, 'repeattype'); ?>>
	<!-- REPEAT FREQ -->
	<div style="clear:both;">
        <?php
        $freq = strtoupper($this->row->freq());
        ?>
		<fieldset class="gsl-button-group">
			<legend class="gsl-margin-right gsl-text-bold" ><?php echo Text::_('JEV_EVENT_REPEATTYPE'); ?></legend>
			<label for='NONE' class="gsl-button gsl-button-small gsl-button-default gsl-text-nowrap 	<?php echo $freq ==  "NONE" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="NONE" class="gsl-hidden"
				       value="none" <?php if ($freq ==  "NONE") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('NONE');"/>
				<?php echo Text::_('NO_REPEAT'); ?>
			</label>
            <label for='DAILY' class="gsl-button gsl-button-small gsl-button-default gsl-text-nowrap <?php echo $freq ==  "DAILY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="DAILY" class="gsl-hidden"
				       value="DAILY" <?php if ($freq ==  "DAILY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('DAILY');"/>
				<?php echo Text::_('DAILY'); ?>
			</label>
            <label for='WEEKLY' class="gsl-button gsl-button-small gsl-button-default gsl-text-nowrap <?php echo $freq ==  "WEEKLY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="WEEKLY" class="gsl-hidden"
				       value="WEEKLY" <?php if ($freq ==  "WEEKLY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('WEEKLY');"/>
				<?php echo Text::_('WEEKLY'); ?>
			</label>
            <label for='MONTHLY' class="gsl-button gsl-button-small gsl-button-default gsl-text-nowrap <?php echo $freq ==  "MONTHLY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="MONTHLY" class="gsl-hidden"
				       value="MONTHLY" <?php if ($freq ==  "MONTHLY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('MONTHLY');"/>
				<?php echo Text::_('MONTHLY'); ?>
			</label>
            <label for='YEARLY' class="gsl-button gsl-button-small gsl-button-default gsl-text-nowrap <?php echo $freq ==  "YEARLY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="YEARLY" class="gsl-hidden"
				       value="YEARLY" <?php if ($freq ==  "YEARLY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('YEARLY');"/>
				<?php echo Text::_('YEARLY'); ?>
			</label>
			<?php
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("dayselect", 0))
			{
				?>
                <label for='IRREGULAR' class="gsl-button gsl-button-small gsl-button-default gsl-text-nowrap <?php echo $freq ==  "IRREGULAR" ? ' gsl-button-primary' : ''; ?>">
					<input type="radio" name="freq" id="IRREGULAR" value="IRREGULAR" class="gsl-hidden"
					       onclick="toggleFreq('IRREGULAR');" <?php if ($freq ==  "IRREGULAR") echo 'checked="checked"'; ?>/>
					<?php echo Text::_('IRREGULAR'); ?>
				</label>
			<?php } ?>
		</fieldset>
	</div>
	<!-- END REPEAT FREQ-->


	<div id="interval_div">
		<div style="float:left">
			<fieldset>
				<legend><?php echo Text::_('REPEAT_INTERVAL') ?></legend>
				<input class="inputbox gsl-width-small" type="text" name="rinterval" id="rinterval" size="2" maxlength="2"
				       value="<?php echo $this->row->interval(); ?>" onchange="checkInterval();"/><span
						id='interval_label' style="margin-left:1em"></span>
			</fieldset>
		</div>
		<div style="float:left;margin-left:20px!important" id="cu_count">
			<fieldset >
				<legend><input type="radio" name="countuntil" value="count" id="cuc" checked="checked"
				               onclick="toggleCountUntil('cu_count');"/><?php echo Text::_('REPEAT_COUNT') ?></legend>
				<input class="inputbox gsl-width-small" type="text" name="count" id="count" size="3" maxlength="3"
				       value="<?php echo $this->row->count(); ?>" onchange="checkInterval();"/><span id='count_label'
				                                                                                     style="margin-left:1em"><?php echo Text::_('REPEATS'); ?></span>
			</fieldset>
		</div>
		<div style="float:left;margin-left:20px!important;" id="cu_until" class="roundedgrey">
			<fieldset class="roundedgrey">
				<legend class="roundedgrey"><input type="radio" name="countuntil" value="until" id="cuu"
				                                   onclick="toggleCountUntil('cu_until');"/><?php echo Text::_('REPEAT_UNTIL'); ?>
				</legend>
				<?php
				$params          = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$minyear         = JEVHelper::getMinYear();
				$maxyear         = JEVHelper::getMaxYear();
				$inputdateformat = $params->get("com_editdateformat", "d.m.Y");
				JEVHelper::loadElectricCalendar("until", "until", JevDate::strftime("%Y-%m-%d", $this->row->until()), $minyear, $maxyear, 'updateRepeatWarning();', "checkUntil();updateRepeatWarning();", $inputdateformat, array("class" => "gsl-width-small"));
				?>
				<input type="hidden" name="until2" id="until2" value=""/>

			</fieldset>
		</div>
	</div>
	<div style="clear:both;">
		<div id="byyearday">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbyd" value="byd"
				               onclick="toggleWhichBy('byyearday');"/><?php echo Text::_('BY_YEAR_DAY'); ?></legend>
				<div>
					<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
					<input class="inputbox gsl-width-small" type="text" name="byyearday" size="20" maxlength="100"
					       value="<?php echo $this->row->byyearday(); ?>" onchange="checkInterval();"/>
				</div>
				<div class="countback">
					<?php echo Text::_('COUNT_BACK_YEAR'); ?>
					<input type="checkbox" name="byd_direction"  class="gsl-checkbox gsl-margin-left"
					       onclick="fixRepeatDates();" <?php echo $this->row->getByDirectionChecked("byyearday"); ?>/>
				</div>
			</fieldset>
		</div>
		<div id="bymonth">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbm" value="bm"
				               onclick="toggleWhichBy('bymonth');"/><?php echo Text::_('BY_MONTH'); ?></legend>
				<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
				<input class="inputbox gsl-width-small" type="text" name="bymonth" size="30" maxlength="20"
				       value="<?php echo $this->row->bymonth(); ?>" onchange="checkInterval();"/>
			</fieldset>
		</div>
		<div id="byweekno">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbwn" value="bwn"
				               onclick="toggleWhichBy('byweekno');"/><?php echo Text::_('BY_WEEK_NO'); ?></legend>
				<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
				<input class="inputbox gsl-width-small" type="text" name="byweekno" size="20" maxlength="20"
				       value="<?php echo $this->row->byweekno(); ?>" onchange="checkInterval();"/>
				<br/>Count back from year end<input type="checkbox"  class="gsl-checkbox gsl-margin-left"
				                                    name="bwn_direction" <?php echo $this->row->getByDirectionChecked("byweekno"); ?> />
			</fieldset>
		</div>
		<div id="bymonthday">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbmd" value="bmd"
				               onclick="toggleWhichBy('bymonthday');"/><?php echo Text::_('BY_MONTH_DAY'); ?></legend>
				<div>
					<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
					<input class="inputbox gsl-width-small" type="text" name="bymonthday" size="30" maxlength="20"
					       value="<?php echo $this->row->bymonthday(); ?>" onchange="checkInterval();"/>
				</div>
				<div class="countback">
					<?php echo Text::_('COUNT_BACK'); ?><input type="checkbox" name="bmd_direction"  class="gsl-checkbox gsl-margin-left"
					                                           onclick="fixRepeatDates();" <?php echo $this->row->getByDirectionChecked("bymonthday"); ?>/>
				</div>
			</fieldset>
		</div>
		<div id="byday">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbd" value="bd"
				               onclick="toggleWhichBy('byday');"/><?php echo Text::_('BY_DAY'); ?></legend>
				<div class="gsl-button-group">
					<?php
					JEventsHTML::buildWeekDaysCheckUikit($this->row->getByDay_days(), '', "weekdays");
					?>
				</div>
			</fieldset>
			<fieldset id="weekofmonth">
				<legend><?php echo Text::_('WHICH_WEEK'); ?></legend>
				<div class="gsl-button-group">
					<?php
					JEventsHTML::buildWeeksCheckUikit($this->row->getByDay_weeks(), "", "weeknums", $this->row->getByDirection("byday"));
					?>
				</div>
				<div class="countback">
					<?php echo Text::_('COUNT_BACK'); ?>
					<input type="checkbox" class="gsl-checkbox gsl-margin-left" name="bd_direction" <?php echo $this->row->getByDirectionChecked("byday"); ?>
					       onclick="updateRepeatWarning();toggleWeeknumDirection();"/>
				</div>
			</fieldset>
		</div>
		<div id="byirregular">
			<fieldset>
				<legend class="gsl-text-bold"><?php echo Text::_('JEV_SELECT_REPEAT_DATES'); ?></legend>
				<div class="irregularDateSelector">
					<?php
					$params           = ComponentHelper::getParams(JEV_COM_COMPONENT);
					$minyear          = JEVHelper::getMinYear();
					$maxyear          = JEVHelper::getMaxYear();
					$inputdateformat  = $params->get("com_editdateformat", "d.m.Y");
					$inputdateformat2 = str_replace(array("Y", "m", "d"), array("%Y", "%m", "%d"), $inputdateformat);
					$attribs          = array("style" => "display:none;" , "class" => "gsl-width-small");
					$irregulartimes   = $params->get("irregulartimes", 0);
					if ($irregulartimes)
					{
						$attribs["showtime"] = "showtime";
						$inputdateformat     .= " %H:%M";
					}
					?>
					<div class="gsl-display-inline-block gsl-text-top"><?php
					JEVHelper::loadElectricCalendar("irregular", "irregular", "", $minyear, $maxyear, '', "setTimeout(function() {selectIrregularDate();updateRepeatWarning();}, 200)", $inputdateformat, $attribs);
					//JEVHelper::loadElectricCalendar("irregular", "irregular", "", $minyear, $maxyear, '', "jQuery(this).trigger('calupdate');", $inputdateformat, $attribs);
					?>
					</div><select id="irregularDates" name="irregularDates[]" multiple="multiple" size="5"
					        onchange="updateRepeatWarning();" >
						<?php
						sort($this->row->_irregulardates);
						array_unique($this->row->_irregulardates);
						foreach ($this->row->_irregulardates as $irregulardate)
						{
							$irregulardateval  = JevDate::strftime('%Y-%m-%d', $irregulardate);
							$irregulardatetext = JevDate::strftime($inputdateformat2, $irregulardate);
							?>
							<option value="<?php echo $irregulardateval; ?>"
							        selected="selected"><?php echo $irregulardatetext; ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<strong><?php echo Text::_("JEV_IRREGULAR_REPEATS_CANNOT_BE_EXPORTED_AT_PRESENT"); ?></strong>
			</fieldset>
		</div>
		<div class="jev_none" id="bysetpos">
			<fieldset>
				<legend><?php echo "NOT YET SUPPORTED" ?></legend>
			</fieldset>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
<?php
ob_start();
?>
    // make the correct frequency visible
    function setupRepeats() {
        hideEmptyJevTabs();
		<?php
		if ($this->row->id() != 0 && $this->row->freq())
		{
		?>
        var freq = "<?php echo strtoupper($this->row->freq()); ?>";
		if (document.getElementById(freq)) {
           document.getElementById(freq).checked = true;
		}
        toggleFreq(freq, true);
        var by = "<?php
			if ($this->row->byyearday(true) != "")
				echo "jevbyd";
			else if ($this->row->bymonth(true) != "")
				echo "jevbm";
			else if ($this->row->bymonthday(true) != "")
				echo "jevbmd";
			else if ($this->row->byweekno(true) != "")
				echo "jevbwn";
			else if ($this->row->byday(true) != "")
				echo "jevbd";
// default repeat is by day
			else
				echo "jevbd";
			?>";
        document.getElementById(by).checked = true;
        var by = "<?php
			if ($this->row->byyearday(true) != "")
				echo "byyearday";
			else if ($this->row->bymonth(true) != "")
				echo "bymonth";
			else if ($this->row->bymonthday(true) != "")
				echo "bymonthday";
			else if ($this->row->byweekno(true) != "")
				echo "byweekno";
			else if ($this->row->byday(true) != "")
				echo "byday";
			?>";
        toggleWhichBy(by);
        var cu = "cu_<?php
			if ($this->row->rawuntil() != "")
				echo "until";
			else
				echo "count";
			?>";
        document.getElementById(cu == "cu_until" ? "cuu" : "cuc").checked = true;
        toggleCountUntil(cu);

        // Now reset the repeats warning so we can track any changes
        document.adminForm.updaterepeats.value = 0;
        // Now sort out the count back!
        fixRepeatDates(true);
        // Finally release the check on changed repeats
        setupRepeatsRun = true;

		<?php
		}
		?>
    }

document.addEventListener('DOMContentLoaded', function () {
    window.setTimeout(setupRepeats, 500);
    // move to 12h fields
    set12hTime(document.adminForm.start_time);
    set12hTime(document.adminForm.end_time);
    // toggle unvisible time fields
    toggleView12Hour();
});

<?php
$script = ob_get_clean();
Factory::getDocument()->addScriptDeclaration($script);
