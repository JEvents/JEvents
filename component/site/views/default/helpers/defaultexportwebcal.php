<?php

/*
 * @JEvents Helper for Generating Exports - Webcal
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

function DefaultExportWebcal($view, $publiclink, $privatelink)
{

	//Webcal Subscribe button:
	//Replace http with webcal	
	$webcalurl_pub = str_replace(array('http:', 'https:'), array('webcal:', 'webcal:'), $publiclink);
	?>
<div class='ical_form_button export_public'>
	<a style="text-decoration:none" href="<?php echo $webcalurl_pub; ?>"
	   title="<?php echo Text::_("JEV_REP_ICAL_PUBLIC_WEBCAL") ?>">
		<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_REP_ICAL_PUBLIC_WEBCAL"), null, true); ?></span>
		<span style="display:inline-block;"><?php echo Text::_("JEV_REP_ICAL_PUBLIC_WEBCAL"); ?></span>
	</a>

</div>
<?php

	$user = Factory::getUser();
	if ($user->id != 0)
	{
		//Webcal Subscribe button:
		//Replace http with webcal	
		$webcalurl_priv = str_replace(array('http:', 'https:'), array('webcal:', 'webcal:'), $privatelink);
		?>
		<div class='ical_form_button export_private'>
			<a style="text-decoration:none" href="<?php echo $webcalurl_priv; ?>"
			   title="<?php echo Text::_("JEV_REP_ICAL_PRIVATE_WEBCAL") ?>">
				<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_REP_ICAL_PRIVATE_WEBCAL"), null, true); ?></span>
				<span style="display:inline-block;"><?php echo Text::_("JEV_REP_ICAL_PRIVATE_WEBCAL"); ?></span>
			</a>
		</div>
<?php
	}

}

/*
 .export_pub, .export_public {
    display: inline-block;
    width: 49%;
    margin: 0 0 10px 0;
    padding: 0;
    vertical-align:top;
}

.export_priv, .export_private {
    display: inline-block;
    width: 49%;
    margin: 0 0 10px 0;
    padding: 0;
    vertical-align:top;
}


 */