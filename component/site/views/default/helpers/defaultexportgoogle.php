<?php
/* 
 *@JEvents Helper for Generating Exports
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

function DefaultExportGoogle($view, $publiclink, $privatelink)
{
	?>
	<div class='ical_form_button export_public'>
		<a href='http://www.google.com/calendar/render?cid=<?php echo  urlencode(str_replace(array(' http://','https://'), array('webcal://', 'webcal://'), $publiclink));?>' target='_blank'>
			<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/gcal32.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
			<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
		</a>
	</div>
		<?php
		$user = Factory::getUser();
		if ($user->id != 0)
		{
			?>
			<div class='ical_form_button export_private'>
				<a href='http://www.google.com/calendar/render?cid=<?php echo  urlencode(str_replace(array(' http://','https://'), array('webcal://', 'webcal://'), $privatelink));?>' target='_blank'>
					<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/gcal32.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
					<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
				</a>
			</div>
			<?php
		}
}