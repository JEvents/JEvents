<?php 
defined('_JEXEC') or die('Restricted access');

class DefaultViewNavTableBarIconic {

	var $view = null;
	
	public function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid ) {

		$this->view = $view	;
		$this->transparentGif = JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$this->view->getViewName()."/assets/images/transp.gif";
		$this->Itemid = JEVHelper::getItemid();
		$this->cat = $this->view->datamodel->getCatidsOutLink();
		$this->task = $task;
		$jinput = JFactory::getApplication()->input;

		$cfg = JEVConfig::getInstance();
                
                //Lets check if we should show the nav on event details 
                if ($task == "icalrepeat.detail" && $cfg->get('shownavbar_detail', 1) == 0) { return;}
		
		$this->iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
		
		if ($jinput->getInt('pop', 0 )) return;
				
    	?>
    	<div class="ev_navigation">
    		<table class="b0" align="center" >
    			<tr align="center" class="vtop">
    	    		<?php 
    	    		if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){
    	    			echo $this->_lastYearIcon($dates, $alts);
    	    			echo $this->_lastMonthIcon($dates, $alts);
    	    		}
    	    		if (in_array("byyear", $this->iconstoshow)) echo $this->_viewYearIcon($view_date);
    	    		if (in_array("bymonth", $this->iconstoshow)) echo $this->_viewMonthIcon($view_date);
    	    		if (in_array("byweek", $this->iconstoshow)) echo $this->_viewWeekIcon($view_date);
    	    		if (in_array("byday", $this->iconstoshow)) echo $this->_viewDayIcon($today_date);
    	    		if (in_array("search", $this->iconstoshow)) echo $this->_viewSearchIcon($view_date);
    	    		if (in_array("bymonth", $this->iconstoshow)) echo $this->_viewJumptoIcon($view_date);
    	    		if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){
	    	    		echo $this->_nextMonthIcon($dates, $alts);
    		    		echo $this->_nextYearIcon($dates, $alts);
    	    		}
    	    		?>
                </tr>
    			<tr class="icon_labels" align="center">
    				<?php   if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){ ?>
	        		<td colspan="2"></td>
	        		<?php } ?>
    				<?php if (in_array("byyear", $this->iconstoshow))   { ?><td><?php echo JText::_('JEV_VIEWBYYEAR');?></td><?php } ?>
    				<?php if (in_array("bymonth", $this->iconstoshow))   { ?><td><?php echo JText::_('JEV_VIEWBYMONTH');?></td><?php } ?>
    				<?php if (in_array("byweek", $this->iconstoshow))   { ?><td><?php echo JText::_('JEV_VIEWBYWEEK');?></td><?php } ?>
    				<?php if (in_array("byday", $this->iconstoshow))   { ?><td><?php echo JText::_('JEV_VIEWTODAY');?></td><?php } ?>
    				<?php if (in_array("search", $this->iconstoshow))   { ?><td><?php echo JText::_('JEV_SEARCH_TITLE');?></td><?php } ?>
    				<?php if (in_array("bymonth", $this->iconstoshow))   { ?><td><?php echo  JText::_('JEV_JUMPTO');?></td><?php } ?>
    				<?php   if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){ ?>
	        		<td colspan="2"></td>
	        		<?php } ?>
                </tr>
                <?php
                 if (in_array("bymonth", $this->iconstoshow))   echo $this->_viewHiddenJumpto($view_date);
                ?>
            </table>
        </div>
		<?php    	

	}

	public function _genericMonthNavigation($dates, $alts, $which, $icon){
		$cfg = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$task = $this->task;
		$link = 'index.php?option=' . JEV_COM_COMPONENT . '&task=' . $task . $this->cat . '&Itemid=' . $this->Itemid. '&';

		$gg	="<img border='0' src='"
		. JURI::root()
		. "components/".JEV_COM_COMPONENT."/views/".$this->view->getViewName()."/assets/images/$icon"."_"
		. $cfg->get('com_navbarcolor').".gif' alt='".$alts[$which]."'/>";

		$thelink = '<a href="'.JRoute::_($link.$dates[$which]->toDateURL()).'" title="'.$alts[$which].'">'.$gg.'</a>'."\n";
		if ($dates[$which]->getYear()>=JEVHelper::getMinYear() && $dates[$which]->getYear()<=JEVHelper::getMaxYear()){
		?>
    	<td width="10" align="center" valign="middle"><?php echo $thelink; ?></td>
		<?php		
		}
		else {
		?>
    	<td width="10" align="center" valign="middle"></td>
		<?php		
		}
	}
	
	public function _lastYearIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts, "prev2","gg");
	}

	public function _lastMonthIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts,"prev1","g");
	}

	public function _nextMonthIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts,"next1","d");
	}

	public function _nextYearIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts,"next2","dd");
	}

	public function _viewYearIcon($today_date) {
		?>
		<td class="iconic_td" align="center" valign="middle">
    		<div id="ev_icon_yearly" class="nav_bar_cal"><a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=year.listevents&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_VIEWBYYEAR');?>"> 
    			<img src="<?php echo $this->transparentGif;?>" alt="<?php echo JText::_('JEV_VIEWBYYEAR');?>"/></a>
    		</div>
        </td>
        <?php
	}

	public function _viewMonthIcon($today_date) {
		?>
    	<td class="iconic_td" align="center" valign="middle">
    		<div id="ev_icon_monthly" class="nav_bar_cal" ><a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=month.calendar&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_VIEWBYMONTH');?>">
    			<img src="<?php echo $this->transparentGif;?>" alt="<?php echo JText::_('JEV_VIEWBYMONTH');?>"/></a>
    		</div>
        </td>
        <?php
	}

	public function _viewWeekIcon($today_date) {
		?>
		<td class="iconic_td" align="center" valign="middle">
			<div id="ev_icon_weekly" class="nav_bar_cal"><a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=week.listevents&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_VIEWBYWEEK');?>">
			<img src="<?php echo $this->transparentGif;?>" alt="<?php echo JText::_('JEV_VIEWBYWEEK');?>"/></a>
			</div>
        </td>
        <?php
	}

	public function _viewDayIcon($today_date) {
		?>
		<td class="iconic_td" align="center" valign="middle">
			<div id="ev_icon_daily" class="nav_bar_cal" ><a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=day.listevents&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_VIEWTODAY');?>"><img src="<?php echo $this->transparentGif;?>" alt="<?php echo JText::_('JEV_VIEWBYDAY');?>"/></a>
			</div>
        </td>
        <?php
	}

	public function _viewSearchIcon($today_date) {
		?>
		<td class="iconic_td" align="center" valign="middle">
			<div id="ev_icon_search" class="nav_bar_cal"><a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=search.form&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_SEARCH_TITLE');?>"><img src="<?php echo $this->transparentGif;?>" alt="<?php echo JText::_('JEV_SEARCH_TITLE');?>"/></a>
			</div>
        </td>                
        <?php
	}

	public function _viewJumptoIcon($today_date) {
		?>
		<td class="iconic_td" align="center" valign="middle">
			<div id="ev_icon_jumpto" class="nav_bar_cal"><a href="#" onclick="if (jevjq('#jumpto').hasClass('jev_none')) {jevjq('#jumpto').removeClass('jev_none');} else {jevjq('#jumpto').addClass('jev_none')};return false;" title="<?php echo   JText::_('JEV_JUMPTO');?>"><img src="<?php echo $this->transparentGif;?>" alt="<?php echo  JText::_('JEV_JUMPTO');?>"/></a>
			</div>
        </td>                
        <?php
	}

	public function _viewHiddenJumpto($this_date){
		$cfg = JEVConfig::getInstance();
		$hiddencat	= "";
		if ($this->view->datamodel->catidsOut!=0){
			$hiddencat = '<input type="hidden" name="catids" value="'.$this->view->datamodel->catidsOut.'"/>';
		}
		?>
		<tr align="center" valign="top">
			<?php   if($cfg->get('com_calUseIconic', 1) != 2){ ?>
	    	<td colspan="10" align="center" valign="top">
	    	<?php }
	    	else {?>
	    	<td colspan="6" align="center" valign="top">
	    	<?php }
			$index = JRoute::_("index.php");
	    	?>
	    	<div id="jumpto"  class="jev_none">
			<form name="BarNav" action="<?php echo $index;?>" method="get">
				<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
				<input type="hidden" name="task" value="month.calendar" />
				<?php
				echo $hiddencat;
				/*Day Select*/
				// JEventsHTML::buildDaySelect( $this_date->getYear(1), $this_date->getMonth(1), $this_date->getDay(1), ' class="fs10px"' );
				/*Month Select*/
				JEventsHTML::buildMonthSelect( $this_date->getMonth(1), 'class="fs10px"');
				/*Year Select*/
				JEventsHTML::buildYearSelect( $this_date->getYear(1), 'class="fs10px"' ); ?>
				<button onclick="submit(this.form)"><?php echo   JText::_('JEV_JUMPTO');?></button>
				<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
			</form>
			</div>
			</td>
	    </tr>
		<?php
	}
}