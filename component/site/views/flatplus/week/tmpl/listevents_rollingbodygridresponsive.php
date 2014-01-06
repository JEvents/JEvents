<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();
$howManyWeeksToShow = max(intval($cfg->get('rollingweeks',1)),1);
$hiddendatearray = array();
// for adding events in day cell
$this->popup = false;
if ($cfg->get("editpopup", 0))
{
	JHTML::_('behavior.modal');
	JHTML::script('components/' . JEV_COM_COMPONENT . '/assets/js/editpopup.js');
	$this->popup = true;
	$this->popupw = $cfg->get("popupw", 800);
	$this->popuph = $cfg->get("popuph", 600);
}


$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/" . $viewname . "/assets";
$viewimages = $viewpath . "/images";

echo $this->loadTemplate('cell');
$eventCellClass = "EventCalendarCell_" . $viewname;

//$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);
$extradata = array();

// generate the extra data for each week to display
if ($howManyWeeksToShow)
{
	$extradata = array();
	$today = mktime(0, 0, 0, $this->month, $this->day, $this->year);
	for ($w = 0; $w < $howManyWeeksToShow; $w++)
	{
		list($y, $m, $d) = explode("-", strftime("%Y-%m-%d", $today));
		$extradata[$w] = $this->datamodel->getWeekData($y, $m, $d);
		if ($w == 0)
		{
			$this->data = $data = $extradata[$w];
		}
		$today += 604800;
	}
	// set the end date output correctly
	 $extradata[0]["enddate"] = $data["enddate"] = $extradata[$howManyWeeksToShow - 1]["enddate"];
}
// make sure the weeks are in the correct sequence
ksort($extradata);


// Sort out the events so we only show the max display number
$maxdisplay = $cfg->get('com_calMaxDisplay', 5);
$datacount = 7;
for ($dn = 0; $dn < $datacount; $dn++)
{
	foreach ($extradata as & $data)
	{
		if (count($data["days"][$dn]["rows"]) > $maxdisplay)
		{
			$data["days"][$dn]["rows"] = array_slice($data["days"][$dn]["rows"], 0, $maxdisplay);
			$data["days"][$dn]["capped"] = true;
		}
		else
		{
			$data["days"][$dn]["capped"] = false;
		}
	}
	unset($data);
}

$startday = $cfg->get('com_starday');
if (!$startday)
{
	$startday = 0;
}
foreach ($extradata as & $data)
{
	$data['startday'] = $startday;
	$data["daynames"] = array();
	for ($i = 0; $i < 7; $i++)
	{
		$data["daynames"][$i] = JEventsHTML::getDayName(($i + $startday) % 7, true);
	}
	unset($data);
}

// Sort out the events so we only show the max display number
$maxdisplay = $cfg->get('com_calMaxDisplay', 5);

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day + ($howManyWeeksToShow - 1) * 7);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

$week = 0;
foreach ($extradata as & $data)
{
// setup and allocate slots if necessary NB slots hold the events that occur on that date
	if (!isset($data['days'][0]["slots"]))
	{
		for ($slot = 0; $slot < 7; $slot++)
		{
			$data['days'][$slot]["slots"] = array();
		}
	}
	for ($dn = 0; $dn < 7; $dn++)
	{

		unset($currentDay);
		$currentDay = & $data['days'][$dn];
		$currentDay['countDisplay'] = 0;

		$currentDay ["cellDate"] = JevDate::mktime(0, 0, 0, $data['days'][$dn]['week_month'], $data['days'][$dn]['week_day'], $data['days'][$dn]['week_year']);
		$dayOfWeek = JevDate::strftime("%w", $currentDay ["cellDate"]);

		$weekstartday = $cfg->get('com_starday', 0);
		if (!$weekstartday )
		{
			$weekstartday = 0;
		}
		// adjust day of week to reflect start day in config
		$dayOfWeek -= $weekstartday;
		if ($dayOfWeek < 0)
		{
			$dayOfWeek+=7;
		}

		// I need to sort the events by start date order (not start time on the day)
		usort($data['days'][$dn]["rows"], array($this, "sortjevents"));

		for ($i = 0; $i < count($data['days'][$dn]["rows"]); $i++)
		{
			unset($event);
			$event = & $currentDay["rows"][$i];

			// find first empty slot for this event
			// If second/third week for event the slot needs to be reset first
			// This clearly doens't apply to multiday evenyts only diusplaying on first day
			if (isset($event->slot_to_use) && $event->slot_to_use > 0 && !isset($event->slotreset) && !$event->multiday())
			{
				$old_slot_to_use = $event->slot_to_use;
				for ($spc = 0; $spc < $event->_length && $dn + $spc < 7; $spc++)
				{
					if (isset($data['days'][$dn + $spc]["slots"][$old_slot_to_use]))
					{
						$data['days'][$dn + $spc]["slots"][$old_slot_to_use] = array(0, 0, 0);
					}
				}
				$event->slotreset = 1;
			}

			$slot_to_use = nextEmptySlot($data['days'][$dn]);
			$event->slot_to_use = $slot_to_use;

			// simplest case first - single day events
			// or multiday events set to only show once and it is the first day
			if ($event->endDate() == $event->startDate() || (!$event->multiday() && $currentDay["cellDate"] == $event->_startday))
			{
				// put the event in its slot
				$currentDay["slots"][$slot_to_use] = array($event, 1, $i);
			}

			if ($event->endDate() != $event->startDate() && $event->multiday() && !isset($event->_length))
			{
				// started last week?
				if ($dn == 0 && $currentDay["cellDate"] != $event->_startday)
				{
					$event->_length = JevDate::strtotime($event->endDate()) - $currentDay["cellDate"];
				}
				else
				{
					$event->_length = JevDate::strtotime($event->endDate()) - JevDate::strtotime($event->startDate());
				}
				$event->_length = intval(round($event->_length / 86400, 0)) + 1;

				// Must allow for events that started BEFORE the month or week in hand
				if ($currentDay["cellDate"] == $event->_startday || ($dn == 0 && $currentDay["cellDate"] > $event->_startday))
				{
					if ($dayOfWeek + $event->_length > 6)
					{
						$blocks = 7 - $dayOfWeek;
					}
					else
					{
						$blocks = $event->_length;
					}
					$data['days'][$dn]["slots"][$slot_to_use] = array($event, $blocks, $i);
					for ($block = 1; $block < $blocks; $block++)
					{
						$data['days'][$dn + $block]["slots"][$slot_to_use] = array($event, 0, $i);
					}
				}
			}
			else
			{
				$event->_length = 1;
			}
		}


		// mark event as shown
		$event->_shown = true;
	}
// determine rowspan in advance
	$weekslots[$week] = 0;
	$dn = 0;
	for ($d = 0; $d < 7 && $dn < $datacount; $d++)
	{
		unset($currentDay);
		$currentDay = $data["days"][$dn];
		if (count($currentDay["slots"]) > 0)
		{
			$weekslots[$week] = $weekslots[$week] < max(array_keys($currentDay["slots"])) + 1 ? max(array_keys($currentDay["slots"])) + 1 : $weekslots[$week];
		}
		$dn++;
	}

	$week++;
	unset($data);
}

$hiddendatearray = array();

// for the week numbers etc. we need the first week's data
$data = $extradata[0];
?>
<div id='jev_maincal' class='jev_<?php echo $this->colourscheme; ?>'>
	<div class="jev_toprow">
		<div class="jev_header">
			<h2><?php echo JText::_('WEEKLY_VIEW'); ?></h2>
			<div class="today" ><?php echo $data['startdate'] . ' - ' . $data['enddate']; ?></div>
		</div>
		<div class="jev_header2">
			<div class="jev_topleft jev_topleft_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>" ></div>
			<div class="jev_header2_container">
				<div class="previousmonth" >
					<?php
					if ($precedingWeek)
						echo "<a href='" . $precedingWeek . "' title='" . JText::_("PRECEEDING_Week") . "' >" . JText::_("PRECEEDING_Week") . "</a>";
					?>
				</div>
				<div class="currentmonth">
					<?php echo $data['startdate'] . ' - ' . $data['enddate']; ?>
				</div>
				<div class="nextmonth">
					<?php
					if ($followingWeek)
						echo "<a href='" . $followingWeek . "' title='" . JText::_("FOLLOWING_Week") . "' >" . JText::_("FOLLOWING_Week");
					?></a>
				</div>

				<?php
				foreach ($data["daynames"] as $dayname)
				{
					?>
					<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>">
						<span>
							<?php echo $dayname; ?>
						</span>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
    <div class="jev_clear" ></div>

	<table class="jev_row" cellspacing="0" cellpadding="0">
		<?php
		$week = 0;
		foreach ($extradata as & $data)
		{

			$dn = 0;
			$dn2 = 0;
			$dn3 = 0;
			?>
			<tr>
				<td class='jev_weeknum jev_weeknum_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>'>
					<img src="<?php echo JURI::root() . 'components/' . JEV_COM_COMPONENT . '/views/' . $this->getViewName() . '/assets/images/spacer.gif'; ?>" alt="spacer" class="jevspacer"/>
				</td>
				<td class="jevdaydata">
					<div class="jevdaydata">
						<?php
						$dn2 = $dn;
						for ($d = 0; $d < 7 && $dn < 7; $d++)
						{
							unset($currentDay);
							$currentDay = $data["days"][$dn];
							$cellclass = $currentDay["today"] ? 'jev_daynum_today jev_daynum' : 'jev_daynum_noevents jev_daynum';
							$cellclass.= $d == 6 ? ' jev_lastdaynum' : '';
							?>
							<div <?php echo 'class="' . $cellclass . '"'; ?>  >
								<?php $this->_datecellAddEvent($currentDay["week_year"], $currentDay["week_month"], $currentDay["week_day"]); ?>
								<a class="cal_daylink" href="<?php echo $currentDay["link"]; ?>" title="<?php echo JText::_('JEV_CLICK_TOSWITCH_DAY'); ?>"><?php echo $currentDay['week_day']; ?></a>
							</div>
							<?php
							$dn++;
						}
						?>
					</div>
					<?php
					for ($slot = 0; $slot < $weekslots[$week]; $slot++)
					{
						?>

						<div class="jeveventrow slots<?php echo $weekslots[$week]; ?>">
							<?php
							$dn3 = $dn2;
							for ($d = 0; $d < 7 && $dn3 < 7; $d++)
							{
								unset($currentDay);
								$currentDay = $data["days"][$dn3];
								$cellclass = $currentDay["today"] ? 'jev_today jevblocks1' : 'jev_daynoevents jevblocks1';

								if (array_key_exists($slot, $currentDay["slots"]))
								{
									$event = $currentDay["slots"][$slot][0];
									$blocks = $currentDay["slots"][$slot][1];
									$key = $currentDay["slots"][$slot][2];
									// reset class to include block count
									$cellclass = $currentDay["today"] ? 'jev_today jevblocks' . $blocks : 'jev_daynoevents jevblocks' . $blocks;

									if ($blocks > 0)
									{
										echo '<div class="' . $cellclass . ' jevstart_' . $event->getUnixStartTime() . '" >';

										$datestp = JevDate::mktime(0, 0, 0, $event->mup(), $event->dup(), $event->yup());
										$day_link = "<a href='" . $currentDay['link'] . "'>" . substr($event->_startrepeat, 11, 5) . "</a>";
										echo "<span class='hiddentime'>$day_link</span>";

										if ($cfg->get("flatscalabledayname", 1) && !in_array($datestp, $hiddendatearray) && (!isset($lastdatestp) || $datestp != $lastdatestp))
										{
											$lastdatestp = $datestp;
											$hiddendatearray[] = $datestp;
											?>
											<div class="hiddendayname jev_daysnames jev_daysnames_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>" style="display:none">
												<span>
													<?php
													$weekday = JevDate::strftime("%w", $currentDay["cellDate"]);

													// adjust day of week to reflect start day in config
													$weekday -= $weekstartday;
													if ($weekday < 0)
													{
														$weekday+=7;
													}

													if (strpos($data["daynames"][$weekday], ">") > 0)
													{
														$hiddendayname = substr($data["daynames"][$weekday], strpos($data["daynames"][$weekday], ">") + 1, strrpos($data["daynames"][$weekday], "<") - strpos($data["daynames"][$weekday], ">") - 1) . " " . JevDate::strftime("%d", $currentDay["cellDate"]);
													}
													else
														$hiddendayname = $data["daynames"][$weekday] . " " . JevDate::strftime("%d", $currentDay["cellDate"]);
													echo $hiddendayname;
													?>
												</span>
											</div>
											<?php
										}


										$ecc = new $eventCellClass($event, $this->datamodel, $this);
										echo $ecc->calendarCell($currentDay, $this->year, $this->month, $key, $slot);
										//echo $event->_summary. " ".$currentDay["slots"][$slot][1]." ".JevDate::strftime("%d",$event->_startday);
										echo '</div>';
										$currentDay['countDisplay']++;
									}
									else if (!$event)
									{
										echo "<div class='$cellclass' >&nbsp;</div>";
									}
								}
								else
								{
									echo "<div class='$cellclass' >&nbsp;</div>";
								}
								$dn3++;
							}
							?>
						</div>
						<?php
					}

					// Are any of these days capped
					$dn3 = $dn2;
					$capped = false;
					for ($d = 0; $d < 7 && $dn3 < 7; $d++)
					{
						unset($currentDay);
						$currentDay = $data["days"][$dn3];
						if ($currentDay["capped"])
							$capped = true;
						$dn3++;
					}
					// if capped then offer the link to more events
					if ($capped)
					{
						?>
						<div class="jeveventrow slots<?php echo $weekslots[$week] + 1; ?>">
							<?php
							$dn3 = $dn2;
							for ($d = 0; $d < 7 && $dn3 < 7; $d++)
							{
								unset($currentDay);
								$currentDay = $data["days"][$dn3];
								$cellclass = $currentDay["today"] ? 'jev_today jevblocks1' : 'jev_daynoevents jevblocks1';
								if ($currentDay["capped"])
								{
									echo "<div class='$cellclass' style='text-align:right'><span style='margin-right:5px'>";
									echo '<a class="cal_daylink" href="' . $currentDay["link"] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">' . JText::_('MORE') . ' ...</a></span>';
									echo '</div>';
								}
								else
								{
									echo "<div class='$cellclass' >&nbsp;</div>";
								}
								$dn3++;
							}
							?>
						</div>
						<?php
					}
					?>
					<div class="jev_underlay">
						<?php
						// Are any of these days capped
						$dn4 = $dn2;
						$capped = false;
						for ($d = 0; $d < 7 && $dn4 < $datacount; $d++)
						{
							unset($currentDay);
							$currentDay = $data["days"][$dn4];
							$class = "jev_underlay_daynum";
							if ($d == 0)
							{
								$class .= " jev_underlay_firstdaynum";
							}
							else if ($d == 6)
							{
								$class .= " jev_underlay_lastdaynum";
							}
							if ($currentDay["today"])
							{
								$class .= " jev_underlay_daynum_today";
							}
							$monthType = "current";
							$testym = $currentDay["week_year"] . $currentDay["week_month"];
							// Styled based on first and last week
							$startym = $extradata[0]["days"][0]["week_year"] . $extradata[0]["days"][0]["week_month"];
							$endym = $extradata[$howManyWeeksToShow-1]["days"][6]["week_year"] . $extradata[$howManyWeeksToShow-1]["days"][6]["week_month"];
							$testd = $currentDay["week_day"];
							$startd = $extradata[0]["days"][0]["week_day"];
							$endd =  $extradata[$howManyWeeksToShow-1]["days"][6]["week_day"];
							// start or end of month
							if ($startym != $endym)
							{
								// end of month
								if ($endd > 20)
								{
									if ($testym == $endym)
									{
										$monthType = "following";
									}
								}
								// start of month
								else
								{
									if ($testym != $endym)
									{
										$monthType = "prior";
									}
								}
							}

							switch ($monthType) {
								case "prior":
									$class .= " jev_underlay_outofmonth_start";
									break;
								case "following":
									$class .= " jev_underlay_outofmonth_end";
									break;
								case "current":
									break;
							}
							?>
							<div class="<?php echo $class; ?>">
								<div>&nbsp;</div>
							</div>
							<?php
							$dn4++;
						}
						?>

					</div>

				</td>
			</tr>
			<?php
			unset($data);
			$week++;
		}
		?>
	</table>
</div>

<div class="jev_clear"></div>

<?php
$this->eventsLegend();

function nextEmptySlot($currentDay)
{
	if (!array_key_exists("slots", $currentDay) || count($currentDay["slots"]) == 0)
		return 0;
	$maxpossible = max(array_keys($currentDay["slots"])) + 1;
	for ($key = 0; $key <= $maxpossible; $key++)
	{

		if (!array_key_exists($key, $currentDay["slots"]) || !$currentDay["slots"][$key])
		{
			return $key;
		}
	}
	return $maxpossible;

}