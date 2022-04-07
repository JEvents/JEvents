<?php
defined('_JEXEC') or die('Restricted access');


use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Creates mini event dialog for view detail page etc.
 * note this must be contained in a position:relative block element in order to work
 *
 * @param Jevent or descendent $row
 */
function DefaultEventManagementDialog($view, $row, $mask, $bootstrap = false)
{

    $actionId   = "action_dialogJQ" . $row->rp_id();

	$input      = Factory::getApplication()->input;
	$user       = Factory::getUser();

	if ($user->get("id") == 0) return "";
	if ((JEVHelper::canEditEvent($row) || JEVHelper::canPublishEvent($row) || JEVHelper::canDeleteEvent($row)))
	{

		$popup  = false;
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
		{

			JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
			JevModal::framework();

			$popup  = true;
			$popupw = $params->get("popupw", 800);
			$popuph = $params->get("popuph", 600);
		}
		if ($input->getInt("pop", 0))
		{
			// do not call the modal scripts if already in a popup window!
			$popup = false;
		}

		$hasrepeat    = false;
		$editImg      = HTMLHelper::image('com_jevents/icons-32/edit.png', Text::_("EDIT_EVENT"), null, true);
		$editLink     = $row->editLink();
		//$editLink     = $popup ? "javascript:jevEditPopupNoHeader('" . $editLink . "');" : $editLink;
		$editLink     = $popup ? "javascript:jevModalNoHeader('myEditModal','" . $editLink . "');closeJevModalBySelector('#" . $actionId . "');" : $editLink;
		$editCopyImg  = HTMLHelper::image('com_jevents/icons-32/copy.png', Text::_("COPY_AND_EDIT_EVENT"), null, true);
		$editCopyLink = $row->editCopyLink();
		//$editCopyLink = $popup ? "javascript:jevEditPopupNoHeader('" . $editCopyLink . "');" : $editCopyLink;
		$editCopyLink = $popup ? "javascript:jevModalNoHeader('myEditModal','" . $editCopyLink . "');closeJevModalBySelector('#" . $actionId . "');" : $editCopyLink;
		$deleteImg    = HTMLHelper::image('com_jevents/icons-32/discard.png', Text::_("DELETE_EVENT"), null, true);
		$deleteLink   = $row->deleteLink();

        if (($row->until() != $row->dtstart() || $row->count() > 1 || $row->freq() == "IRREGULAR") && ($row->freq() !== 'none'))
		{

			$hasrepeat = true;

			$editRepeatImg    = HTMLHelper::image('com_jevents/icons-32/edit.png', Text::_("EDIT_REPEAT"), null, true);
			$editRepeatLink   = $row->editRepeatLink();
			//$editRepeatLink   = $popup ? "javascript:jevEditPopupNoHeader('" . $editRepeatLink . "');" : $editRepeatLink;
			$editRepeatLink   = $popup ? "javascript:jevModalNoHeader('myEditModal','" . $editRepeatLink . "');closeJevModalBySelector('#" . $actionId . "');" : $editRepeatLink;
			$deleteRepeatImg  = HTMLHelper::image('com_jevents/icons-32/discard.png', Text::_("DELETE_THIS_REPEAT"), null, true);
			$deleteRepeatLink = $row->deleteRepeatLink();
			//$deleteRepeatLink = $row->deleteRepeatLink(false);
			//$deleteRepeatLink = Route::_($deleteRepeatLink."&rettask=month.calendar", true);
			$deleteFutureImg  = HTMLHelper::image('com_jevents/icons-32/discards.png', Text::_("JEV_DELETE_FUTURE_REPEATS"), null, true);
			$deleteFutureLink = $row->deleteFutureLink();
			$deleteImg        = HTMLHelper::image('com_jevents/icons-32/discards.png', Text::_("DELETE_ALL_REPEATS"), null, true);
		}
		else
		{
			$editRepeatLink   = "";
			$deleteRepeatLink = "";
			$deleteFutureLink = "";
		}

		if (!JEVHelper::canEditEvent($row))
		{
			$editLink       = "";
			$editRepeatLink = "";
			$editCopyLink   = "";
		}

		if (!JEVHelper::canDeleteEvent($row))
		{
			$deleteLink       = "";
			$deleteRepeatLink = "";
			$deleteFutureLink = "";
		}

		$publishLink = "";
		if (JEVHelper::canPublishEvent($row))
		{
			if ($row->published() > 0)
			{
				$publishImg  = HTMLHelper::image('com_jevents/icons-32/cancel.png', Text::_("UNPUBLISH_EVENT"), null, true);
				$publishLink = $row->unpublishLink();
				$publishText = Text::_('UNPUBLISH_EVENT');
			}
			else
			{
				$publishImg  = HTMLHelper::image('com_jevents/icons-32/accept.png', Text::_("PUBLISH_EVENT"), null, true);
				$publishLink = $row->publishLink();
				$publishText = Text::_('PUBLISH_EVENT');
			}
		}

		if ($publishLink . $editRepeatLink . $editLink . $deleteRepeatLink . $deleteLink . $deleteFutureLink == "")
		{
			return false;
		}

		$jevparams = ComponentHelper::getParams('com_jevents');
		if (strpos($jevparams->get('framework', 'bootstrap'), 'uikit') === 0)
		{
			?>
			<div id="action_dialogJQ<?php echo $row->rp_id(); ?>" class="action_dialogUI" tabindex="-1" data-uk-modal
			     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="uk-modal-dialog">
					<button type="button" class="uk-modal-close-default"      data-uk-close ></button>
					<div class="uk-modal-header">
						<h4 class="uk-modal-title" id="myModalLabel"><?php echo Text::_("JEV_MANAGE_EVENT"); ?></h4>
					</div>
					<div class="uk-modal-body">
						<?php
						if ($publishLink != "")
						{
							?>
							<a href="<?php echo $publishLink; ?>" id="publish_reccur" class="uk-display-block uk-margin-small"
							   title="<?php echo $publishText; ?>"><?php echo $publishImg; ?><?php echo $publishText; ?></a>

							<?php
						}
						?>
						<?php
						if ($editRepeatLink != "")
						{
							?>
							<a href="<?php echo $editRepeatLink; ?>" id="edit_reccur"  class="uk-display-block uk-margin-small"
							   title="edit event"><?php echo $editRepeatImg; ?><?php echo Text::_('EDIT_REPEAT'); ?></a>
							<?php
						}
						if ($editLink != "")
						{
							?>
							<a href="<?php echo $editLink; ?>" id="edit_event"  class="uk-display-block uk-margin-small"
							   title="edit event"><?php echo $editImg; ?><?php echo Text::_('EDIT_EVENT'); ?></a>
							<a href="<?php echo $editCopyLink; ?>" id="edit_eventcopy" class="uk-display-block uk-margin-small"
							   title="edit event"><?php echo $editCopyImg; ?><?php echo Text::_('COPY_AND_EDIT_EVENT'); ?></a>

							<?php
						}
						if ($deleteRepeatLink != "")
						{
							?>
							<a href="<?php echo $deleteRepeatLink; ?>" class="uk-display-block uk-margin-small"
							   onclick="return confirm('<?php echo Text::_('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECURRENCE', true); ?>')"
							   id="delete_repeat"
							   title="delete repeat"><?php echo $deleteRepeatImg; ?><?php echo Text::_('DELETE_THIS_REPEAT'); ?></a>

							<?php
						}
						if ($deleteLink != "")
						{
							?>
							<a href="<?php echo $deleteLink; ?>" class="uk-display-block uk-margin-small"
							   onclick="return confirm('<?php echo Text::_($hasrepeat ? 'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT_AND_ALL_ITS_REPEAT' : 'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT', true); ?>')"
							   id="delete_event"
							   title="delete event"><?php echo $deleteImg; ?><?php echo Text::_($hasrepeat ? "DELETE_ALL_REPEATS" : "DELETE_EVENT"); ?></a>

							<?php
						}
						if ($deleteFutureLink != "")
						{
							?>
							<a href="<?php echo $deleteFutureLink; ?>" class="uk-display-block uk-margin-small"
							   onclick="return confirm('<?php echo Text::_('ARE_YOU_SURE_YOU_WITH_TO_DELETE_THIS_EVENT_AND_ALL_FUTURE_REPEATS', true) ?>')"
							   id="delete_eventfuture"
							   title="delete event"><?php echo $deleteFutureImg; ?><?php echo Text::_('JEV_DELETE_FUTURE_REPEATS'); ?></a>

							<?php

						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		else
		{
		?>
		<div id="action_dialogJQ<?php echo $row->rp_id(); ?>" class="action_dialogJQ modal hide fade" tabindex="-1"
		     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<?php
							$jversion = new Joomla\CMS\Version;
							if ($jversion->isCompatible('4.0'))
							{
						?>
							<h4 class="modal-title" id="myModalLabel"><?php echo Text::_("JEV_MANAGE_EVENT"); ?></h4>
							<button type="button" class="close btn-close uk-modal-close-default" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true"></button>
								<?php
							}
							else {
								?>
						<button type="button" class="close uk-modal-close-default" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><?php echo Text::_("JEV_MANAGE_EVENT"); ?></h4>
								<?php
							}
							?>
					</div>
					<div class="modal-body">
						<?php
						if ($publishLink != "")
						{
							?>
							<a href="<?php echo $publishLink; ?>" id="publish_reccur"
							   title="<?php echo $publishText; ?>"><?php echo $publishImg; ?><?php echo $publishText; ?></a>
							<br/>
							<?php
						}
						?>
						<?php
						if ($editRepeatLink != "")
						{
							?>
							<a href="<?php echo $editRepeatLink; ?>" id="edit_reccur"
							   title="edit event"><?php echo $editRepeatImg; ?><?php echo Text::_('EDIT_REPEAT'); ?></a>
							<br/>
							<?php
						}
						if ($editLink != "")
						{
							?>
							<a href="<?php echo $editLink; ?>" id="edit_event"
							   title="edit event"><?php echo $editImg; ?><?php echo Text::_('EDIT_EVENT'); ?></a><br/>
							<a href="<?php echo $editCopyLink; ?>" id="edit_eventcopy"
							   title="edit event"><?php echo $editCopyImg; ?><?php echo Text::_('COPY_AND_EDIT_EVENT'); ?></a>
							<br/>
							<?php
						}
						if ($deleteRepeatLink != "")
						{
							?>
							<a href="<?php echo $deleteRepeatLink; ?>"
							   onclick="return confirm('<?php echo Text::_('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECURRENCE', true); ?>')"
							   id="delete_repeat"
							   title="delete repeat"><?php echo $deleteRepeatImg; ?><?php echo Text::_('DELETE_THIS_REPEAT'); ?></a>
							<br/>
							<?php
						}
						if ($deleteLink != "")
						{
							?>
							<a href="<?php echo $deleteLink; ?>"
							   onclick="return confirm('<?php echo Text::_($hasrepeat ? 'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT_AND_ALL_ITS_REPEAT' : 'ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT', true); ?>')"
							   id="delete_event"
							   title="delete event"><?php echo $deleteImg; ?><?php echo Text::_($hasrepeat ? "DELETE_ALL_REPEATS" : "DELETE_EVENT"); ?></a>
							<br/>
							<?php
						}
						if ($deleteFutureLink != "")
						{
							?>
							<a href="<?php echo $deleteFutureLink; ?>"
							   onclick="return confirm('<?php echo Text::_('ARE_YOU_SURE_YOU_WITH_TO_DELETE_THIS_EVENT_AND_ALL_FUTURE_REPEATS', true) ?>')"
							   id="delete_eventfuture"
							   title="delete event"><?php echo $deleteFutureImg; ?><?php echo Text::_('JEV_DELETE_FUTURE_REPEATS'); ?></a>
							<br/>
							<?php

						}
						?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default"
						        data-dismiss="modal" data-bs-dismiss="modal" ><?php echo Text::_("JEV_CLOSE"); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		return true;
	}
	else
	{
		return false;
	}
}
