<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

function DefaultViewDatecellAddEvent($view, $year, $month, $day)
{

	if (JEVHelper::isEventCreator())
	{
		JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
		// no events on Saturday or Sunday!
		//if (date("N",mktime(0,0,0,$month,$day, $year))>5) return;
		$editLink       = Route::_('index.php?option=' . JEV_COM_COMPONENT
			. '&task=icalevent.edit' . '&year=' . $year . '&month=' . $month . '&day=' . $day . '&Itemid=' . $view->Itemid, true);
		$transparentGif = Uri::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images/transp.gif";
		$eventlinkadd   = $editLink;

		if ($view->popup)
		{
			JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
			JevModal::framework();

			$eventlinkadd = "javascript:jevModalNoHeader('myEditModal','" . $editLink . "');";
			?>
			<a href="<?php echo $eventlinkadd; ?>" title="<?php echo Text::_('JEV_ADDEVENT'); ?>" class="addjevent"
			   id="add<?php echo $year . $month . $day; ?>"> <!-- data-toggle="modal" data-target="#myEditModal"> //-->
				<img src="<?php echo $transparentGif; ?>" alt="<?php echo Text::_('JEV_ADDEVENT'); ?>"/>
			</a>
			<?php
		}
		else
		{
			?>
			<a href="<?php echo $eventlinkadd; ?>" title="<?php echo Text::_('JEV_ADDEVENT'); ?>" class="addjevent"
			   id="add<?php echo $year . $month . $day; ?>">
				<img src="<?php echo $transparentGif; ?>" alt="<?php echo Text::_('JEV_ADDEVENT'); ?>"/>
			</a>
			<?php
		}

		static $modalDefined = false;
		if (!$modalDefined && $view->popup)
		{
			$modalDefined = true;
			Text::script('JEV_ADD_EVENT');
		}

	}

}
