<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

function DefaultEventManagementButton($view, $row)
{

	JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
	JevModal::modal();
	JEVHelper::script('editpopupJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
	?>
	<a href="javascript:jevIdPopup('action_dialogJQ<?php echo $row->rp_id(); ?>');"
	   title="<?php echo Text::_('JEV_E_EDIT', true); ?>">
		<span class="icon-edit"> </span>
	</a>
	<?php
}
