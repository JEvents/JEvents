<?php
defined('_JEXEC') or die('Restricted access');

class FlatViewNavTableBarIconic
{

	var
			$view = null;

	function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid)
	{
		global $catidsOut;

		if (JRequest::getInt('pop', 0))
			return;

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$this->iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
		$viewimages = JURI::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images";

		$cat = "";
		$hiddencat = "";
		if ($catidsOut != 0)
		{
			$cat = '&catids=' . $catidsOut;
			$hiddencat = '<input type="hidden" name="catids" value="' . $catidsOut . '"/>';
		}

		$link = 'index.php?option=' . $option . '&task=' . $task . $cat . '&Itemid=' . $Itemid . '&';
		?>
		<div class="new-navigation">
			<div class="nav-items">
				<?php if (in_array("byyear", $this->iconstoshow))
				{
					?>
					<div class="buttontext" id="nav-year">
						<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=year.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYYEAR'); ?>"  class="buttontext">
					<?php echo JText::_('JEV_VIEWBYYEAR'); ?></a>
					</div>
				<?php } ?>
				<?php if (in_array("bymonth", $this->iconstoshow))
				{
					?>
					<div class="buttontext" id="nav-month">
						<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=month.calendar&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYMONTH'); ?>" class="buttontext">							<?php echo JText::_('JEV_VIEWBYMONTH'); ?></a>
					</div>
				<?php } ?>
		<?php if (in_array("byweek", $this->iconstoshow))
		{
			?>
					<div class="buttontext" id="nav-week">
						<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=week.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYWEEK'); ?>" class="buttontext">
					<?php echo JText::_('JEV_VIEWBYWEEK'); ?></a>
					</div>
		<?php } ?>
						<?php if (in_array("byday", $this->iconstoshow))
						{
							?>
					<div class="buttontext" id="nav-today">
						<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=day.listevents&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWTODAY'); ?>" class="buttontext">
					<?php echo JText::_('JEV_VIEWTODAY'); ?></a>
					</div>
				<?php } ?>
				<?php if (in_array("bymonth", $this->iconstoshow))
				{
					?>
					<?php
					echo $this->_viewJumptoIcon($view_date, $viewimages);
					?>
		<?php } ?>
						<?php if ($cfg->get('com_hideshowbycats', 0) == '0')
						{
							?>
					<?php if (in_array("bycat", $this->iconstoshow))
					{
						?>
						<div class="buttontext" id="nav-cat">
							<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=cat.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYCAT'); ?>" class="buttontext">
				<?php echo JText::_('JEV_VIEWBYCAT'); ?></a>
						</div>
					<?php } ?>
				<?php } ?>
				<?php if (in_array("search", $this->iconstoshow))
				{
					?>
				</div>
					<div class="buttontext" id="nav-search">
						<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=search.form&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" class="buttontext">
							<img src="<?php echo $viewimages; ?>/icon-search.gif" alt="Search" /></a>
					</div>
			<?php } ?>
			<?php
			if (in_array("bymonth", $this->iconstoshow))
				echo $this->_viewHiddenJumpto($view_date, $view, $Itemid);
			?>

			
		</div>
				<?php

			}

			function _viewJumptoIcon($today_date, $viewimages)
			{
				?>
			<div class="buttontext"  id="nav-jumpto">
				<a  href="#"  onclick="jtdisp = document.getElementById('jumpto').style.display;
						document.getElementById('jumpto').style.display = (jtdisp == 'none') ? 'block' : 'none';
						return false;" title="<?php echo JText::_('JEV_JUMPTO'); ?>"><?php echo JText::_('JEV_JUMPTO'); ?></a>
			</div>                
			<?php

		}

		function _viewHiddenJumpto($this_date, $view, $Itemid)
		{
			$cfg = JEVConfig::getInstance();
			$hiddencat = "";
			if ($view->datamodel->catidsOut != 0)
			{
				$hiddencat = '<input type="hidden" name="catids" value="' . $view->datamodel->catidsOut . '"/>';
			}

			$index = JRoute::_("index.php");
			?>
			<div id="jumpto"  style="display:none">
				<form name="BarNav" action="<?php echo $index; ?>" method="get">
					<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
					<input type="hidden" name="task" value="month.calendar" />
					<?php
					echo $hiddencat;
					/* Day Select */
					// JEventsHTML::buildDaySelect( $this_date->getYear(1), $this_date->getMonth(1), $this_date->getDay(1), ' style="font-size:10px;"' );
					/* Month Select */
					JEventsHTML::buildMonthSelect($this_date->getMonth(1), 'style="font-size:10px;"');
					/* Year Select */
					JEventsHTML::buildYearSelect($this_date->getYear(1), 'style="font-size:10px;"');
					?>
					<button onclick="submit(this.form)"><?php echo JText::_('JEV_JUMPTO'); ?></button>
					<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
				</form>
			</div>
		<?php

	}

}
