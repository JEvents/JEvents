<?php
defined('_JEXEC') or die('Restricted access');

class AlternativeViewNavTableBarIconic
{

	var $view = null;

	function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid)
	{
		global $catidsOut;
		$jinput = JFactory::getApplication()->input;
		if ($jinput->getInt('pop', 0))
			return;
		
		$cat = "";
		$hiddencat = "";
		if ($view->datamodel->catidsOut != 0)
		{
			$cat = '&catids=' . $view->datamodel->catidsOut;
			$hiddencat = '<input type="hidden" name="catids" value="' . $view->datamodel->catidsOut . '"/>';
		}

		$link = 'index.php?option=' . $option . '&task=' . $task . $cat . '&Itemid=' . $Itemid . '&';

		$cfg = JEVConfig::getInstance();
		$this->iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
                //Lets check if we should show the nav on event details 
                if ($task == "icalrepeat.detail" && $cfg->get('shownavbar_detail', 1) == 0) { return;}
		
		$monthSelect = $view->buildMonthSelect($link, $view_date->month, $view_date->year);

		$transparentGif = JURI::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images/transp.gif";
		?>
		<div class="ev_navigation" style="width:100%">
			<table  border="0" >
				<tr valign="top">
					<?php if (in_array("bymonth", $this->iconstoshow))   { ?>
					<td class="iconic_td" align="center" valign="middle">
						<div id="ev_icon_monthly<?php echo ($task == "month.calendar") ? "_active" : "" ?>" class="nav_bar_cal" ><a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=month.calendar&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYMONTH'); ?>">
								<img src="<?php echo $transparentGif; ?>" alt="<?php echo JText::_('JEV_VIEWBYMONTH'); ?>"/></a>
						</div>
					</td>
					<?php } ?>
					<?php if (in_array("byweek", $this->iconstoshow))   { ?>
					<td class="iconic_td" align="center" valign="middle">
						<div id="ev_icon_weekly<?php echo $task == "week.listevents" ? "_active" : "" ?>" class="nav_bar_cal"><a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=week.listevents&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYWEEK'); ?>">
								<img src="<?php echo $transparentGif; ?>" alt="<?php echo JText::_('JEV_VIEWBYWEEK'); ?>"/></a>
						</div>
					</td>
					<?php } ?>
					<?php if (in_array("byday", $this->iconstoshow))   { ?>
					<td class="iconic_td" align="center" valign="middle">
						<div id="ev_icon_daily<?php echo $task == "day.listevents" ? "_active" : "" ?>" class="nav_bar_cal" ><a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=day.listevents&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWTODAY'); ?>"><img src="<?php echo $transparentGif; ?>" alt="<?php echo JText::_('JEV_VIEWBYDAY'); ?>"/></a>
						</div>
					</td>
					<?php } ?>
					<?php if (in_array("bymonth", $this->iconstoshow))   { ?>
					<td class="iconic_td" align="center" valign="middle">
		<?php echo $monthSelect; ?>
					</td>                    
					<?php } ?>
				</tr>
			</table>
		</div>
		<?php
		

	}

}