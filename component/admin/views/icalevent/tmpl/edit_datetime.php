<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit_datetime.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

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
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("disablerepeats", 0) && !JEVHelper::isEventEditor())
{
	$repeatStyle = "style='display:none;' class='jeveditrepeats' ";
}
?>
<div style="clear:both;" class="jevdatetime">
	<fieldset class="jev_sed"><legend><?php echo JText::_("Start_End_Duration"); ?></legend>
		<div  class="control-group form-inline allDayEvent">
			<?php echo $this->form->getLabel("allDayEvent"); ?>
			<span>
				<?php echo $this->form->getInput("allDayEvent"); ?>
			</span>

			<span style="margin-left:20px" class='checkbox12h'>
				<?php echo $this->form->getLabel("view12Hour"); ?>
				<?php echo $this->form->getInput("view12Hour"); ?>
			</span>
		</div>

		<div  class="control-group  form-inline">
			<span class="jevstartdate">
				<?php echo $this->form->getLabel("publish_up"); ?>
				<?php echo $this->form->getInput("publish_up"); ?>
			</span>

			<span style="margin-left:20px" class='jevstarttime'>
				<?php echo $this->form->getLabel("start_time"); ?>
				<?php echo $this->form->getInput("start_time"); ?>
			</span>
		</div>

		<div  class="control-group  form-inline">
			<span class="jevenddate">
				<?php echo $this->form->getLabel("publish_down"); ?>
				<?php echo $this->form->getInput("publish_down"); ?>
			</span>

			<span style="margin-left:20px" class='jevendtime'>
				<?php echo $this->form->getLabel("end_time"); ?>
				<?php echo $this->form->getInput("end_time"); ?>
			</span>

			<span style="margin-left:20px" class='jevnoendtime'>
				<?php echo $this->form->getLabel("noendtime"); ?>
				<?php echo $this->form->getInput("noendtime"); ?>
			</span>

		</div>
		

		<div id="jevmultiday" style="display:<?php echo $this->row->endDate() > $this->row->startDate() ? "block" : "none"; ?>">

			<label style="font-weight:bold;" ><?php echo JText::_('JEV_EVENT_MULTIDAY'); ?></label><br/>
			<div style="float:left;"><?php echo JText::_('JEV_EVENT_MULTIDAY_LONG') . "&nbsp;"; ?></div>
			<div class="radio btn-group" style="float:left;margin-left:20px!important;">
				<label for="yes"  class="radio btn">
				<input type="radio" id="yes" name="multiday" value="1" <?php echo $this->row->multiday() ? 'checked="checked"' : ''; ?>  onclick="updateRepeatWarning();" />
					<?php echo JText::_("JEV_YES"); ?>
				</label>
				<label for="no" class="radio btn">
				<input type="radio" id="no" name="multiday" value="0" <?php echo $this->row->multiday() ? '' : 'checked="checked"'; ?>  onclick="updateRepeatWarning();" />
					<?php echo JText::_("JEV_NO"); ?>
				</label>
			</div>
		</div>
	</fieldset>
</div>

<div <?php echo $repeatStyle; ?>>
	<!-- REPEAT FREQ -->
	<div style="clear:both;">
		<fieldset class="radio btn-group" ><legend><?php echo JText::_('JEV_EVENT_REPEATTYPE'); ?></legend>
			<label for='NONE' class="btn radio">
				<input type="radio" name="freq" id="NONE" value="none" <?php if ($this->row->freq() == "NONE") echo 'checked="checked"'; ?> onclick="toggleFreq('NONE');" />
				<?php echo JText::_('NO_REPEAT'); ?>
			</label>
			<label for='DAILY' class="btn radio">
				<input type="radio" name="freq" id="DAILY" value="DAILY" <?php if ($this->row->freq() == "DAILY") echo 'checked="checked"'; ?> onclick="toggleFreq('DAILY');" />
				<?php echo JText::_('DAILY'); ?>
			</label>
			<label for='WEEKLY' class="btn radio">
				<input type="radio" name="freq" id="WEEKLY" value="WEEKLY" <?php if ($this->row->freq() == "WEEKLY") echo 'checked="checked"'; ?> onclick="toggleFreq('WEEKLY');" />
				<?php echo JText::_('WEEKLY'); ?>
			</label>
			<label for='MONTHLY' class="btn radio">
				<input type="radio" name="freq" id="MONTHLY" value="MONTHLY" <?php if ($this->row->freq() == "MONTHLY") echo 'checked="checked"'; ?> onclick="toggleFreq('MONTHLY');" />
				<?php echo JText::_('MONTHLY'); ?>
			</label>
			<label for='YEARLY' class="btn radio">
				<input type="radio" name="freq" id="YEARLY" value="YEARLY" <?php if ($this->row->freq() == "YEARLY") echo 'checked="checked"'; ?> onclick="toggleFreq('YEARLY');" />
				<?php echo JText::_('YEARLY'); ?>
			</label>
			<?php
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("dayselect", 0))
			{
				?>
				<label for='IRREGULAR' class="btn radio">
					<input type="radio" name="freq" id="IRREGULAR" value="IRREGULAR" onclick="toggleFreq('IRREGULAR');"  <?php if ($this->row->freq() == "IRREGULAR") echo 'checked="checked"'; ?>/>
					<?php echo JText::_('IRREGULAR'); ?>
				</label>
			<?php } ?>
		</fieldset>
	</div>			
	<!-- END REPEAT FREQ-->
	<div id="interval_div">
		<div style="float:left">
			<fieldset><legend><?php echo JText::_('REPEAT_INTERVAL') ?></legend>
				<input class="inputbox" type="text" name="rinterval" id="rinterval" size="2" maxlength="2" value="<?php echo $this->row->interval(); ?>" onchange="checkInterval();" /><span id='interval_label' style="margin-left:1em"></span>
			</fieldset>
		</div>
		<div style="float:left;margin-left:20px!important"  id="cu_count" >
			<fieldset><legend><input type="radio" name="countuntil" value="count" id="cuc" checked="checked" onclick="toggleCountUntil('cu_count');" /><?php echo JText::_('REPEAT_COUNT') ?></legend>
				<input class="inputbox" type="text" name="count" id="count" size="3" maxlength="3" value="<?php echo $this->row->count(); ?>" onchange="checkInterval();" /><span id='count_label' style="margin-left:1em"><?php echo JText::_('REPEATS'); ?></span>
			</fieldset>
		</div>
		<div style="float:left;margin-left:20px!important;background-color:#dddddd;" id="cu_until">
			<fieldset style="background-color:#dddddd">
				<legend  style="background-color:#dddddd"><input type="radio" name="countuntil" value="until" id="cuu" onclick="toggleCountUntil('cu_until');" /><?php echo JText::_('REPEAT_UNTIL'); ?></legend>
				<?php
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$minyear = JEVHelper::getMinYear();
				$maxyear = JEVHelper::getMaxYear();
				$inputdateformat = $params->get("com_editdateformat", "d.m.Y");
				JEVHelper::loadCalendar("until", "until", JevDate::strftime("%Y-%m-%d", $this->row->until()), $minyear, $maxyear, 'updateRepeatWarning();', "checkUntil();updateRepeatWarning();", $inputdateformat);
				?>
				<input type="hidden"  name="until2" id="until2" value="" />

			</fieldset>
		</div>
	</div>
	<div style="clear:both;">
		<div   id="byyearday">
			<fieldset><legend><input type="radio" name="whichby" id="jevbyd" value="byd"  onclick="toggleWhichBy('byyearday');" /><?php echo JText::_('BY_YEAR_DAY'); ?></legend>
				<div>					
					<?php echo JText::_('COMMA_SEPARATED_LIST'); ?>
					<input class="inputbox" type="text" name="byyearday" size="20" maxlength="100" value="<?php echo $this->row->byyearday(); ?>" onchange="checkInterval();" />
				</div>
				<div class="countback">
					<?php echo JText::_('COUNT_BACK_YEAR'); ?>
					<input type="checkbox" name="byd_direction"  onclick="fixRepeatDates();" <?php echo $this->row->getByDirectionChecked("byyearday"); ?>/>
				</div>
			</fieldset>
		</div>
		<div  id="bymonth">
			<fieldset><legend><input type="radio" name="whichby"  id="jevbm" value="bm"  onclick="toggleWhichBy('bymonth');" /><?php echo JText::_('BY_MONTH'); ?></legend>
				<?php echo JText::_('COMMA_SEPARATED_LIST'); ?>
				<input class="inputbox" type="text" name="bymonth" size="30" maxlength="20" value="<?php echo $this->row->bymonth(); ?>" onchange="checkInterval();" />
			</fieldset>
		</div>
		<div id="byweekno">
			<fieldset><legend><input type="radio" name="whichby"  id="jevbwn" value="bwn"  onclick="toggleWhichBy('byweekno');" /><?php echo JText::_('BY_WEEK_NO'); ?></legend>
				<?php echo JText::_('COMMA_SEPARATED_LIST'); ?>
				<input class="inputbox" type="text" name="byweekno" size="20" maxlength="20" value="<?php echo $this->row->byweekno(); ?>" onchange="checkInterval();" />
				<br/>Count back from year end<input type="checkbox" name="bwn_direction"  <?php echo $this->row->getByDirectionChecked("byweekno"); ?> />
			</fieldset>
		</div>
		<div   id="bymonthday">
			<fieldset><legend><input type="radio" name="whichby"  id="jevbmd" value="bmd"  onclick="toggleWhichBy('bymonthday');" /><?php echo JText::_('BY_MONTH_DAY'); ?></legend>
				<div>
					<?php echo JText::_('COMMA_SEPARATED_LIST'); ?>
					<input class="inputbox" type="text" name="bymonthday" size="30" maxlength="20" value="<?php echo $this->row->bymonthday(); ?>" onchange="checkInterval();" />
				</div>
				<div class="countback">
					<?php echo JText::_('COUNT_BACK'); ?><input type="checkbox" name="bmd_direction"  onclick="fixRepeatDates();"  <?php echo $this->row->getByDirectionChecked("bymonthday"); ?>/>
				</div>
			</fieldset>
		</div>
		<div id="byday">
			<fieldset >
				<legend><input type="radio" name="whichby"  id="jevbd" value="bd"  onclick="toggleWhichBy('byday');" /><?php echo JText::_('BY_DAY'); ?></legend>           			
				<div class="checkbox btn-group ">
					<?php
					JEventsHTML::buildWeekDaysCheck($this->row->getByDay_days(), '', "weekdays");
					?>
				</div>
			</fieldset>
			<fieldset  id="weekofmonth">
				<legend><?php echo JText::_('WHICH_WEEK'); ?></legend>           			
				<div class="checkbox btn-group ">
					<?php
					JEventsHTML::buildWeeksCheck($this->row->getByDay_weeks(), "", "weeknums");
					?>
				</div>
				<div class="countback">
					<?php echo JText::_('COUNT_BACK'); ?>
					<input type="checkbox" name="bd_direction" <?php echo $this->row->getByDirectionChecked("byday"); ?>  onclick="updateRepeatWarning();"/>
				</div>
			</fieldset>
		</div>
		<div  class="jev_none" id="bysetpos">
			<fieldset><legend><?php echo "NOT YET SUPPORTED" ?></legend>
			</fieldset>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
<script type="text/javascript" >
	// make the correct frequency visible
	function setupRepeats(){	
	hideEmptyJevTabs()
<?php
if ($this->row->id() != 0 && $this->row->freq())
{
	?>
				var freq = "<?php echo strtoupper($this->row->freq()); ?>";
				document.getElementById(freq).checked=true;
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
				document.getElementById(by).checked=true;
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
				document.getElementById(cu=="cu_until"?"cuu":"cuc").checked=true;
				toggleCountUntil(cu);

				// Now reset the repeats warning so we can track any changes
				document.adminForm.updaterepeats.value = 0;
	<?php
}
?>
		setupJEventsBootstrap();
	}
	//if (window.attachEvent) window.attachEvent("onload",setupRepeats);
	//else window.onload=setupRepeats;
	//setupRepeats();
	window.setTimeout("setupRepeats()", 500);
	// move to 12h fields
	set12hTime(document.adminForm.start_time);
	set12hTime(document.adminForm.end_time);
	// toggle unvisible time fields
	toggleView12Hour();

	function setupJEventsBootstrap(){
		(function($){
			// Turn radios into btn-group
			$('.radio.btn-group label').addClass('btn');
			var el = $(".radio.btn-group label");
			
			// Isis template and others may already have done this so remove these!
			$(".radio.btn-group label").unbind('click');
			
			$(".radio.btn-group label").click(function() {
				var label = $(this);
				var input = $('#' + label.attr('for'));
				if (!input.prop('checked') && !input.prop('disabled')) {
					label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
					if (input.prop('value')!=0){
						label.addClass('active btn-success');
					}
					else {
						label.addClass('active btn-danger');
					}
					input.prop('checked', true);
				}
			});

			// Turn checkboxes into btn-group
			$('.checkbox.btn-group label').addClass('btn');
			
			// Isis template and others may already have done this so remove these!
			$(".checkbox.btn-group label").unbind('click');
			$(".checkbox.btn-group label input[type='checkbox']").unbind('click');
			
			$(".checkbox.btn-group label").click(function(event) {
				event || (event = window.event);
				
				// stop the event being triggered twice is click on input AND label outside it!
				if (event.target.tagName.toUpperCase()=="INPUT"){
					//event.preventDefault();
					return;
				}
				
				var label = $(this);
				var input = $('#' + label.attr('for'));
				//alert(label.val()+ " "+event.target.tagName+" checked? "+input.prop('checked')+ " disabled? "+input.prop('disabled')+ " label disabled? "+label.hasClass('disabled'));
				if (input.prop('disabled')) {
					label.removeClass('active btn-success btn-danger btn-primary');
					input.prop('checked', false);
					event.stopImmediatePropagation();
					return;
				}
				if (!input.prop('checked')) {
					if (input.prop('value')!=0){
						label.addClass('active btn-success');
					}
					else {
						label.addClass('active btn-danger');
					}
				}
				else {
					label.removeClass('active btn-success btn-danger btn-primary');
				}
				// bootstrap takes care of the checkboxes themselves!
				
			});
		
			$(".btn-group input[type=checkbox]").each(function() {
				var input = $(this);
				input.css('display','none');
			});		
		})(jQuery);
		
		initialiseBootstrapButtons();
	}
	
	function initialiseBootstrapButtons(){
		(function($){	
			// this doesn't seem to find just the checked ones!'
			//$(".btn-group input[checked=checked]").each(function() {
			$(".btn-group input").each(function() {
				var label = $("label[for=" + $(this).attr('id') + "]");
				var elem = $(this);
				if (elem.prop('disabled')) {
					label.addClass('disabled');
					label.removeClass('active btn-success btn-danger btn-primary');
					return;
				}
				label.removeClass('disabled');
				if (!elem.prop('checked')) {
					label.removeClass('active btn-success btn-danger btn-primary');
					return;
				}
				if (elem.prop('value')!=0){
					label.addClass('active btn-success');
				}
				else {
					label.addClass('active btn-danger');
				}

			});
			
		})(jQuery);
	}
	
</script>
