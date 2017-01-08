<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: overview.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

$option = JEV_COM_COMPONENT;

$jinput = JFactory::getApplication()->input;

$user = JFactory::getUser();
$db = JFactory::getDBO();
$pathIMG = JURI::root() . 'administrator/images/';
$orderdir = $jinput->getCmd("filter_order_Dir", 'asc');
$order = $jinput->getCmd("filter_order", 'tl.id');

if (isset($this->message) && $this->message != null)
{
	?>
	<div class="message"><?php echo $this->message; ?></div>
	<?php
}
$url = JRoute::_("index.php?option=" . $option);
$mainspan = 10;
 $fullspan = 12;
?>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
 <?php endif; ?>

<form action="<?php echo $url; ?>" method="post" name="adminForm"  id="adminForm">
		<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
			<div id="jevuser">

				<table cellpadding="4" cellspacing="0" border="0" >
					<tr>
						<td><?php echo JText::_('JEV_SEARCH'); ?>&nbsp;</td>
						<td>
							<input type="text" name="search" id="jevsearch" value="<?php echo $this->search; ?>" class="inputbox" onChange="document.adminForm.submit();" />
						</td>
						<td>
							<button onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
							<button onclick="document.getElementById('jevsearch').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
						</td>
					</tr>
				</table>
				<br/>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist   table table-striped">
					<thead>
						<tr>
							<th width="20">
								<?php echo JHtml::_('grid.checkall'); ?>
							</th>
							<th class="title" width="20%" align="left"  nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('NAME'), 'jname', $orderdir, $order, "user.list"); ?></th>
							<th width="20%" align="left" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('USERNAME'), 'username', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('ENABLED'), 'published', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('CREATE'), 'cancreate', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('MAX_EVENTS'), 'eventslimit', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('PUBLISH_OWN'), 'canpublishown', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('DELETE_OWN'), 'candeleteown', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('EDIT_ALL'), 'canedit', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('PUBLISH_ALL'), 'canpublishall', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('DELETE_ALL'), 'candeleteall', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('UPLOAD_IMAGES'), 'canuploadimages', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('UPLOAD_FILES'), 'canuploadmovies', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('CREATE_OWN_EXTRAS'), 'cancreateown', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('CREATE_GLOBAL_EXTRAS'), 'cancreateglobal', $orderdir, $order, "user.list"); ?></th>
							<th align="center" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('MAX_EXTRAS'), 'extraslimit', $orderdir, $order, "user.list"); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="16">
<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						$k = 0;
						$i = 0;
						foreach ($this->users as $row)
						{
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td width="20">
									<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="Joomla.isChecked(this.checked);" />
								</td>
								<td>
									<a href="#edit" onclick=" return listItemTask('cb<?php echo $i; ?>','user.edit');"><?php echo $row->jname; ?></a>
								</td>
								<td>
								<?php echo $row->username; ?>
								</td>

								<?php
								$img = $row->published ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->published >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->published ? 'user.unpublish' : 'user.publish') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = '<img src="' . JURI::root() . $img . '" width="12" height="12" border="0" alt="" />';
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->cancreate ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->cancreate >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->cancreate ? 'user.cannotcreate' : 'user.cancreate') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<td align="center"><?php echo $row->eventslimit; ?></td>

								<?php
								$img = $row->canpublishown ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->canpublishown >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->canpublishown ? 'user.cannotpublishown' : 'user.canpublishown') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->candeleteown ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->candeleteown >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->candeleteown ? 'user.cannotdeleteown' : 'user.candeleteown') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->canedit ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->canedit >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->canedit ? 'user.cannotedit' : 'user.canedit') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->canpublishall ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->canpublishall >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->canpublishall ? 'user.cannotpublishall' : 'user.canpublishall') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->candeleteall ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->candeleteall >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->candeleteall ? 'user.cannotdeleteall' : 'user.candeleteall') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->canuploadimages ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
								$href = '';
								if ($row->canuploadimages >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->canuploadimages ? 'user.cannotuploadimages' : 'user.canuploadimages') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

								<?php
								$img = $row->canuploadmovies ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

								$href = '';
								if ($row->canuploadmovies >= 0)
								{
									$href = '<a href="javascript: void(0);" ';
									$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->canuploadmovies ? 'user.cannotuploadmovies' : 'user.canuploadmovies') . '\')">';
									$href .= $img;
									$href .= '</a>';
								}
								else
								{
									$href = $img;
								}
								?>
								<td align="center"><?php echo $href; ?></td>

	<?php
	$img = $row->cancreateown ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

	$href = '';
	if ($row->cancreateown >= 0)
	{
		$href = '<a href="javascript: void(0);" ';
		$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->cancreateown ? 'user.cannotcreateown' : 'user.cancreateown') . '\')">';
		$href .= $img;
		$href .= '</a>';
	}
	else
	{
		$href = $img;
	}
	?>
								<td align="center"><?php echo $href; ?></td>

	<?php
	$img = $row->cancreateglobal ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);

	$href = '';
	if ($row->cancreateglobal >= 0)
	{
		$href = '<a href="javascript: void(0);" ';
		$href .= 'onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->cancreateglobal ? 'user.cannotcreateglobal' : 'user.cancreateglobal') . '\')">';
		$href .= $img;
		$href .= '</a>';
	}
	else
	{
		$href = $img;
	}
	?>
								<td align="center"><?php echo $href; ?></td>

								<td align="center"><?php echo $row->extraslimit; ?></td>

	<?php
	$k = 1 - $k;
	$i++;
	?>
							</tr>
<?php } ?>
					</tbody>
				</table>
<?php echo JHTML::_('form.token'); ?>
				<input type="hidden" name="hidemainmenu" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="task" value='user.overview' />
				<input type="hidden" name="filter_order" value="<?php echo JRequest::getCmd("filter_order", "tl.id"); ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo JRequest::getCmd("filter_order_Dir", "asc"); ?>" />
			</div>
		</div>
</form>

<script  type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.getElementsByName ('adminForm');
<?php
if (isset($editorFields) && is_array($editorFields))
{
	foreach ($editorFields as $editor)
	{
		// Where editor[0] = your areaname and editor[1] = the field name
		echo $wysiwygeditor->save($editor[1]);
	}
}
?>
						if (pressbutton == 'cancel') {
							submitform( pressbutton );
							return;
						} else {
							submitform( pressbutton );
						}
					}
</script>
