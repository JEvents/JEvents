<?php

/*
 * @JEvents Helper for Generating Exports - Outlook 2003
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

function DefaultExportOutlook2003($view, $publiclink, $privatelink)
{

	$publiclink1 = "https://outlook.live.com/owa/?path=/calendar/action/compose&rru=addsubscription&name=Test&$publiclink&outlook2003=1";
	$publiclink2 = "https://outlook.office.com/owa/?path=/calendar/action/compose&rru=addsubscription&name=Test&$publiclink&outlook2003=1";;
	$privatelink1 = "https://outlook.live.com/owa/?path=/calendar/action/compose&rru=addsubscription&name=Test&$privatelink&outlook2003=1";
	$privatelink2 = "https://outlook.office.com/owa/?path=/calendar/action/compose&rru=addsubscription&name=Test&$privatelink&outlook2003=1";

	$user = Factory::getUser();
	/*
	if ($user->id != 0)
	{
		echo "<div class='ical_form_button export_public'><h3>" . Text::_('JEV_ICAL_OUTLOOK_SPECIFIC') . "</h3></div>";
	}
	else
	{
		echo "<div class='ical_form_button export_public clearleft' ><h3>" . Text::_('JEV_ICAL_OUTLOOK_SPECIFIC') . "</h3></div>";
	}
	*/
	?>
	<div class='ical_form_button export_public'>
		<a style="text-decoration:none" href="<?php echo $publiclink1; ?>"
		   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE") ?>" target="_blank">
			<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
			<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"); ?></span>
		</a>
	</div>
	<?php
	if ($user->id != 0)
	{
		?>
		<div class='ical_form_button export_private'>
			<a style="text-decoration:none" href="<?php echo $privatelink1; ?>"
			   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE") ?>" target="_blank">
				<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
				<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"); ?></span>
			</a>
		</div>
		<?php
	}
	?>
	<div class='ical_form_button export_public'>
		<a style="text-decoration:none" href="<?php echo $publiclink2; ?>"
		   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK") ?>" target="_blank">
			<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"), null, true); ?></span>
			<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"); ?></span>
		</a>
	</div>
	<?php
	if ($user->id != 0)
	{
		?>
		<div class='ical_form_button export_private'>
			<a style="text-decoration:none" href="<?php echo $privatelink2; ?>"
			   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK") ?>" target="_blank">
				<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"), null, true); ?></span>
				<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"); ?></span>
			</a>
		</div>
		<?php
	}

}