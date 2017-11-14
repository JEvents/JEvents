<?php 
defined('_JEXEC') or die('Restricted access');

/**
	 * Creates mini event dialog for view detail page etc.
	 * note this must be contained in a position:relative block element in order to work
	 *
	 * @param Jevent or descendent $row
	 */
function DefaultEventManagementDialog($view,$row, $mask, $bootstrap = false) {

	JevHtmlBootstrap::modal("action_dialogJQ".$row->rp_id());
	$jinput = JFactory::getApplication()->input;
	$user = JFactory::getUser();

	if ($user->get("id")==0) return "";
	if( (JEVHelper::canEditEvent($row) || JEVHelper::canPublishEvent($row)|| JEVHelper::canDeleteEvent($row)) ) { 

		$popup=false;
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup",0) && JEVHelper::isEventCreator())
		{
			JevHtmlBootstrap::modal();
			JEVHelper::script('editpopupJQ.js','components/'.JEV_COM_COMPONENT.'/assets/js/');
			$popup=true;
			$popupw = $params->get("popupw",800);
			$popuph = $params->get("popuph",600);
		}
		if ($jinput->getInt("pop", 0)){
			// do not call the modal scripts if already in a popup window!
			$popup=false;
		}

		$hasrepeat = false;
		$pathIMG = JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images';
		$editImg = JHtml::image('com_jevents/icons-32/edit.png',JText::_("EDIT_EVENT"),null,true);
		$editLink = $row->editLink();
		$editLink = $popup?"javascript:jevEditPopupNoHeader('".$editLink."');":$editLink;
		$editCopyImg = JHtml::image('com_jevents/icons-32/copy.png',JText::_("COPY_AND_EDIT_EVENT"),null,true);
		$editCopyLink = $row->editCopyLink();
		$editCopyLink = $popup?"javascript:jevEditPopupNoHeader('".$editCopyLink."');":$editCopyLink;
		$deleteImg = JHtml::image('com_jevents/icons-32/discard.png',JText::_("DELETE_EVENT"),null,true);
		$deleteLink = $row->deleteLink();
		if ($row->until()!=$row->dtstart() || $row->count()>1 || $row->freq()=="IRREGULAR"){

			$hasrepeat = true;

			$editRepeatImg = JHtml::image('com_jevents/icons-32/edit.png',JText::_("EDIT_REPEAT"),null,true);
			$editRepeatLink = $row->editRepeatLink();
			$editRepeatLink = $popup?"javascript:jevEditPopupNoHeader('".$editRepeatLink."');":$editRepeatLink;
			$deleteRepeatImg = JHtml::image('com_jevents/icons-32/discard.png',JText::_("DELETE_THIS_REPEAT"),null,true);
			$deleteRepeatLink = $row->deleteRepeatLink();
			//$deleteRepeatLink = $row->deleteRepeatLink(false);
			//$deleteRepeatLink = JRoute::_($deleteRepeatLink."&rettask=month.calendar", true);
			$deleteFutureImg = JHtml::image('com_jevents/icons-32/discards.png',JText::_("JEV_DELETE_FUTURE_REPEATS"),null,true);
			$deleteFutureLink = $row->deleteFutureLink();
			$deleteImg = JHtml::image('com_jevents/icons-32/discards.png',JText::_("DELETE_ALL_REPEATS"),null,true);
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
				$publishImg =  JHtml::image('com_jevents/icons-32/cancel.png',JText::_("UNPUBLISH_EVENT"),null,true);
				$publishLink = $row->unpublishLink();
				$publishText = JText::_( 'UNPUBLISH_EVENT' );
			}
			else {
				$publishImg =  JHtml::image('com_jevents/icons-32/accept.png',JText::_("PUBLISH_EVENT"),null,true);
				$publishLink = $row->publishLink();
				$publishText = JText::_( 'PUBLISH_EVENT' );
			}
		}

		if ($publishLink.$editRepeatLink.$editLink.$deleteRepeatLink.$deleteLink.$deleteFutureLink == "") {
			return false;
		}

            ?>
		<div id="action_dialogJQ<?php echo $row->rp_id();?>" class="action_dialogJQ modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><?php echo JText::_("JEV_MANAGE_EVENT");?></h4>
					</div>
					<div class="modal-body">
						<?php
						if ($publishLink!=""){
						?>
						<a href="<?php echo $publishLink;?>" id="publish_reccur"  title="<?php echo $publishText;?>" ><?php echo $publishImg; ?><?php echo $publishText;?></a><br/>
						<?php
						}
						?>
						<?php
						if ($editRepeatLink!=""){
						?>
						<a href="<?php echo $editRepeatLink;?>" id="edit_reccur"  title="edit event" ><?php echo $editRepeatImg; ?><?php echo JText::_( 'EDIT_REPEAT' );?></a><br/>
						<?php
						}
						if ($editLink!=""){
						?>
					   <a href="<?php echo $editLink;?>"  id="edit_event" title="edit event" ><?php echo $editImg; ?><?php echo JText::_( 'EDIT_EVENT' );?></a><br/>
					   <a href="<?php echo $editCopyLink;?>" id="edit_eventcopy" title="edit event" ><?php echo $editCopyImg; ?><?php echo JText::_( 'COPY_AND_EDIT_EVENT' );?></a><br/>
						<?php
						}
						if ($deleteRepeatLink!=""){
						?>
						<a href="<?php echo $deleteRepeatLink;?>"  onclick="return confirm('<?php echo JText::_( 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECURRENCE', true );?>')" id="delete_repeat"  title="delete repeat" ><?php echo $deleteRepeatImg; ?><?php echo JText::_( 'DELETE_THIS_REPEAT' );?></a><br/>
						<?php
						}
						if ($deleteLink!=""){
						?>
						<a href="<?php echo $deleteLink;?>"  onclick="return confirm('<?php echo JText::_($hasrepeat?'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT_AND_ALL_ITS_REPEAT':'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT', true);?>')" id="delete_event"  title="delete event" ><?php echo $deleteImg; ?><?php echo JText::_($hasrepeat?"DELETE_ALL_REPEATS":"DELETE_EVENT");?></a><br/>
					   <?php
						}
						if ($deleteFutureLink!=""){
						?>
						<a href="<?php echo $deleteFutureLink;?>"  onclick="return confirm('<?php echo JText::_( 'ARE_YOU_SURE_YOU_WITH_TO_DELETE_THIS_EVENT_AND_ALL_FUTURE_REPEATS', true )?>')" id="delete_eventfuture"  title="delete event" ><?php echo $deleteFutureImg; ?><?php echo JText::_( 'JEV_DELETE_FUTURE_REPEATS' );?></a><br/>
				   <?php

						}
						?>
					</div>
					<div class="modal-footer">
						 <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_("JEV_CLOSE");?></button>
					</div>
				</div>
			</div>
		</div>

	        <?php
	        return true;
	}
	else {
		return false;
	}
}
