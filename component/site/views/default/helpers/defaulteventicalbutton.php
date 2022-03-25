<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

function DefaultEventIcalButton($view, $row)
{

	JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
	JevModal::modal();
	JEVHelper::script('editpopupJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
	?>

	<a href="javascript:jevIdPopup('ical_dialogJQ<?php echo $row->rp_id(); ?>');"
	   title="<?php echo Text::_('JEV_SAVEICAL'); ?>">
		<img src="<?php echo Uri::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/jevents_event_sml.png' ?>"
		     align="middle" name="image" alt="<?php echo Text::_('JEV_SAVEICAL'); ?>"
		     class="h24px jev_ev_sml nothumb"/>
	</a>
	<?php

}
