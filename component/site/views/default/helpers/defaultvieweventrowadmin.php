<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

function DefaultViewEventRowAdmin($view, $row, $manage = false)
{

	$input     = Factory::getApplication()->input;
	$pub_filter = $input->get('published_fv', 0);
	$popup      = false;
	$params     = ComponentHelper::getParams(JEV_COM_COMPONENT);
	if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
	{
		JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
		JevModal::framework();
		$popup  = true;
	}

	$editLink = $row->editLink(true);
	$editLink = $popup ? "javascript:jevModalNoHeader('myEditModal','" . $editLink . "');" : $editLink;

	$modifylink = '';
	if (!$manage && JEVHelper::canEditEvent($row))
	{
		//$modifylink = '<a href="' . $row->editlink(true) . '" title="' . Text::_('JEV_MODIFY') . '"><b>' . Text::_('JEV_MODIFY') . "</b></a>\n";
		$modifylink = '<a href="' . $editLink . '" title="' . Text::_('JEV_MODIFY') . '"><b>' . Text::_('JEV_MODIFY') . "</b></a>\n";
	}

	$deletelink = "";
	if (!$manage && JEVHelper::canDeleteEvent($row))
	{
        $deleteMsg  = 'onclick="return confirm(\'' . Text::_('ARE_YOU_SURE_YOU_WISH_TO_DELETE_THIS_EVENT', true) . '\')"';
		$deletelink = '<a href="' . $row->deletelink(false) . "&rettask=admin.listevents" . '" title="' . Text::_('JEV_DELETE') . '" ' . $deleteMsg . '><b>' . Text::_('JEV_DELETE') . "</b></a>\n";
	}

	if (!$manage && JEVHelper::canPublishEvent($row))
	{
		if ($row->published())
		{
			$publishlink = '<a href="' . $row->unpublishlink(false) . "&rettask=admin.listevents&published_fv=" . $pub_filter . '" title="' . Text::_('UNPUBLISH') . '"><b>' . Text::_('UNPUBLISH') . "</b></a>\n";
		}
		else
		{
			$publishlink = '<a href="' . $row->publishlink(false) . "&rettask=admin.listevents&published_fv=" . $pub_filter . '" title="' . Text::_('PUBLISH') . '"><b>' . Text::_('PUBLISH') . "</b></a>\n";
		}
	}
	else
	{
		$publishlink = "";
	}

	$eventlink = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false);
	$eventlink = Route::_($eventlink . $view->datamodel->getCatidsOutLink());
	$border    = "border-color:" . $row->bgcolor() . ";";
	?>

	<li class="ev_td_li" style="<?php echo $border; ?>">
		<a class="<?php echo $row->state() ? 'ev_link_row' : 'ev_link_unpublished'; ?>" href="<?php echo $eventlink; ?>"
		   title="<?php echo JEventsHTML::special($row->title()) . ($row->state() ? '' : Text::_('JEV_UNPUBLISHED')); ?>"><?php echo $row->title() . ($row->state() ? '' : Text::_('JEV_UNPUBLISHED')); ?></a>
		&nbsp;<?php echo Text::_('JEV_BY'); ?>
		&nbsp;<i><?php echo $row->contactlink('', true); ?></i>
		&nbsp;&nbsp;<?php echo $deletelink; ?>
		&nbsp;&nbsp;<?php echo $modifylink; ?>
		&nbsp;&nbsp;<?php echo $publishlink; ?>
	</li>
	<?php

}
