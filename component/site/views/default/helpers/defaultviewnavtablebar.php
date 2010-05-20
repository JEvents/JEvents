<?php 
defined('_JEXEC') or die('Restricted access');

class DefaultViewNavTableBar {

	var $view = null;
	
	function DefaultViewNavTableBar($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid ) {

		$cfg = & JEVConfig::getInstance();
		$this->view = $view	;
		$this->transparentGif = JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$this->view->getViewName()."/assets/images/transp.gif";
		$this->Itemid = JEVHelper::getItemid();
		$this->cat = $this->view->datamodel->getCatidsOutLink();
		$this->task = $task;
		
		if (JRequest::getInt( 'pop', 0 )) return;
		
		list($year,$month,$day) = JEVHelper::getYMD();		
    	?>
    	<div class="ev_navigation" style="width:100%">
    		<table width="300" border="0" align="center" >
    			<tr align="center" valign="top">
    				<td height="1" width="100" align="right" valign="top">
    					<a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=day.listevents&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_VIEWTODAY');?>"><?php echo JText::_('JEV_VIEWTODAY');?></a>
    				</td>
    				<td height="1" align="center" valign="bottom">
    					<form name="ViewSelect" action="index.php" method="get">
                            <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
                            <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
                            <input type="hidden" name="year" value="<?php echo $year;?>" />
                            <input type="hidden" name="month" value="<?php echo $month;?>" />
                            <input type="hidden" name="day" value="<?php echo $day;?>" />
                            <select name="task" id="task" onchange="submit(this.form);">
                            	<option value="day.listevents"><?php echo JText::_('JEV_VIEWBYDAY');?></option>
                            	<option value="week.listevents"><?php echo JText::_('JEV_VIEWBYWEEK');?></option>
                            	<option value="month.calendar"><?php echo JText::_('JEV_VIEWBYMONTH');?></option>
                            	<option value="year.listevents"><?php echo JText::_('JEV_VIEWBYYEAR');?></option>
                            	<option value="search.form"><?php echo JText::_('JEV_SEARCH_TITLE');?></option>
								<?php if ($cfg->get('com_hideshowbycats', 0) == '0') { ?>
                            	<option value="cat.listevents"><?php echo JText::_('JEV_VIEWBYCAT');?></option>
								<?php } ?>
                            </select>
                        </form>
                    </td>
                    <td height="1" width="100" align="left" valign="top">
                    	<a href="<?php echo JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . $this->cat . '&task=month.calendar&'. $today_date->toDateURL() . '&Itemid=' . $this->Itemid );?>" title="<?php echo  JText::_('JEV_VIEWTOCOME');?>">
    						<?php echo JText::_('JEV_VIEWTOCOME');?>
    					</a>
                    </td>
                </tr>
            </table>
	        <table width="300" border="0" align="center">
	        	<tr valign="top">
    	    		<?php 
    	    		echo $this->_lastYearIcon($dates, $alts);
    	    		echo $this->_lastMonthIcon($dates, $alts);
    	    		?>
	        		<td align="center" valign="top">
						<form name="BarNav" action="index.php" method="get">
							<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
							<input type="hidden" name="task" value="<?php echo $this->task;?>" />
							<?php
							/*Day Select*/
							JEventsHTML::buildDaySelect( $year, $month, $day, ' style="font-size:10px;" onchange="submit(this.form)"' );
							/*Month Select*/
							JEventsHTML::buildMonthSelect( $month, 'style="font-size:10px;" onchange="submit(this.form)"');
							/*Year Select*/
							JEventsHTML::buildYearSelect( $year, 'style="font-size:10px;" onchange="submit(this.form)"' ); ?>
							<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
						</form>
	        		</td>
    	    		<?php 
    	    		echo $this->_nextMonthIcon($dates, $alts);
    	    		echo $this->_nextYearIcon($dates, $alts);
    	    		?>
	        	</tr>
	        </table>

        </div>
		<?php    	
	}

	function _genericMonthNavigation($dates, $alts, $which, $icon){
		$cfg = & JEVConfig::getInstance();
		$task = $this->task;
		$link = 'index.php?option=' . JEV_COM_COMPONENT . '&task=' . $task . $this->cat . '&Itemid=' . $this->Itemid. '&';

		$gg	="<img border='0' src='"
		. JURI::root()
		. "components/".JEV_COM_COMPONENT."/views/".$this->view->getViewName()."/assets/images/$icon"."_"
		. $cfg->get('com_navbarcolor').".gif' alt='".$alts[$which]."'/>";

		$thelink = '<a href="'.JRoute::_($link.$dates[$which]->toDateURL()).'" title="'.$alts[$which].'">'.$gg.'</a>'."\n";
		if ($dates[$which]->getYear()>=$cfg->get('com_earliestyear') && $dates[$which]->getYear()<=$cfg->get('com_latestyear')){
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
	
	function _lastYearIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts, "prev2","gg");
	}

	function _lastMonthIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts,"prev1","g");
	}

	function _nextMonthIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts,"next1","d");
	}

	function _nextYearIcon($dates, $alts){
		$this->_genericMonthNavigation($dates, $alts,"next2","dd");
	}

}