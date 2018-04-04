<?php
defined('_JEXEC') or die('Restricted access');

function DefaultEventManagementButton($view, $row)
{
   	JEVHelper::script( 'view_detailJQ.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
	JevHtmlBootstrap::modal();
	JEVHelper::script('editpopupJQ.js','components/'.JEV_COM_COMPONENT.'/assets/js/');
	?>
	<a href="javascript:jevIdPopup('action_dialogJQ<?php echo $row->rp_id();?>');" title="<?php echo JText::_('JEV_E_EDIT', true); ?>">
		<?php echo JHtml::image('com_jevents/icons-32/edit.png',JText::_("JEV_E_EDIT"),null,true); ?>
	</a>
	<?php
}
