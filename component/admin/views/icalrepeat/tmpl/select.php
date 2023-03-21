<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: select.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

global $task;
$db   = Factory::getDbo();
$user = Factory::getUser();


$input = Factory::getApplication()->input;

$editor = $input->getString('editor');
$document       = Factory::getDocument();
$document->addStyleDeclaration("body, input, select, table {font-size:11px;}
	table.filters, table.filters tr,table.filters td {border-width:0px!important;font-size:11px;}
	table.filters {margin-bottom:10px}");
$function = $input->getCmd('function', 'jSelectEvent');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php if (!$input->getInt("nomenu")) { ?>
		<table cellpadding="4" cellspacing="0" class="filters">
			<tr>
				<td align="right"><?php echo Text::_('JEV_TARGET_MENU'); ?> </td>
				<td><?php echo $this->menulist; ?> </td>
			</tr>
		</table>
	<?php } ?>
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist   table table-striped">
		<tr>
			<th class="title" width="60%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_SUMMARY'); ?></th>
			<th width="40%" nowrap="nowrap"><?php echo Text::_('COM_JEVENTS_ICALREPEAT_REPEAT_DATE_TIME'); ?></th>
		</tr>

		<?php
		$k        = 0;
		$nullDate = $db->getNullDate();

		for ($i = 0, $n = count($this->icalrows); $i < $n; $i++)
		{
			$row    = &$this->icalrows[$i];
			$repeat = $row;
			// dummy menu item
			$link = $repeat->viewDetailLink($repeat->yup(), $repeat->mup(), $repeat->dup(), false, 1);
			?>
			<tr class="row<?php echo $k; ?>">
				<td width="30%">
					<a href="#select"
					   onclick="return window.parent.<?php echo $function; ?>('<?php echo $link; ?>','<?php echo addslashes(htmlspecialchars($repeat->title())); ?>' , (jQuery('#Itemid').length?jQuery('#Itemid').val():0) , <?php echo $repeat->ev_id(); ?>, <?php echo $repeat->rp_id(); ?>, '<?php echo $editor; ?>')"
					   title="<?php echo Text::_('JEV_SELECT_Repeat'); ?>"><?php echo $row->title(); ?></a></td>
				<td width="40%">
					<?php
					$times = '<table style="border: 1px solid #666666; width:100%;">';
					$times .= '<tr><td>Start : ' . $row->publish_up() . '</td></tr>';
					$times .= '<tr><td>End : ' . $row->publish_down() . '</td></tr>';
					$times .= "</table>";
					echo $times;
					?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		<tr>
			<th align="center" colspan="9"><?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true)); ?></th>
		</tr>
	</table>
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	<input type="hidden" name="evid" value="<?php echo $this->evid; ?>"/>
	<input type="hidden" name="function" value="<?php echo $function; ?>"/>
	<input type="hidden" name="task" value="icalrepeat.select"/>
	<input type="hidden" name="tmpl" value="component"/>
</form>



