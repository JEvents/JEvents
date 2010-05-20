<?php 
defined('_JEXEC') or die('Restricted access');

JLoader::register('DefaultViewNavTableBarIconic',JEV_VIEWS."/default/helpers/defaultviewnavtablebariconic.php");

class GeraintViewNavTableBarIconic extends DefaultViewNavTableBarIconic {

	var $view = null;
	
	function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid ){
		//parent::DefaultViewNavTableBarIconic($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid);
		$this->view = $view	;
		$this->transparentGif = JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$this->view->getViewName()."/assets/images/transp.gif";
		$this->Itemid = JEVHelper::getItemid();
		$this->cat = $this->view->datamodel->getCatidsOutLink();
		$this->task = $task;
		
		$cfg = & JEVConfig::getInstance();
		
		if (JRequest::getInt( 'pop', 0 )) return;
    	?>
    	<div class="ev_navigation">
    		<table  >
    			<tr align="center" valign="top">
    	    		<?php 
    	    		if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){
    	    			echo $this->_lastYearIcon($dates, $alts);
    	    			echo $this->_lastMonthIcon($dates, $alts);
    	    		}
    	    		echo $this->_viewYearIcon($view_date);
    	    		echo $this->_viewMonthIcon($view_date);
    	    		echo $this->_viewWeekIcon($view_date);
    	    		echo $this->_viewDayIcon($today_date);
    	    		echo $this->_viewSearchIcon($view_date);
    	    		echo $this->_viewJumptoIcon($view_date);
    	    		if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){
	    	    		echo $this->_nextMonthIcon($dates, $alts);
    		    		echo $this->_nextYearIcon($dates, $alts);
    	    		}
    	    		?>
                </tr>
    			<tr class="icon_labels" align="center" valign="top">
    				<?php   if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){ ?>
	        		<td colspan="2"></td>
	        		<?php } ?>
    				<td><?php echo JText::_('JEV_VIEWBYYEAR');?></td>
    				<td><?php echo JText::_('JEV_VIEWBYMONTH');?></td>
    				<td><?php echo JText::_('JEV_VIEWBYWEEK');?></td>
    				<td><?php echo JText::_('JEV_VIEWTODAY');?></td>
    				<td><?php echo JText::_('JEV_SEARCH_TITLE');?></td>
    				<td><?php echo  JText::_('JEV_JUMPTO');?></td>
    				<?php   if($cfg->get('com_calUseIconic', 1) != 2  && $task!="range.listevents"){ ?>
	        		<td colspan="2"></td>
	        		<?php } ?>
                </tr>
                <?php
                echo $this->_viewHiddenJumpto($view_date);
                ?>
            </table>
        </div>
		<?php    	
	}

}