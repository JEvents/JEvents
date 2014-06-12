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
if( $cfg->get('com_calUseStdTime') == 0 ) {
	$clock24=true;
}
else $clock24=false;
if ($this->editCopy || $this->repeatId==0) {
	$repeatStyle=" class='jeveditrepeats jevdatetime ' ";
}
else {
	$repeatStyle="style='display:none;' class='jeveditrepeats jevdatetime' ";
}

// Disable event repeats for non-full editors if disable repeats is enabled
$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
if ($params->get("disablerepeats",0) && !JEVHelper::isEventEditor() ){
	$repeatStyle="style='display:none;' class='jeveditrepeats' ";
}

?>
   <div style="clear:both;" class="jevdatetime">
    <fieldset class="jev_sed"><legend><?php echo JText::_("Start_End_Duration");?></legend>
    <span>
		<span ><?php echo JText::_('JEV_EVENT_ALLDAY');?></span>
		<span><input type="checkbox" id='allDayEvent' name='allDayEvent' <?php echo $this->row->alldayevent()?"checked='checked'":"";?> onclick="toggleAllDayEvent();" />
		</span>
    </span>
	<span style="margin:20px" class='checkbox12h'>
		<span style="font-weight:bold"><?php echo JText::_("TWELVE_Hour");?></span>
		<span><input type="checkbox" id='view12Hour' name='view12Hour' <?php echo !$clock24 ?"checked='checked'":"";?> onclick="toggleView12Hour();" value="1"/>
		</span>
	</span>
    <div>
        <fieldset><legend><?php echo JText::_('JEV_EVENT_STARTDATE'); ?></legend>
        <div style="float:left">
			<?php
			$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
			$minyear = JEVHelper::getMinYear();
			$maxyear = JEVHelper::getMaxYear();
			$inputdateformat = $params->get("com_editdateformat","d.m.Y");
			$document = JFactory::getDocument();
			$js = "\neventEditDateFormat='$inputdateformat';Date.defineParser(eventEditDateFormat.replace('d','%d').replace('m','%m').replace('Y','%Y'));";
			$document->addScriptDeclaration($js);

			JEVHelper::loadCalendar("publish_up", "publish_up", $this->row->startDate(),$minyear, $maxyear, 'var elem = $("publish_up");checkDates(elem);fixRepeatDates();',"elem = $('publish_up');checkDates(elem);", $inputdateformat);
		
			?>
			<input type="hidden"  name="publish_up2" id="publish_up2" value="" />
         </div>
         <div style="float:left;margin-left:20px!important;">
            <?php echo JText::_('JEV_EVENT_STARTTIME')."&nbsp;"; ?>
			<span id="start_24h_area" class="jev_inline">
            <input class="inputbox" type="text" name="start_time" id="start_time" size="8" <?php echo $this->row->alldayevent()?"disabled='disabled'":"";?> maxlength="8" value="<?php echo $this->row->starttime24();?>" onchange="checkTime(this);"/>
			</span>
			<span id="start_12h_area" class="jev_inline">
           	<input class="inputbox" type="text" name="start_12h" id="start_12h" size="8" maxlength="8" <?php echo $this->row->alldayevent()?"disabled='disabled'":"";?> value="" onchange="check12hTime(this);" />
      		<input type="radio" name="start_ampm" id="startAM" value="none" checked="checked" onclick="toggleAMPM('startAM');" <?php echo $this->row->alldayevent()?"disabled='disabled'":"";?> /><?php echo JText::_( 'JEV_AM' );?>
      		<input type="radio" name="start_ampm" id="startPM" value="none" onclick="toggleAMPM('startPM');" <?php echo $this->row->alldayevent()?"disabled='disabled'":"";?> /><?php echo JText::_( 'JEV_PM' );?>
			</span>
         </div>
         </fieldset>
     </div>
    <div>
        <fieldset><legend><?php echo JText::_('JEV_EVENT_ENDDATE'); ?></legend>
        <div style="float:left">
				<?php
			$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
			$minyear = JEVHelper::getMinYear();
			$maxyear = JEVHelper::getMaxYear();
			JEVHelper::loadCalendar("publish_down", "publish_down", $this->row->endDate(),$minyear, $maxyear, 'var elem = $("publish_down");checkDates(elem);',"elem = $('publish_up');checkDates(elem);", $inputdateformat);
			
			?>
			<input type="hidden"  name="publish_down2" id="publish_down2" value="" />
			
         </div>
         <div style="float:left;margin-left:20px!important">
             <?php echo JText::_('JEV_EVENT_ENDTIME')."&nbsp;"; ?>
			<span id="end_24h_area" class="jev_inline">
           	<input class="inputbox" type="text" name="end_time" id="end_time" size="8" maxlength="8" <?php echo ($this->row->alldayevent() || $this->row->noendtime())?"disabled='disabled'":"";?> value="<?php echo $this->row->endtime24();?>" onchange="checkTime(this);" />
			</span>
			<span id="end_12h_area" class="jev_inline">
           	<input class="inputbox" type="text" name="end_12h" id="end_12h" size="8" maxlength="8" <?php echo ($this->row->alldayevent() || $this->row->noendtime())?"disabled='disabled'":"";?> value="" onchange="check12hTime(this);" />
      		<input type="radio" name="end_ampm" id="endAM" value="none" checked="checked" onclick="toggleAMPM('endAM');" <?php echo ($this->row->alldayevent() || $this->row->noendtime())?"disabled='disabled'":"";?> /><?php echo JText::_( 'JEV_AM' );?>
      		<input type="radio" name="end_ampm" id="endPM" value="none" onclick="toggleAMPM('endPM');" <?php echo ($this->row->alldayevent() || $this->row->noendtime())?"disabled='disabled'":"";?> /><?php echo JText::_( 'JEV_PM' );?>
			</span>
		    <span style="margin-left:10px">
				<span><input type="checkbox" id='noendtime' name='noendtime' <?php echo $this->row->noendtime()?"checked='checked'":"";?> onclick="toggleNoEndTime();" value="1" />
				<span ><?php echo JText::_('JEV_EVENT_NOENDTIME');?></span>
				</span>
		    </span>
         </div>
         </fieldset>
     </div>
    <div id="jevmultiday" style="display:<?php echo $this->row->endDate()>$this->row->startDate()?"block":"none";?>">
        <fieldset><legend><?php echo JText::_('JEV_EVENT_MULTIDAY'); ?></legend>
            <?php echo JText::_('JEV_EVENT_MULTIDAY_LONG')."&nbsp;"; ?>
      		<input type="radio" name="multiday" value="1" <?php echo $this->row->multiday()?'checked="checked"':'';?>  onclick="updateRepeatWarning();" /><?php echo JText::_("JEV_YES");?>
      		<input type="radio" name="multiday" value="0" <?php echo $this->row->multiday()?'':'checked="checked"';?>  onclick="updateRepeatWarning();" /><?php echo JText::_("JEV_NO");?>
         </fieldset>
     </div>
     </fieldset>
     </div>
     <div <?php echo $repeatStyle;?>>
	 <!-- REPEAT FREQ -->
     <div style="clear:both;">
		<fieldset><legend><?php echo JText::_('JEV_EVENT_REPEATTYPE'); ?></legend>
        <table border="0" cellspacing="2">
        	<tr>                                	
            <td class="r1"><input type="radio" name="freq" id="NONE" value="none" checked="checked" onclick="toggleFreq('NONE');" /><label for='NONE'><?php echo JText::_( 'NO_REPEAT' );?></label></td>
            <td class="r2"><input type="radio" name="freq" id="DAILY" value="DAILY" onclick="toggleFreq('DAILY');" /><label for='DAILY'><?php echo JText::_( 'DAILY' );?></label></td>
            <td class="r1"><input type="radio" name="freq" id="WEEKLY" value="WEEKLY" onclick="toggleFreq('WEEKLY');" /><label for='WEEKLY'><?php echo JText::_( 'WEEKLY' );?></label></td>
            <td class="r2"><input type="radio" name="freq" id="MONTHLY" value="MONTHLY" onclick="toggleFreq('MONTHLY');" /><label for='MONTHLY'><?php echo JText::_( 'MONTHLY' );?></label></td>
            <td class="r1"><input type="radio" name="freq" id="YEARLY" value="YEARLY" onclick="toggleFreq('YEARLY');" /><label for='YEARLY'><?php echo JText::_( 'YEARLY' );?></label></td>
	   <?php 
		$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
		if ($params->get("dayselect",0)){
		?>
            <td class="r2"><input type="radio" name="freq" id="IRREGULAR" value="IRREGULAR" onclick="toggleFreq('IRREGULAR');" /><label for='IRREGULAR'><?php echo JText::_( 'IRREGULAR' );?></label></td>
		<?php } ?>
            </tr>
		</table>
        </fieldset>
	</div>			
   <!-- END REPEAT FREQ-->
   <div style="clear:both;display:none" id="interval_div">
   		<div style="float:left">
   		<fieldset><legend><?php echo JText::_( 'REPEAT_INTERVAL' ) ?></legend>
            <input class="inputbox" type="text" name="rinterval" id="rinterval" size="2" maxlength="2" value="<?php echo $this->row->interval();?>" onchange="checkInterval();" /><span id='interval_label' style="margin-left:1em"></span>
   		</fieldset>
   		</div>
   		<div style="float:left;margin-left:20px!important"  id="cu_count" >
   		<fieldset><legend><input type="radio" name="countuntil" value="count" id="cuc" checked="checked" onclick="toggleCountUntil('cu_count');" /><?php echo JText::_( 'REPEAT_COUNT' ) ?></legend>
            <input class="inputbox" type="text" name="count" id="count" size="3" maxlength="3" value="<?php echo $this->row->count();?>" onchange="checkInterval();" /><span id='count_label' style="margin-left:1em"><?php echo JText::_( 'REPEATS' );?></span>
   		</fieldset>
   		</div>
   		<div style="float:left;margin-left:20px!important;background-color:#dddddd;" id="cu_until">
   		<fieldset style="background-color:#dddddd"><legend><input type="radio" name="countuntil" value="until" id="cuu" onclick="toggleCountUntil('cu_until');" /><?php echo JText::_( 'REPEAT_UNTIL' ); ?></legend>
			<?php
			/*
			 echo JHTML::calendar(JevDate::strftime("%Y-%m-%d",$this->row->until()), 'until', 'until', '%Y-%m-%d',	array('size'=>'12','maxlength'=>'10'));
			 */
			$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
			$minyear = JEVHelper::getMinYear();
			$maxyear = JEVHelper::getMaxYear();
			JEVHelper::loadCalendar("until", "until", JevDate::strftime("%Y-%m-%d",$this->row->until()),$minyear, $maxyear, 'updateRepeatWarning();',"checkUntil();updateRepeatWarning();", $inputdateformat);
			?>
			<input type="hidden"  name="until2" id="until2" value="" />

   		</fieldset>
   		</div>
   </div>
   <div style="clear:both;">
   <div  style="float:left;display:none;margin-right:1em;" id="byyearday">
   		<fieldset><legend><input type="radio" name="whichby" id="jevbyd" value="byd"  onclick="toggleWhichBy('byyearday');" /><?php echo JText::_( 'BY_YEAR_DAY' ); ?></legend>
   			<?php echo JText::_( 'COMMA_SEPARATED_LIST' );?>
            <input class="inputbox" type="text" name="byyearday" size="20" maxlength="50" value="<?php echo $this->row->byyearday();?>" onchange="checkInterval();" />
   			<br/><?php echo JText::_( 'COUNT_BACK_YEAR' );?><input type="checkbox" name="byd_direction"  onclick="fixRepeatDates();" <?php echo $this->row->getByDirectionChecked("byyearday");?>/>
   		</fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="bymonth">
   		<fieldset><legend><input type="radio" name="whichby"  id="jevbm" value="bm"  onclick="toggleWhichBy('bymonth');" /><?php echo JText::_( 'BY_MONTH' ); ?></legend>
   			<?php echo JText::_( 'COMMA_SEPARATED_LIST' );?>
            <input class="inputbox" type="text" name="bymonth" size="30" maxlength="20" value="<?php echo $this->row->bymonth();?>" onchange="checkInterval();" />
        </fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="byweekno">
   		<fieldset><legend><input type="radio" name="whichby"  id="jevbwn" value="bwn"  onclick="toggleWhichBy('byweekno');" /><?php echo JText::_( 'BY_WEEK_NO' ); ?></legend>
   			<?php echo JText::_( 'COMMA_SEPARATED_LIST' );?>
            <input class="inputbox" type="text" name="byweekno" size="20" maxlength="20" value="<?php echo $this->row->byweekno();?>" onchange="checkInterval();" />
   			<br/>Count back from year end<input type="checkbox" name="bwn_direction"  <?php echo $this->row->getByDirectionChecked("byweekno");?> />
        </fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="bymonthday">
   		<fieldset><legend><input type="radio" name="whichby"  id="jevbmd" value="bmd"  onclick="toggleWhichBy('bymonthday');" /><?php echo JText::_( 'BY_MONTH_DAY' ); ?></legend>
   			<?php echo JText::_( 'COMMA_SEPARATED_LIST' );?>
            <input class="inputbox" type="text" name="bymonthday" size="30" maxlength="20" value="<?php echo $this->row->bymonthday();?>" onchange="checkInterval();" />
   			<br/><?php echo JText::_( 'COUNT_BACK' );?><input type="checkbox" name="bmd_direction"  onclick="fixRepeatDates();"  <?php echo $this->row->getByDirectionChecked("bymonthday");?>/>
        </fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="byday">
   		<fieldset><legend><input type="radio" name="whichby"  id="jevbd" value="bd"  onclick="toggleWhichBy('byday');" /><?php echo JText::_( 'BY_DAY' ); ?></legend>           			
            <?php 
            JEventsHTML::buildWeekDaysCheck( $this->row->getByDay_days(), '' ,"weekdays");
            ?>
            <div id="weekofmonth">
   			<?php
   			JEventsHTML::buildWeeksCheck( $this->row->getByDay_weeks(), "" ,"weeknums");
            ?>
   			<br/><?php echo JText::_( 'COUNT_BACK' );?><input type="checkbox" name="bd_direction" <?php echo $this->row->getByDirectionChecked("byday");?>  onclick="updateRepeatWarning();"/>
            </div>
   		</fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="bysetpos">
   		<fieldset><legend><?php echo "NOT YET SUPPORTED" ?></legend>
   		</fieldset>
   </div>
   </div>
   <div style="clear:both;"></div>
</div>
<script type="text/javascript" >
// make the correct frequency visible
function setupRepeats(){
	<?php
	if ($this->row->id()!=0 && $this->row->freq()){
		?>
		var freq = "<?php echo strtoupper($this->row->freq());?>";
		document.getElementById(freq).checked=true;
		toggleFreq(freq, true);
		var by = "<?php
		if ($this->row->byyearday(true)!="") echo "jevbyd";
		else if ($this->row->bymonth(true)!="") echo "jevbm";
		else if ($this->row->bymonthday(true)!="") echo "jevbmd";
		else if ($this->row->byweekno(true)!="") echo "jevbwn";
		else if ($this->row->byday(true)!="") echo "jevbd";
		// default repeat is by day
		else echo "jevbd";
		?>";
		document.getElementById(by).checked=true;
		var by = "<?php
		if ($this->row->byyearday(true)!="") echo "byyearday";
		else if ($this->row->bymonth(true)!="") echo "bymonth";
		else if ($this->row->bymonthday(true)!="") echo "bymonthday";
		else if ($this->row->byweekno(true)!="") echo "byweekno";
		else if ($this->row->byday(true)!="") echo "byday";
		?>";
		toggleWhichBy(by);
		var cu = "cu_<?php
		if ($this->row->rawuntil()!="") echo "until";
		else echo "count";
		?>";
		document.getElementById(cu=="cu_until"?"cuu":"cuc").checked=true;
		toggleCountUntil(cu);

		// Now reset the repeats warning so we can track any changes
		document.adminForm.updaterepeats.value = 0;
		<?php
	}
	?>
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
</script>
