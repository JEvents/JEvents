<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: select.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

global $task;
$db = JFactory::getDBO();
$user = JFactory::getUser();
JHTML::_('behavior.tooltip');

$jinput = JFactory::getApplication()->input;

$pathIMG = JURI::Root() . 'administrator/images/';
$pathJeventsIMG = JURI::Root() . "administrator/components/" . JEV_COM_COMPONENT . "/images/";
$document = JFactory::getDocument();
$document->addStyleDeclaration("body, input, select, table {font-size:11px;}
	table.filters, table.filters tr,table.filters td {border-width:0px!important;font-size:11px;}
	table.filters {margin-bottom:10px}");
$function	= $jinput->getCmd('function', 'jSelectEvent');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php if (!$jinput->getInt("nomenu")) {?>
	<table cellpadding="4" cellspacing="0"  class="filters">
		<tr>
			<td align="right"><?php echo JText::_('JEV_TARGET_MENU'); ?> </td>
			<td ><?php echo $this->menulist; ?> </td>
		</tr>
	</table>
	<?php } ?>
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist   table table-striped">
		<tr>
			<th class="title" width="60%" nowrap="nowrap"><?php echo JText::_('JEV_ICAL_SUMMARY'); ?></th>
			<th width="40%" nowrap="nowrap"><?php echo "Repeat Date/Time"; ?></th>
		</tr>

		<?php
		$k = 0;
		$nullDate = $db->getNullDate();

		for ($i = 0, $n = count($this->icalrows); $i < $n; $i++)
		{
			$row = &$this->icalrows[$i];
			$repeat = $row;
			// dummy menu item 			
			$link = $repeat->viewDetailLink($repeat->yup(),$repeat->mup(),$repeat->dup(),false, 1);
			?>
			<tr class="row<?php echo $k; ?>">
				<td width="30%">
					<a href="#select" onclick="return window.parent.<?php echo $function;?>('<?php echo $link;?>','<?php echo addslashes(htmlspecialchars($repeat->title()));?>' , (jQuery('#Itemid').length?jQuery('#Itemid').val():0) , <?php echo $repeat->ev_id();?>, <?php echo $repeat->rp_id();?>)" title="<?php echo JText::_('JEV_SELECT_Repeat'); ?>"><?php echo $row->title(); ?></a>				</td>
				<td width="40%">
					<?php
					$times = '<table style="border: 1px solid #666666; width:100%;">';
					$times .= '<tr><td>Start : ' . $row->publish_up() . '</td></tr>';
					$times .= '<tr><td>End : ' . $row->publish_down() . '</td></tr>';
					$times .="</table>";
					echo $times;
					?>
				</td>
			</tr>
	<?php
	$k = 1 - $k;
}
?>
		<tr>
			<th align="center" colspan="9"><?php echo $this->pageNav->getListFooter(); ?></th>
		</tr>
    </table>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
    <input type="hidden" name="evid" value="<?php echo $this->evid; ?>" />
    <input type="hidden" name="function" value="<?php echo $function; ?>" />
    <input type="hidden" name="task" value="icalrepeat.select" />
    <input type="hidden" name="tmpl" value="component" />
</form>



