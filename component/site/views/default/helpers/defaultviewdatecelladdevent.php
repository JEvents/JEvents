<?php
defined('_JEXEC') or die('Restricted access');

function DefaultViewDatecellAddEvent($view, $year, $month, $day)
{
	if (JEVHelper::isEventCreator())
	{
		JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
		// no events on Saturday or Sunday!
		//if (date("N",mktime(0,0,0,$month,$day, $year))>5) return;
		$editLink = JRoute::_('index.php?option=' . JEV_COM_COMPONENT
						. '&task=icalevent.edit' . '&year=' . $year . '&month=' . $month . '&day=' . $day . '&Itemid=' . $view->Itemid, true);
		$transparentGif = JURI::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images/transp.gif";
		$eventlinkadd = $editLink;

		if ($view->popup)
		{
			$eventlinkadd = "javascript:jevEditPopup('".$editLink."')";
			?>
			<a href="<?php echo $eventlinkadd; ?>" title="<?php echo JText::_('JEV_ADDEVENT'); ?>" class="addjevent" id="add<?php echo $year . $month . $day; ?>"  > <!-- data-toggle="modal" data-target="#myEditModal"> //-->
				<img src="<?php echo $transparentGif; ?>" alt="<?php echo JText::_('JEV_ADDEVENT'); ?>"/>
			</a>
			<?php
		}
		else
		{
			?>
			<a href="<?php echo $eventlinkadd; ?>" title="<?php echo JText::_('JEV_ADDEVENT'); ?>" class="addjevent" id="add<?php echo $year . $month . $day; ?>">
				<img src="<?php echo $transparentGif; ?>" alt="<?php echo JText::_('JEV_ADDEVENT'); ?>"/>
			</a>
			<?php
		}

		static $modalDefined = false;
		if (!$modalDefined && $view->popup)
		{
			$modalDefined = true;
			//JevHtmlBootstrap::modal("myEditModal");
			JText::script('JEV_ADD_EVENT');
		}

	}

}
