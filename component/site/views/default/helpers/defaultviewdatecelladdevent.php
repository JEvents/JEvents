<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultViewDatecellAddEvent($view, $year, $month, $day){
	if (JEVHelper::isEventCreator()){
	// no events on Saturday or Sunday!
	//if (date("N",mktime(0,0,0,$month,$day, $year))>5) return;
    	$editLink = JRoute::_('index.php?option=' . JEV_COM_COMPONENT
    	. '&task=icalevent.edit' . '&year=' . $year . '&month=' . $month . '&day=' . $day. '&Itemid=' . $view->Itemid, true);
    	 $eventlinkadd = $view->popup?"javascript:jevEditPopup('".$editLink."',$view->popupw, $view->popuph);":$editLink;
    	 $transparentGif = JURI::root() . "components/".JEV_COM_COMPONENT."/views/".$view->getViewName()."/assets/images/transp.gif";
    	 ?>
    	<a href="<?php echo $eventlinkadd; ?>" title="<?php echo JText::_('JEV_ADDEVENT');?>" class="addjevent">
        	<img src="<?php echo $transparentGif;?>" alt="<?php echo JText::_('JEV_ADDEVENT');?>"/>
    	</a>
    	<?php
    }
	
}
