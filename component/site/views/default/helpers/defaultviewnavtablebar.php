<?php
defined('_JEXEC') or die('Restricted access');

class DefaultViewNavTableBar
{

	var $view = null;

	function DefaultViewNavTableBar($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid)
	{
		$jinput = JFactory::getApplication()->input;

		$cfg = JEVConfig::getInstance();
		$this->view = $view;
		$this->transparentGif = JURI::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $this->view->getViewName() . "/assets/images/transp.gif";
		$this->Itemid = JEVHelper::getItemid();
		$this->cat = $this->view->datamodel->getCatidsOutLink();
		$this->task = $task;

		if ($jinput->getInt('pop', 0))
			return;

		list($year, $month, $day) = JEVHelper::getYMD();
		$iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
		?>
		<div class="ev_navigation">
			<table class="ev_navtable" align="center">
				<tr align="center">
					<td align="right" class="h1 vtop">
						<a href="<?php echo JRoute::_('index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=day.listevents&' . $today_date->toDateURL() . '&Itemid=' . $this->Itemid); ?>" title="<?php echo JText::_('JEV_VIEWTODAY'); ?>"><?php echo JText::_('JEV_VIEWTODAY'); ?></a>
					</td>
					<td align="center" class="h1 vbotom">
						<form name="ViewSelect" action="<?php echo JURI::root() ."index.php"; ?>" method="get">
							<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
							<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
							<input type="hidden" name="year" value="<?php echo $year; ?>" />
							<input type="hidden" name="month" value="<?php echo $month; ?>" />
							<input type="hidden" name="day" value="<?php echo $day; ?>" />
                            <?php
                            $jinput = JFactory::getApplication()->input;
                            $v = $jinput->get('task', 'none');
?>
                            <select name="task" id="task" onchange="submit(this.form);">
								<?php if (in_array("byday", $iconstoshow))
								{ ?>
									<option value="day.listevents" <?php if ($v == "day.listevents") { echo "selected";}?>><?php echo JText::_('JEV_VIEWBYDAY'); ?></option>
									<?php
								}
								if (in_array("byweek", $iconstoshow))
								{
									?>
									<option value="week.listevents"<?php if ($v == "week.listevents") { echo "selected";}?>><?php echo JText::_('JEV_VIEWBYWEEK'); ?></option>
									<?php
								}
								if (in_array("bymonth", $iconstoshow))
								{
									?>
									<option value="month.calendar"<?php if ($v == "month.calendar") { echo "selected";}?>><?php echo JText::_('JEV_VIEWBYMONTH'); ?></option>
									<?php
								}
								if (in_array("byyear", $iconstoshow))
								{
									?>
									<option value="year.listevents"<?php if ($v == "year.listevents") { echo "selected";}?>><?php echo JText::_('JEV_VIEWBYYEAR'); ?></option>
									<?php
								}
								if (in_array("search", $iconstoshow))
								{
									?>
									<option value="search.form"<?php if ($v == "search.form") { echo "selected";}?>><?php echo JText::_('JEV_SEARCH_TITLE'); ?></option>
									<?php
								}
								if (in_array("bycat", $iconstoshow))
								{
									?>
									<option value="cat.listevents"<?php if ($v == "cat.listevents") { echo "selected";}?>><?php echo JText::_('JEV_VIEWBYCAT'); ?></option>
									<?php
								}
								?>
							</select>
						</form>
					</td>
					<td align="left" class="w100 vtop h1">
						<a href="<?php echo JRoute::_('index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=month.calendar&' . $today_date->toDateURL() . '&Itemid=' . $this->Itemid); ?>" title="<?php echo JText::_('JEV_VIEWTOCOME'); ?>">
							<?php echo JText::_('JEV_VIEWTOCOME'); ?>
						</a>
					</td>
				</tr>
			</table>
			<table align="center" class="t300 b0">
				<tr valign="top">
					<?php
					if (in_array("byyear", $iconstoshow))
					{
						echo $this->_lastYearIcon($dates, $alts);
					}
					if (in_array("bymonth", $iconstoshow))
					{
						echo $this->_lastMonthIcon($dates, $alts);
					}
					?>
					<td align="center" class="vtop">
						<form name="BarNav" action="<?php echo JURI::root() ."index.php"; ?>" method="get">
							<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
							<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
							<?php
							/* Day Select */
							JEventsHTML::buildDaySelect($year, $month, $day, ' style="font-size:10px;" onchange="submit(this.form)"');
							/* Month Select */
							JEventsHTML::buildMonthSelect($month, 'style="font-size:10px;" onchange="submit(this.form)"');
							/* Year Select */
							JEventsHTML::buildYearSelect($year, 'style="font-size:10px;" onchange="submit(this.form)"');
							?>
							<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
						</form>
					</td>
					<?php
					if (in_array("bymonth", $iconstoshow))
					{
						echo $this->_nextMonthIcon($dates, $alts);
					}
					if (in_array("byyear", $iconstoshow))
					{
						echo $this->_nextYearIcon($dates, $alts);
					}
					?>
				</tr>
			</table>

		</div>
		<?php

	}

	function _genericMonthNavigation($dates, $alts, $which, $icon)
	{
		$cfg = JEVConfig::getInstance();
		$task = $this->task;
		$link = 'index.php?option=' . JEV_COM_COMPONENT . '&task=' . $task . $this->cat . '&Itemid=' . $this->Itemid . '&';

		$gg = "<img border='0' src='"
				. JURI::root()
				. "components/" . JEV_COM_COMPONENT . "/views/default/assets/images/$icon" . "_"
				. $cfg->get('com_navbarcolor') . ".gif' alt='" . $alts[$which] . "'/>";

		$thelink = '<a href="' . JRoute::_($link . $dates[$which]->toDateURL()) . '" title="' . $alts[$which] . '">' . $gg . '</a>' . "\n";
		if ($dates[$which]->getYear() >= JEVHelper::getMinYear() && $dates[$which]->getYear() <= JEVHelper::getMaxYear())
		{
			?>
			<td class="w10px vmiddle" align="center"><?php echo $thelink; ?></td>
			<?php
		}
		else
		{
			?>
			<td class="w10px vmiddle" align="center"></td>
			<?php
		}

	}

	function _lastYearIcon($dates, $alts)
	{
		$this->_genericMonthNavigation($dates, $alts, "prev2", "gg");

	}

	function _lastMonthIcon($dates, $alts)
	{
		$this->_genericMonthNavigation($dates, $alts, "prev1", "g");

	}

	function _nextMonthIcon($dates, $alts)
	{
		$this->_genericMonthNavigation($dates, $alts, "next1", "d");

	}

	function _nextYearIcon($dates, $alts)
	{
		$this->_genericMonthNavigation($dates, $alts, "next2", "dd");

	}

}