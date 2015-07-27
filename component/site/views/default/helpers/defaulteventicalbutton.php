<?php
defined('_JEXEC') or die('Restricted access');

function DefaultEventIcalButton($view, $row)
{
	JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
	?>
	<a href="#myical-modal" data-target="#ical_dialogJQ<?php echo $row->rp_id(); ?>" data-toggle="modal" title="<?php echo JText::_('JEV_SAVEICAL'); ?>">
		<img src="<?php echo JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/jevents_event_sml.png' ?>" align="middle" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL'); ?>" class="h24px jev_ev_sml nothumb"/>
	</a>
	<?php

}
