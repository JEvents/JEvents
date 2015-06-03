<?php
defined('_JEXEC') or die('Restricted access');

function DefaultEventManagementButton($view, $row)
{
   	JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
	?>
	<a href="#my-modal" data-toggle="modal"  data-target="#action_dialogJQ<?php echo $row->rp_id();?>"  title="<?php echo JText::_('JEV_E_EDIT', true); ?>">
		<?php echo JEVHelper::imagesite('edit.png', JText::_('JEV_E_EDIT')); ?>
	</a>
	<?php
}
