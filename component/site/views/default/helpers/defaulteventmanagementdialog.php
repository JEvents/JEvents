<?php 
defined('_JEXEC') or die('Restricted access');

/**
	 * Creates mini event dialog for view detail page etc.
	 * note this must be contained in a position:relative block element in order to work
	 *
	 * @param Jevent or descendent $row
	 */
function DefaultEventManagementDialog($view,$row, $mask){

	$user = JFactory::getUser();
	if ($user->get("id")==0) return "";
	if( (JEVHelper::canEditEvent($row) || JEVHelper::canPublishEvent($row)|| JEVHelper::canDeleteEvent($row))  && !( $mask & MASK_POPUP )) {

		$popup=false;
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup",0)){
			JHTML::_('behavior.modal');
			JEVHelper::script('editpopup.js','components/'.JEV_COM_COMPONENT.'/assets/js/');
			$popup=true;
			$popupw = $params->get("popupw",800);
			$popuph = $params->get("popuph",600);
		}

		$hasrepeat = false;
		$pathIMG = JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images';
		$editImg = $pathIMG."/edit_f2.png";
		$editLink = $row->editLink();
		$editLink = $popup?"javascript:jevEditPopup('".$editLink."',$popupw, $popuph);":$editLink;
		$editCopyImg = $pathIMG."/copy_f2.png";
		$editCopyLink = $row->editCopyLink();
		$editCopyLink = $popup?"javascript:jevEditPopup('".$editCopyLink."',$popupw, $popuph);":$editCopyLink;
		$deleteImg = $pathIMG."/delete_f2.png";
		$deleteLink = $row->deleteLink();
		if ($row->until()!=$row->dtstart() || $row->count()>1){

			$hasrepeat = true;

			$editRepeatImg = $pathIMG."/edit_f2.png";
			$editRepeatLink = $row->editRepeatLink();
			$editRepeatLink = $popup?"javascript:jevEditPopup('".$editRepeatLink."',$popupw, $popuph);":$editRepeatLink;
			$deleteRepeatImg = $pathIMG."/delete_f2.png";
			$deleteRepeatLink = $row->deleteRepeatLink();
			$deleteFutureImg = $pathIMG."/delete_f2.png";
			$deleteFutureLink = $row->deleteFutureLink();
		}
		else {
			$editRepeatLink ="";
			$deleteRepeatLink = "";
			$deleteFutureLink = "";
		}

		if (!JEVHelper::canEditEvent($row)){
			$editLink = "";
			$editRepeatLink = "";
			$editCopyLink = "";
		}

		if (!JEVHelper::canDeleteEvent($row)){
			$deleteLink = "";
			$deleteRepeatLink = "";
			$deleteFutureLink = "";
		}

		$publishLink = "";
		if (JEVHelper::canPublishEvent($row)){
			if ($row->published()>0){
				$publishImg =  $pathIMG."/publish_r.png";
				$publishLink = $row->unpublishLink();
				$publishText = JText::_( 'UNPUBLISH_EVENT' );
			}
			else {
				$publishImg =  $pathIMG."/publish_g.png";
				$publishLink = $row->publishLink();
				$publishText = JText::_( 'PUBLISH_EVENT' );
			}
		}

		if ($publishLink.$editRepeatLink.$editLink.$deleteRepeatLink.$deleteLink.$deleteFutureLink == "") {
			return false;
		}

            ?>
            <div id="action_dialog" >
            	<div class="close_dialog" >					
            		<a href="javascript:void(0)" onclick="closedialog()" >x</a>
            	</div>
                 <?php
                 if ($publishLink!=""){
                 ?>
                 <a href="<?php echo $publishLink;?>" id="publish_reccur"  title="<?php echo $publishText;?>" ><img src="<?php echo $publishImg; ?>" alt="" /><?php echo $publishText;?></a><br/>
                 <?php
                 }
                 ?>
                 <?php
                 if ($editRepeatLink!=""){
                 ?>
                 <a href="<?php echo $editRepeatLink;?>" id="edit_reccur"  title="edit event" ><img src="<?php echo $editRepeatImg; ?>" alt="" /><?php echo JText::_( 'EDIT_REPEAT' );?></a><br/>
                 <?php
                 }
                 if ($editLink!=""){
                 ?>
            	<a href="<?php echo $editLink;?>" id="edit_event" title="edit event" ><img src="<?php echo $editImg; ?>" alt="" /><?php echo JText::_( 'EDIT_EVENT' );?></a><br/>
            	<a href="<?php echo $editCopyLink;?>" id="edit_eventcopy" title="edit event" ><img src="<?php echo $editCopyImg; ?>" alt="" /><?php echo JText::_( 'COPY_AND_EDIT_EVENT' );?></a><br/>
                 <?php
                 }
                 if ($deleteRepeatLink!=""){
                 ?>
                 <a href="<?php echo $deleteRepeatLink;?>" onclick="return confirm('<?php echo JText::_( 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECURRENCE', true );?>')" id="delete_repeat"  title="delete repeat" ><img src="<?php echo $deleteRepeatImg; ?>" alt="" /><?php echo JText::_( 'DELETE_THIS_REPEAT' );?></a><br/>
                 <?php
                 }
                 if ($deleteLink!=""){
                 ?>
                 <a href="<?php echo $deleteLink;?>" onclick="return confirm('<?php echo JText::_($hasrepeat?'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT_AND_ALL_ITS_REPEAT':'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT', true);?>')" id="delete_event"  title="delete event" ><img src="<?php echo $deleteImg; ?>" alt="" /><?php echo JText::_($hasrepeat?"DELETE_ALL_REPEATS":"DELETE_EVENT");?></a><br/>
            	<?php
                 }
                 if ($deleteFutureLink!=""){
                 ?>
                 <a href="<?php echo $deleteFutureLink;?>" onclick="return confirm('<?php echo JText::_( 'ARE_YOU_SURE_YOU_WITH_TO_DELETE_THIS_EVENT_AND_ALL_FUTURE_REPEATS', true )?>')" id="delete_eventfuture"  title="delete event" ><img src="<?php echo $deleteFutureImg; ?>" alt="" /><?php echo JText::_( 'JEV_DELETE_FUTURE_REPEATS' );?></a><br/>
            <?php

                 }
                 ?>
	        </div>
	        <?php
	        return true;
	}
	else {
		return false;
	}
}
