<?php 
defined('_JEXEC') or die('Restricted access');

/**
	 * Creates mini event dialog for view detail page etc.
	 * note this must be contained in a position:relative block element in order to work
	 *
	 * @param Jevent or descendent $row
	 */
function DefaultEventManagementDialog($view,$row, $mask){

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

		if (JVersion::isCompatible("1.6.0")){
			$pathIMG = JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images';
		}
		else {
			$pathIMG = JURI::root() . 'administrator/images';
		}
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
            <div id="action_dialog"  style="position:absolute;right:0px;background-color:#dedede;border:solid 1px #000000;width:200px;padding:10px;visibility:hidden;z-index:999;">
            	<div style="width:12px!important;position:absolute;right:0px;top:0px;background-color:#ffffff;;border:solid #000000;border-width:0 0 1px 1px;text-align:center;">
            		<a href="javascript:void(0)" onclick="closedialog()" style="font-weight:bold;text-decoration:none;color:#000000;">x</a>
            	</div>
                 <?php
                 if ($publishLink!=""){
                 ?>
                 <a href="<?php echo $publishLink;?>" id="publish_reccur"  title="<?php echo $publishText;?>" style="text-decoration:none;"><img src="<?php echo $publishImg; ?>" style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo $publishText;?></a><br/>
                 <?php
                 }
                 ?>
                 <?php
                 if ($editRepeatLink!=""){
                 ?>
                 <a href="<?php echo $editRepeatLink;?>" id="edit_reccur"  title="edit event" style="text-decoration:none;"><img src="<?php echo $editRepeatImg; ?>" style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo JText::_( 'EDIT_REPEAT' );?></a><br/>
                 <?php
                 }
                 if ($editLink!=""){
                 ?>
            	<a href="<?php echo $editLink;?>" id="edit_event" title="edit event" style="text-decoration:none;"><img src="<?php echo $editImg; ?>" style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo JText::_( 'EDIT_EVENT' );?></a><br/>
            	<a href="<?php echo $editCopyLink;?>" id="edit_eventcopy" title="edit event" style="text-decoration:none;"><img src="<?php echo $editCopyImg; ?>" style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo JText::_( 'COPY_AND_EDIT_EVENT' );?></a><br/>
                 <?php
                 }
                 if ($deleteRepeatLink!=""){
                 ?>
                 <a href="<?php echo $deleteRepeatLink;?>" onclick="return confirm('<?php echo JText::_( 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECURRENCE' );?>')" id="delete_repeat"  title="delete repeat" style="text-decoration:none;"><img src="<?php echo $deleteRepeatImg; ?>"  style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo JText::_( 'DELETE_THIS_REPEAT' );?></a><br/>
                 <?php
                 }
                 if ($deleteLink!=""){
                 ?>
                 <a href="<?php echo $deleteLink;?>" onclick="return confirm('<?php echo JText::_($hasrepeat?'Are you sure you wish to delete this event and all its repeat':'Are you sure you wish to delete this event');?>')" id="delete_event"  title="delete event" style="text-decoration:none;"><img src="<?php echo $deleteImg; ?>"  style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo JText::_($hasrepeat?"DELETE_ALL_REPEATS":"DELETE_EVENT");?></a><br/>
            	<?php
                 }
                 if ($deleteFutureLink!=""){
                 ?>
                 <a href="<?php echo $deleteFutureLink;?>" onclick="return confirm('<?php echo JText::_( 'ARE_YOU_SURE_YOU_WITH_TO_DELETE_THIS_EVENT_AND_ALL_FUTURE_REPEATS' )?>')" id="delete_eventfuture"  title="delete event" style="text-decoration:none;"><img src="<?php echo $deleteFutureImg; ?>"  style="width:20px;height:20px;border:0px;margin-right:1em;vertical-align:middle;" alt="" /><?php echo JText::_( 'JEV_DELETE_FUTURE_REPEATS' );?></a><br/>
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
