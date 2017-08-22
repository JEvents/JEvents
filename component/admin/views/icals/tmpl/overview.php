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
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$db = JFactory::getDBO();
$user = JFactory::getUser();

// get configuration object
$cfg = JEVConfig::getInstance();

$pathIMG = JURI::root() . 'administrator/images/';
$pathJeventsIMG = JURI::root() . "administrator/components/" . JEV_COM_COMPONENT . "/assets/images/";

global $task;
JHTML::_('behavior.tooltip');

// get configuration object
$cfg = JEVConfig::getInstance();
$mainspan = 10;
 $fullspan = 12;
?>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
 <?php endif; ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<table cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="100%">
						&nbsp;
					</td>
					<td align="right"><?php echo $this->clist; ?> </td>
					<td><?php echo JText::_('JEV_SEARCH'); ?>&nbsp;</td>
					<td>
						<input type="text" name="search" value="<?php echo $this->search; ?>" class="inputbox" onChange="document.adminForm.submit();" />
					</td>
				</tr>
			</table>

			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist  table table-striped">
				<tr>
					<th width="20" nowrap="nowrap">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th class="title" width="30%" nowrap="nowrap"><?php echo JText::_('JEV_ICAL_SUMMARY'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_ICAL_TYPE'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_CATEGORY_NAME'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_ADMIN_REFRESH'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_PUBLISHED'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_EVENT_ANONREFRESH'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_EVENT_ISDEFAULT'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_ACCESS'); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_ICAL_ID'); ?></th>
				</tr>

				<?php
				$k = 0;
				$nullDate = $db->getNullDate();

				for ($i = 0, $n = count($this->rows); $i < $n; $i++)
				{
					$row = &$this->rows[$i];
					?>
					<tr class="row<?php echo $k; ?>">
						<td width="20">
							<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->ics_id; ?>" onclick="Joomla.isChecked(this.checked);" />
						</td>
						<td>
							<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','icals.edit')" title="<?php echo JText::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->label; ?></a>
						</td>
						<td align="center">
							<?php
							$types = array("Remote", "Uploaded File", "Native");
							$typeTranslation = 'COM_JEVENTS_MANAGE_CALENDARS_OVERVIEW_' . str_replace(' ','_',strtoupper($types[$row->icaltype]));
							echo JText::_($typeTranslation);
							?>
						</td>
						<td align="center"><?php echo $row->category; ?></td>
						<td align="center">
							<?php
							// only offer reload for URL based ICS
							if ($row->srcURL != "")
							{
								?>
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','icals.reload')">
									<img src="<?php echo $pathJeventsIMG . "reload.png"; ?>" border="0" alt="reload" />
								</a>
		<?php
	}
	?>

						</td>
						<td align="center">
								<?php
								$img = $row->state ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
								?>
							<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->state ? 'icals.unpublish' : 'icals.publish'; ?>')">
							<?php echo $img; ?>
							</a>
						</td>
						<td align="center">
							<?php
							if ($row->icaltype == 0)
							{
								$img = $row->autorefresh ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
								?>
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->autorefresh ? 'icals.noautorefresh' : 'icals.autorefresh'; ?>')">
								<?php echo $img; ?>
								</a>
								<?php
								if ($row->autorefresh)
								{
									?>
									<br/><a href="<?php echo JURI::root() . "index.php?option=" . JEV_COM_COMPONENT . "&icsid=" . $row->ics_id . "&task=icals.reload"; ?>" title="<?php echo JText::_("JEV_AUTOREFRESH_LINK") ?>"><?php echo JText::_("JEV_AUTOREFRESH_LINK") ?></a>
									<?php
								}
							}
							else
							{
								echo " - ";
							}
							?>
						</td>
						<td align="center">
							<?php
							if ($row->icaltype == 2)
							{
								$img = $row->isdefault ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
								?>
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->isdefault ? 'icals.notdefault' : 'icals.isdefault'; ?>')">
		<?php echo $img; ?>
								</a>
	<?php
	}
	else
	{
		echo " - ";
	}
	?>
						</td>
						<td align="center"><?php echo $row->_groupname; ?></td>
						<td align="center"><?php echo $row->ics_id; ?></td>
					</tr>
	<?php
	$k = 1 - $k;
}
?>
				<tr>
					<th align="center" colspan="10"><?php echo $this->pageNav->getListFooter(); ?></th>
				</tr>
			</table>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="icals.list" />
			<input type="hidden" name="boxchecked" value="0" />
		</div>
</form>

