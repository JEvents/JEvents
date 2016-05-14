<?php
defined('_JEXEC') or die('Restricted access');

class ExtViewNavTableBarIconic
{

	var
			$view = null;

	function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid)
	{
		global $catidsOut;

		$jinput = JFactory::getApplication()->input;

		if ($jinput->getInt('pop', 0))
			return;

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;
		//Lets check if we should show the nav on event details
		if ($task == "icalrepeat.detail" && $cfg->get('shownavbar_detail', 1) == 0)
		{
			return;
		}

		$this->iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));

		$viewimages = JURI::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images";

		$cat = "";
		$hiddencat = "";
		if ($catidsOut != 0)
		{
			$cat = '&catids=' . $catidsOut;
			$hiddencat = '<input type="hidden" name="catids" value="' . $catidsOut . '"/>';
		}

		// for popup editing
		$view->popup = false;
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
		{
			JevHtmlBootstrap::modal();
			JEVHelper::script('editpopupJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
			$view->popup = true;
			$view->popupw = $params->get("popupw", 800);
			$view->popuph = $params->get("popuph", 600);
		}

		$link = 'index.php?option=' . $option . '&task=' . $task . $cat . '&Itemid=' . $Itemid . '&';
		?>
		<table class="ev_navigation" bgcolor="#ffffff" border="0" cellpadding="10" cellspacing="0" width="100%" >
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
								$editLink = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.edit' . '&year=' . $year . '&month=' . $month . '&day=' . $day . '&Itemid=' . $view->Itemid, true);
								$eventlinkadd = $view->popup ? "javascript:jevEditPopup('" . $editLink . "');" : $editLink;
								?>
										<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
										<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
											<a href="<?php echo $eventlinkadd; ?>" title="Add Event" class="buttontext">
												<img src="<?php echo $viewimages; ?>/icon-addevent.gif" alt="Add Event" border="0"/><br/>
										<?php echo JText::_('JEV_ADD_EVENT'); ?></a>
										</td>
							<?php } ?>
								//-->
							<!-- END add_event -->
		<?php if (in_array("byyear", $this->iconstoshow))
		{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=year.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYYEAR'); ?>"  class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-flyer.gif" alt="Flat View" border="0"/><br/>
								<?php echo JText::_('JEV_VIEWBYYEAR'); ?></a>
								</td>
		<?php } ?>
		<?php if (in_array("bymonth", $this->iconstoshow))
		{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=month.calendar&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYMONTH'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-calendarview.gif" alt="<?php echo JText::_('MONTHLY_VIEW'); ?>" border="0"/><br/>
			<?php echo JText::_('JEV_VIEWBYMONTH'); ?></a>
								</td>
		<?php } ?>
									<?php if (in_array("byweek", $this->iconstoshow))
									{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=week.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYWEEK'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-weekly.gif" alt="Weekly View" border="0"/><br/>
								<?php echo JText::_('JEV_VIEWBYWEEK'); ?></a>
								</td>
		<?php } ?>
		<?php if (in_array("byday", $this->iconstoshow))
		{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=day.listevents&' . $today_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWTODAY'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-daily.gif" alt="Daily View" border="0"/><br/>
								<?php echo JText::_('JEV_VIEWTODAY'); ?></a>
								</td>
		<?php } ?>

									<?php if ($cfg->get('com_hideshowbycats', 0) == '0')
									{ ?>
								<?php if (in_array("bycat", $this->iconstoshow))
								{ ?>
									<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
									<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
										<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=cat.listevents&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_VIEWBYCAT'); ?>" class="buttontext">
											<img src="<?php echo $viewimages; ?>/icon-cats.gif" alt="Categories" border="0"/><br/>
				<?php echo JText::_('JEV_VIEWBYCAT'); ?></a>
									</td>
			<?php } ?>
		<?php } ?>
		<?php if (in_array("search", $this->iconstoshow))
		{ ?>
								<td><img name="spacer" src="<?php echo $viewimages; ?>/spacer.gif"  alt="" border="0" height="25" width="10"/></td>
								<td class="buttontext" align="center" nowrap="nowrap" valign="middle">
									<a href="<?php echo JRoute::_('index.php?option=' . $option . $cat . '&task=search.form&' . $view_date->toDateURL() . '&Itemid=' . $Itemid); ?>" title="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" class="buttontext">
										<img src="<?php echo $viewimages; ?>/icon-search.gif" alt="Search" border="0"/><br/>
			<?php echo JText::_('JEV_SEARCH_TITLE'); ?></a>
								</td>
		<?php } ?>

						</tr>
					</table>

		        </td>
			</tr></table>
		<?php

	}

}
