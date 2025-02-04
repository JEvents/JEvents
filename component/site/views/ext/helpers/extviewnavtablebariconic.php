<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;


class ExtViewNavTableBarIconic
{

	var
		$view = null;

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

		$viewimages = Uri::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images";

		$cat       = "";
		$hiddencat = "";
		if ($catidsOut != 0)
		{
			$cat       = '&catids=' . $catidsOut;
			$hiddencat = '<input type="hidden" name="catids" value="' . $catidsOut . '"/>';
		}

		// for popup editing
		$view->popup = false;
		$params      = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
		{
			JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
			JevModal::framework();

			$view->popup  = true;
			$view->popupw = $params->get("popupw", 800);
			$view->popuph = $params->get("popuph", 600);
		}

		$link = 'index.php?option=' . $option . '&task=' . $task . $cat . '&Itemid=' . $Itemid . '&';
		?>
		<table class="ev_navigation" bgcolor="#ffffff" border="0" cellpadding="10" cellspacing="0" width="100%">
			<tr>
				<td class="tableh1" align="center">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<!-- BEGIN add_event -->
							<!--//
							<?php
							if (JEVHelper::isEventCreator())
							{
								list($year, $month, $day) = JEVHelper::getYMD();
								$editLink     = Route::_('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.edit' . '&year=' . $year . '&month=' . $month . '&day=' . $day . '&Itemid=' . $view->Itemid, true);
								$eventlinkadd = $view->popup ? "javascript:jevModalPopup('myEditModal','" . $editLink . "');" : $editLink;

								?>
										<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
										<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
											<a href="<?php echo $eventlinkadd; ?>" title="Add Event" class="buttontext">
												<img src="<?php echo $viewimages; ?>/icon-addevent.gif" alt="Add Event" border="0"/><br/>
										<?php echo Text::_('JEV_ADD_EVENT'); ?></a>
										</td>
							<?php } ?>
								//-->
							<!-- END add_event -->
							<?php if (in_array("byyear", $this->iconstoshow))
							{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif" alt="" border="0"
								         height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=year.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
									   title="<?php echo Text::_('JEV_VIEWBYYEAR'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-flyer.gif" alt="Flat View"
										     border="0"/><br/>
										<?php echo Text::_('JEV_VIEWBYYEAR'); ?></a>
								</td>
							<?php } ?>
							<?php if (in_array("bymonth", $this->iconstoshow))
							{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif" alt="" border="0"
								         height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=month.calendar&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
									   title="<?php echo Text::_('JEV_VIEWBYMONTH'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-calendarview.gif"
										     alt="<?php echo Text::_('MONTHLY_VIEW'); ?>" border="0"/><br/>
										<?php echo Text::_('JEV_VIEWBYMONTH'); ?></a>
								</td>
							<?php } ?>
							<?php if (in_array("byweek", $this->iconstoshow))
							{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif" alt="" border="0"
								         height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=week.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
									   title="<?php echo Text::_('JEV_VIEWBYWEEK'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-weekly.gif" alt="Weekly View"
										     border="0"/><br/>
										<?php echo Text::_('JEV_VIEWBYWEEK'); ?></a>
								</td>
							<?php } ?>
							<?php if (in_array("byday", $this->iconstoshow))
							{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif" alt="" border="0"
								         height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=day.listevents&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
									   title="<?php echo Text::_('JEV_VIEWTODAY'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-daily.gif" alt="Daily View"
										     border="0"/><br/>
										<?php echo Text::_('JEV_VIEWTODAY'); ?></a>
								</td>
							<?php } ?>

                            <?php if (in_array("bymonth", $this->iconstoshow))   { ?>
                                <?php
                                echo $this->_viewJumptoIcon($view_date, $viewimages);
                                ?>
                            <?php } ?>

							<?php if ($cfg->get('com_hideshowbycats', 0) == '0')
							{ ?>
								<?php if (in_array("bycat", $this->iconstoshow))
							{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif" alt="" border="0"
								         height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=cat.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
									   title="<?php echo Text::_('JEV_VIEWBYCAT'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-cats.gif" alt="Categories"
										     border="0"/><br/>
										<?php echo Text::_('JEV_VIEWBYCAT'); ?></a>
								</td>
							<?php } ?>
							<?php } ?>
							<?php if (in_array("search", $this->iconstoshow))
							{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif" alt="" border="0"
								         height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo Route::_('index.php?option=' . $option . $cat . '&task=search.form&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>"
									   title="<?php echo Text::_('JEV_SEARCH_TITLE'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-search.gif" alt="Search"
										     border="0"/><br/>
										<?php echo Text::_('JEV_SEARCH_TITLE'); ?></a>
								</td>
							<?php } ?>

						</tr>
					</table>

				</td>
			</tr>
            <?php
            if (in_array("bymonth", $this->iconstoshow))   echo $this->_viewHiddenJumpto($view_date, $view, $Itemid);
            ?>
		</table>
		<?php

	}

    function _viewJumptoIcon($today_date, $viewimages)
    {
        ?>
		<td class="buttontext" align="center"  nowrap="nowrap" >
			<a href="#" onclick="if (jevjq('#jumpto').hasClass('jev_none')) {jevjq('#jumpto').removeClass('jev_none');} else {jevjq('#jumpto').addClass('jev_none')};return false;" title="<?php echo   Text::_('JEV_JUMPTO');?>">
				<img src="<?php echo $viewimages;?>/jumpto.gif" alt="<?php echo Text::_('JEV_JUMPTO', true);?>" /><br/>
                <?php echo Text::_('JEV_JUMPTO');?></a>
		</td>
        <?php
    }

    function _viewHiddenJumpto($this_date, $view,$Itemid){
        $cfg = JEVConfig::getInstance();
        $hiddencat	= "";
        if ($view->datamodel->catidsOut!=0){
            $hiddencat = '<input type="hidden" name="catids" value="'.$view->datamodel->catidsOut.'"/>';
        }
        ?>
		<tr align="center" valign="top">
            <?php   if($cfg->get('com_calUseIconic', 1) != 2){ ?>
			<td colspan="10" align="center" valign="top">
                <?php }
                else {?>
			<td colspan="6" align="center" valign="top">
                <?php }
                $index = Route::_("index.php");
                ?>
				<div id="jumpto"  class="jev_none">
					<form name="BarNav" action="<?php echo $index;?>" method="get">
						<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
						<input type="hidden" name="task" value="month.calendar" />
                        <?php
                        echo $hiddencat;
                        /*Day Select*/
                        // JEventsHTML::buildDaySelect( $this_date->getYear(1), $this_date->getMonth(1), $this_date->getDay(1), ' style="font-size:10px;"' );
                        /*Month Select*/
                        JEventsHTML::buildMonthSelect( $this_date->getMonth(1), 'class="extp_month_select"');
                        /*Year Select*/
                        JEventsHTML::buildYearSelect( $this_date->getYear(1), 'class="extp_year_select"' ); ?>
						<button onclick="submit(this.form)"><?php echo   Text::_('JEV_JUMPTO');?></button>
						<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
					</form>
				</div>
			</td>
		</tr>
        <?php
    }


}
