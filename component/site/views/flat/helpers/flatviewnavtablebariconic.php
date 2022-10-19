<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

class FlatViewNavTableBarIconic
{

	var $view = null;

	function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid)
	{

		global $catidsOut;
		$input = Factory::getApplication()->input;

		if ($input->getInt('pop', 0))
			return;
		$cfg      = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		//Lets check if we should show the nav on event details
		if ($task == "icalrepeat.detail" && $cfg->get('shownavbar_detail', 1) == 0)
		{
			return;
		}

		$this->iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
		$viewimages        = Uri::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images";

		$cat       = "";
		$hiddencat = "";
		if ($catidsOut != 0)
		{
			$cat       = '&catids=' . $catidsOut;
			$hiddencat = '<input type="hidden" name="catids" value="' . $catidsOut . '"/>';
		}

		$link       = 'index.php?option=' . $option . '&task=' . $task . $cat . '&Itemid=' . $Itemid . '&';
		$month_date = (JevDate::mktime(0, 0, 0, $view_date->month, $view_date->day, $view_date->year));
		?>
		<?php
		if ($task == "month.calendar")
		{
			echo "<div class='month_date'><div class='month_title'>" . JEV_CommonFunctions::jev_strftime("%B", $month_date) . ", </div><div class='month_title_year'>  " . JEV_CommonFunctions::jev_strftime("%Y", $month_date) . "</div></div>";
		}
		?>
		<div class="new-navigation">
			<div class="nav-items">
				<?php
				if (in_array("byyear", $this->iconstoshow))
				{
					?>
					<div id="nav-year"<?php
					if ($task == "year.listevents")
					{
						echo ' class="active"';
					}
					?> >
						<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=year.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
						   title="<?php echo Text::_('JEV_VIEWBYYEAR'); ?>">
							<?php echo Text::_('JEV_VIEWBYYEAR'); ?></a>
					</div>
				<?php } ?>
				<?php
				if (in_array("bymonth", $this->iconstoshow))
				{
					?>
					<div id="nav-month"<?php
					if ($task == "month.calendar")
					{
						echo ' class="active"';
					}
					?>>
						<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=month.calendar&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
						   title="<?php echo Text::_('JEV_VIEWBYMONTH'); ?>">                            <?php echo Text::_('JEV_VIEWBYMONTH'); ?></a>
					</div>
				<?php } ?>
				<?php
				if (in_array("byweek", $this->iconstoshow))
				{
					?>
					<div id="nav-week"<?php
					if ($task == "week.listevents")
					{
						echo ' class="active"';
					}
					?>>
						<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=week.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
						   title="<?php echo Text::_('JEV_VIEWBYWEEK'); ?>">
							<?php echo Text::_('JEV_VIEWBYWEEK'); ?></a>
					</div>
				<?php } ?>
				<?php
				if (in_array("byday", $this->iconstoshow))
				{
					?>
					<div id="nav-today"<?php
					if ($task == "day.listevents")
					{
						echo ' class="active"';
					}
					?>>
						<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=day.listevents&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
						   title="<?php echo Text::_('JEV_VIEWTODAY'); ?>">
							<?php echo Text::_('JEV_VIEWTODAY'); ?></a>
					</div>
				<?php } ?>
				<?php
				if (in_array("bymonth", $this->iconstoshow))
				{
					?>
					<?php
					echo $this->_viewJumptoIcon($view_date, $viewimages);
					?>
				<?php } ?>
				<?php
				if ($cfg->get('com_hideshowbycats', 0) == '0')
				{
					?>
					<?php
					if (in_array("bycat", $this->iconstoshow))
					{
						?>
						<div id="nav-cat"<?php
						if ($task == "cat.listevents")
						{
							echo ' class="active"';
						}
						?>>
							<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=cat.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
							   title="<?php echo Text::_('JEV_VIEWBYCAT'); ?>">
								<?php echo Text::_('JEV_VIEWBYCAT'); ?></a>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<?php
			if (in_array("search", $this->iconstoshow))
			{
				?>
				<div id="nav-search">
					<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=search.form&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
					   title="<?php echo Text::_('JEV_SEARCH_TITLE'); ?>">
						<img src="<?php echo $viewimages; ?>/icon-search.gif" alt="Search"/>
					</a>
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
		<div id="nav-jumpto">
			<a href="#"
			   onclick="if (jevjq('#jumpto').hasClass('jev_none')) {jevjq('#jumpto').removeClass('jev_none');} else {jevjq('#jumpto').addClass('jev_none')}return false;"
			   title="<?php echo Text::_('JEV_JUMPTO'); ?>">
				<?php echo Text::_('JEV_JUMPTO'); ?>
			</a>
		</div>
		<?php
	}

	function _viewHiddenJumpto($this_date, $view, $Itemid)
	{

		$hiddencat = "";
		if ($view->datamodel->catidsOut != 0)
		{
			$hiddencat = '<input type="hidden" name="catids" value="' . $view->datamodel->catidsOut . '"/>';
		}

        $index = Route::_("index.php?option=com_jevents&view=month&layout=calendar&Itemid=$Itemid");
		?>
		<div id="jumpto" class="jev_none">
			<form name="BarNav" action="<?php echo $index; ?>" method="get">
				<?php
				echo $hiddencat;
				/* Day Select */
				// JEventsHTML::buildDaySelect( $this_date->getYear(1), $this_date->getMonth(1), $this_date->getDay(1), ' style="font-size:10px;"' );
				/* Month Select */
				JEventsHTML::buildMonthSelect($this_date->getMonth(1), '');
				/* Year Select */
				JEventsHTML::buildYearSelect($this_date->getYear(1), '');
				?>
				<button onclick="submit(this.form)"><?php echo Text::_('JEV_JUMPTO'); ?></button>
			</form>
		</div>
		<?php
	}

}
