<?php

/*
 * @JEvents Helper for Generating Exports - Ical Download
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

function DefaultExportIcalDownload($view, $publiclink, $privatelink)
{
?>
	<div class='ical_form_button export_public'>
		<a style="text-decoration:none" href="<?php echo $publiclink; ?>"
		   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
			<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
			<span style="display:inline-block;"><?php echo Text::_("JEV_SAVEICAL"); ?></span>
		</a>
	</div>
<?php
	$user = Factory::getUser();
	if ($user->id != 0)
	{
		?>
		<div class='ical_form_button export_private'>
			<a style="text-decoration:none" href="<?php echo $privatelink; ?>"
			   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
				<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
				<span style="display:inline-block;"><?php echo Text::_("JEV_SAVEICAL"); ?></span>
			</a>
		</div>
<?php
	}
}