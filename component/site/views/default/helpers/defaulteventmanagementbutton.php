<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

function DefaultEventManagementButton($view, $row)
{

	JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
	JevHtmlBootstrap::modal();
	JEVHelper::script('editpopupJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
	?>
	<a href="javascript:jevIdPopup('action_dialogJQ<?php echo $row->rp_id(); ?>');"
	   title="<?php echo Text::_('JEV_E_EDIT', true); ?>">
		<?php echo HTMLHelper::image('com_jevents/icons-32/edit.png', Text::_("JEV_E_EDIT"), null, true); ?>
	</a>
	<?php
}
