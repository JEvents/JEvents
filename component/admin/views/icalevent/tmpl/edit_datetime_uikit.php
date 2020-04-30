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
	$repeatStyle = "style='display:none;' class='jeveditrepeats jevdatetime' ";
}

// Disable event repeats for non-full editors if disable repeats is enabled
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("disablerepeats", 0) && !JEVHelper::isEventEditor())
{
	$repeatStyle = "style='display:none;' class='jeveditrepeats' ";
}
?>
<div style="clear:both;" class="jevdatetime">
	<fieldset class="jev_sed">
		<legend><?php echo Text::_("Start_End_Duration"); ?></legend>
		<?php
		if ($params->get("showtimezone", 0))
		{
			?>
			<div style="margin:0px;clear:left;">
				<div class="row jevtimezone" <?php JEventsHelper::showOnRel($this->form, 'tzid'); ?>>
					<div class="span2">
						<?php echo $this->form->getLabel("tzid"); ?>
					</div>
					<div class="span10">
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
			<legend><?php echo Text::_('JEV_EVENT_REPEATTYPE'); ?></legend>
			<label for='NONE' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "NONE" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="NONE" class="gsl-hidden"
				       value="none" <?php if ($freq ==  "NONE") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('NONE');"/>
				<?php echo Text::_('NO_REPEAT'); ?>
			</label>
            <label for='MINUTELY' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "MINUTELY" ? ' gsl-button-primary' : ''; ?>">
                <input type="radio" name="freq" id="MINUTELY" class="gsl-hidden"
                       value="MINUTELY" <?php if ($freq ==  "MINUTELY") echo 'checked="checked"'; ?>
                       onclick="toggleFreq('MINUTELY');"/>
                <?php echo Text::_('MINUTELY'); ?>
            </label>
            <label for='HOURLY' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "HOURLY" ? ' gsl-button-primary' : ''; ?>">
                <input type="radio" name="freq" id="HOURLY" class="gsl-hidden"
                       value="HOURLY" <?php if ($freq ==  "HOURLY") echo 'checked="checked"'; ?>
                       onclick="toggleFreq('HOURLY');"/>
                <?php echo Text::_('HOURLY'); ?>
            </label>
            <label for='DAILY' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "DAILY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="DAILY" class="gsl-hidden"
				       value="DAILY" <?php if ($freq ==  "DAILY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('DAILY');"/>
				<?php echo Text::_('DAILY'); ?>
			</label>
            <label for='WEEKLY' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "WEEKLY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="WEEKLY" class="gsl-hidden"
				       value="WEEKLY" <?php if ($freq ==  "WEEKLY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('WEEKLY');"/>
				<?php echo Text::_('WEEKLY'); ?>
			</label>
            <label for='MONTHLY' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "MONTHLY" ? ' gsl-button-primary' : ''; ?>">
				<input type="radio" name="freq" id="MONTHLY" class="gsl-hidden"
				       value="MONTHLY" <?php if ($freq ==  "MONTHLY") echo 'checked="checked"'; ?>
				       onclick="toggleFreq('MONTHLY');"/>
				<?php echo Text::_('MONTHLY'); ?>
			</label>
            <label for='YEARLY' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "YEARLY" ? ' gsl-button-primary' : ''; ?>">
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
                <label for='IRREGULARBTN' class="gsl-button gsl-button-small gsl-button-default <?php echo $freq ==  "IRREGULAR" ? ' gsl-button-primary' : ''; ?>">
					<input type="radio" name="freq" id="IRREGULARBTN" value="IRREGULAR" class="gsl-hidden"
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
				<input class="inputbox" type="text" name="rinterval" id="rinterval" size="2" maxlength="2"
				       value="<?php echo $this->row->interval(); ?>" onchange="checkInterval();"/><span
						id='interval_label' style="margin-left:1em"></span>
			</fieldset>
		</div>
		<div style="float:left;margin-left:20px!important" id="cu_count">
			<fieldset >
				<legend><input type="radio" name="countuntil" value="count" id="cuc" checked="checked"
				               onclick="toggleCountUntil('cu_count');"/><?php echo Text::_('REPEAT_COUNT') ?></legend>
				<input class="inputbox" type="text" name="count" id="count" size="3" maxlength="3"
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
				JEVHelper::loadElectricCalendar("until", "until", JevDate::strftime("%Y-%m-%d", $this->row->until()), $minyear, $maxyear, 'updateRepeatWarning();', "checkUntil();updateRepeatWarning();", $inputdateformat);
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
					<input class="inputbox" type="text" name="byyearday" size="20" maxlength="100"
					       value="<?php echo $this->row->byyearday(); ?>" onchange="checkInterval();"/>
				</div>
				<div class="countback">
					<?php echo Text::_('COUNT_BACK_YEAR'); ?>
					<input type="checkbox" name="byd_direction"
					       onclick="fixRepeatDates();" <?php echo $this->row->getByDirectionChecked("byyearday"); ?>/>
				</div>
			</fieldset>
		</div>
		<div id="bymonth">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbm" value="bm"
				               onclick="toggleWhichBy('bymonth');"/><?php echo Text::_('BY_MONTH'); ?></legend>
				<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
				<input class="inputbox" type="text" name="bymonth" size="30" maxlength="20"
				       value="<?php echo $this->row->bymonth(); ?>" onchange="checkInterval();"/>
			</fieldset>
		</div>
		<div id="byweekno">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbwn" value="bwn"
				               onclick="toggleWhichBy('byweekno');"/><?php echo Text::_('BY_WEEK_NO'); ?></legend>
				<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
				<input class="inputbox" type="text" name="byweekno" size="20" maxlength="20"
				       value="<?php echo $this->row->byweekno(); ?>" onchange="checkInterval();"/>
				<br/>Count back from year end<input type="checkbox"
				                                    name="bwn_direction" <?php echo $this->row->getByDirectionChecked("byweekno"); ?> />
			</fieldset>
		</div>
		<div id="bymonthday">
			<fieldset>
				<legend><input type="radio" name="whichby" id="jevbmd" value="bmd"
				               onclick="toggleWhichBy('bymonthday');"/><?php echo Text::_('BY_MONTH_DAY'); ?></legend>
				<div>
					<?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
					<input class="inputbox" type="text" name="bymonthday" size="30" maxlength="20"
					       value="<?php echo $this->row->bymonthday(); ?>" onchange="checkInterval();"/>
				</div>
				<div class="countback">
					<?php echo Text::_('COUNT_BACK'); ?><input type="checkbox" name="bmd_direction"
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
				<legend><?php echo Text::_('WHICH_WEEKS_IN_MONTH'); ?></legend>
				<div class="gsl-button-group">
					<?php
					JEventsHTML::buildWeeksCheckUikit($this->row->getByDay_weeks(), "", "weeknums", $this->row->getByDirection("byday"));
					?>
				</div>
				<div class="countback">
					<?php echo Text::_('COUNT_BACK'); ?>
					<input type="checkbox" name="bd_direction" <?php echo $this->row->getByDirectionChecked("byday"); ?>
					       onclick="updateRepeatWarning();toggleWeeknumDirection();"/>
				</div>
			</fieldset>
		</div>

        <div class="gsl-grid gsl-child-width-1-1">
            <div id="byhour">
                <fieldset>
                    <legend>
                        <?php echo Text::_('SPECIFIC_HOURS'); ?>
                        <a href="#" class="gsl-icon  gsl-text-primary gsl-margin-small-left" type="button" gsl-toggle="target: #hour-constraint-info" data-gsl-icon="icon:info"></a>
                    </legend>
                    <div>
                        <div id="hour-constraint-info" gsl-modal>
                            <div class="gsl-modal-dialog">
                                <button class="gsl-modal-close-default" type="button" gsl-close></button>
                                <div class="gsl-modal-header">
                                    <h4 class="gsl-modal-title gsl-text-small">
                                        <?php echo Text::_('COMMA_SEPARATED_LIST_EMPTY_FOR_ALL_OR_HOURS_RANGE'); ?>
                                    </h4>
                                </div>
                                <div class="gsl-modal-body">
                                    <?php echo Text::_('COMMA_SEPARATED_LIST_EMPTY_FOR_ALL_OR_HOURS_RANGE_MORE'); ?>
                                </div>
                            </div>
                        </div>
                        <input class="inputbox" type="text" name="byhour" size="30" maxlength="20"
                               value="<?php echo $this->row->byhour(); ?>" onchange="checkInterval();"/>
                    </div>
                </fieldset>
            </div>

            <div id="byminute">
                <fieldset>
                    <legend>
                        <?php echo Text::_('SPECIFIC_MINUTES'); ?>
                        <a href="#" class="gsl-icon  gsl-text-primary gsl-margin-small-left" type="button" gsl-toggle="target: #minute-constraint-info" data-gsl-icon="icon:info"></a>
                    </legend>
                    <div>
                        <div id="minute-constraint-info" gsl-modal>
                            <div class="gsl-modal-dialog">
                                <button class="gsl-modal-close-default" type="button" gsl-close></button>
                                <div class="gsl-modal-header">
                                    <h4 class="gsl-modal-title gsl-text-small">
                                        <?php echo Text::_('COMMA_SEPARATED_LIST_EMPTY_FOR_ALL_OR_MINUTES_RANGE'); ?>
                                    </h4>
                                </div>
                                <div class="gsl-modal-body">
				                    <?php echo Text::_('COMMA_SEPARATED_LIST_EMPTY_FOR_ALL_OR_MINUTES_RANGE_MORE'); ?>
                                </div>
                            </div>
                        </div>
                        <input class="inputbox" type="text" name="byminute" size="30" maxlength="20"
                               value="<?php echo $this->row->byminute(); ?>" onchange="checkInterval();"/>
                    </div>
                </fieldset>
            </div>

            <div id="bysecond">
                <fieldset>
                    <legend><input type="radio" name="whichby" id="jevsecond" value="bsec"
                                   onclick="toggleWhichBy('bysecond');"/><?php echo Text::_('BY_SECOND'); ?></legend>
                    <div>
                        <?php echo Text::_('COMMA_SEPARATED_LIST'); ?>
                        <input class="inputbox" type="text" name="bysecond" size="30" maxlength="20"
                               value="<?php echo $this->row->bysecond(); ?>" onchange="checkInterval();"/>
                    </div>
                </fieldset>
            </div>
        </div>
        <div id="byirregular">
			<fieldset>
				<legend><?php echo Text::_('JEV_SELECT_REPEAT_DATES'); ?></legend>
				<div class="irregularDateSelector">
					<?php
					$params           = ComponentHelper::getParams(JEV_COM_COMPONENT);
					$minyear          = JEVHelper::getMinYear();
					$maxyear          = JEVHelper::getMaxYear();
					$inputdateformat  = $params->get("com_editdateformat", "d.m.Y");
					$inputdateformat2 = str_replace(array("Y", "m", "d"), array("%Y", "%m", "%d"), $inputdateformat);
					$attribs          = array("style" => "display:none;");
					$irregulartimes   = $params->get("irregulartimes", 0);
					if ($irregulartimes)
					{
						$attribs["showtime"] = "showtime";
						$inputdateformat     .= " %H:%M";
					}
					JEVHelper::loadElectricCalendar("irregular", "irregular", "", $minyear, $maxyear, '', "setTimeout(function() {selectIrregularDate();updateRepeatWarning();}, 200)", $inputdateformat, $attribs);
					//JEVHelper::loadElectricCalendar("irregular", "irregular", "", $minyear, $maxyear, '', "jQuery(this).trigger('calupdate');", $inputdateformat, $attribs);

					//"selectIrregularDate();updateRepeatWarning();"
					/*
					Factory::getDocument()->addScriptDeclaration(
						'jQuery(document).on("ready", function () {
						jQuery("#irregular").on("calupdate", function(evt) {
							alert(evt);
						});
						});'
					);
					 */
					?>
				</div>
				<select id="irregularDates" name="irregularDates[]" multiple="multiple" size="5"
				        onchange="updateRepeatWarning()">
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
<script type="text/javascript">
    // make the correct frequency visible
    function setupRepeats() {
        hideEmptyJevTabs();
		<?php
		if ($this->row->id() != 0 && $this->row->freq())
		{
		?>
        var freq = "<?php echo strtoupper($this->row->freq()); ?>";
        document.getElementById(freq).checked = true;
        toggleFreq(freq, true);
        var by = "<?php
	        if ($freq === "HOURLY")
		        echo "jevbd";
			else if ($this->row->byyearday(true) != "")
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
	        if ($freq === "HOURLY")
		        echo "byday";
			else if ($this->row->byyearday(true) != "")
				echo "byyearday";
			else if ($this->row->bymonth(true) != "")
				echo "bymonth";
			else if ($this->row->bymonthday(true) != "")
				echo "bymonthday";
			else if ($this->row->byweekno(true) != "")
				echo "byweekno";
			else if ($this->row->byday(true) != "")
				echo "byday";
			else
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

    //if (window.attachEvent) window.attachEvent("onload",setupRepeats);
    //else window.onload=setupRepeats;
    //setupRepeats();
    window.setTimeout(setupRepeats, 500);
    // move to 12h fields
    set12hTime(document.adminForm.start_time);
    set12hTime(document.adminForm.end_time);
    // toggle unvisible time fields
    toggleView12Hour();

</script>
