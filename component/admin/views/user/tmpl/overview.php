<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: overview.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

$option = JEV_COM_COMPONENT;

$app      = Factory::getApplication();
$input   = $app->input;
$user     = Factory::getUser();
$db       = Factory::getDbo();
$orderdir = $input->getCmd("filter_order_Dir", 'asc');
$order    = $input->getCmd("filter_order", 'tl.id');

if (isset($this->message) && $this->message != null)
{
	?>
	<div class="message"><?php echo $this->message; ?></div>
	<?php
}
$url      = Route::_("index.php?option=" . $option);
$mainspan = 10;
$fullspan = 12;
?>
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
<?php endif; ?>

<form action="<?php echo $url; ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<div id="jevuser">

			<table cellpadding="4" cellspacing="0" border="0">
				<tr>
					<td><?php echo Text::_('JEV_SEARCH'); ?>&nbsp;</td>
					<td>
						<input type="text" name="search" id="jevsearch" value="<?php echo htmlspecialchars($this->search); ?>"
						       class="inputbox" onChange="document.adminForm.submit();"/>
					</td>
					<td>
						<button onclick="this.form.submit();"><?php echo Text::_('GO'); ?></button>
						<button onclick="document.getElementById('jevsearch').value='';this.form.submit();"><?php echo Text::_('RESET'); ?></button>
					</td>
				</tr>
			</table>
			<br/>
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist   table table-striped">
				<thead>
				<tr>
					<th width="20">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="title" width="20%" align="left"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('NAME'), 'jname', $orderdir, $order, "user.list"); ?></th>
					<th width="20%" align="left"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('USERNAME'), 'username', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('ENABLED'), 'published', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('CREATE'), 'cancreate', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('MAX_EVENTS'), 'eventslimit', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('PUBLISH_OWN'), 'canpublishown', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('DELETE_OWN'), 'candeleteown', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('EDIT_ALL'), 'canedit', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('PUBLISH_ALL'), 'canpublishall', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('DELETE_ALL'), 'candeleteall', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('UPLOAD_IMAGES'), 'canuploadimages', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('UPLOAD_FILES'), 'canuploadmovies', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('CREATE_OWN_EXTRAS'), 'cancreateown', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('CREATE_GLOBAL_EXTRAS'), 'cancreateglobal', $orderdir, $order, "user.list"); ?></th>
					<th align="center"
					    nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort', Text::_('MAX_EXTRAS'), 'extraslimit', $orderdir, $order, "user.list"); ?></th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="16">
						<?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true)); ?>
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
							<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->id; ?>"
							       onclick="Joomla.isChecked(this.checked);"/>
						</td>
						<td>
							<a href="#edit"
							   onclick=" return Joomla.listItemTask('cb<?php echo $i; ?>','user.edit');"><?php echo $row->jname; ?></a>
						</td>
						<td>
							<?php echo $row->username; ?>
						</td>

						<?php
						$img = $row->published ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->published >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->published ? 'user.unpublish' : 'user.publish') . '\')">';
							$href .= $img;
							$href .= '</a>';
						}
						else
						{
							$href = '<img src="' . Uri::root() . $img . '" width="12" height="12" border="0" alt="" />';
						}
						?>
						<td align="center"><?php echo $href; ?></td>

						<?php
						$img = $row->cancreate ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->cancreate >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->cancreate ? 'user.cannotcreate' : 'user.cancreate') . '\')">';
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
						$img = $row->canpublishown ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->canpublishown >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->canpublishown ? 'user.cannotpublishown' : 'user.canpublishown') . '\')">';
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
						$img = $row->candeleteown ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->candeleteown >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->candeleteown ? 'user.cannotdeleteown' : 'user.candeleteown') . '\')">';
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
						$img = $row->canedit ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->canedit >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->canedit ? 'user.cannotedit' : 'user.canedit') . '\')">';
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
						$img = $row->canpublishall ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->canpublishall >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->canpublishall ? 'user.cannotpublishall' : 'user.canpublishall') . '\')">';
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
						$img = $row->candeleteall ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->candeleteall >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->candeleteall ? 'user.cannotdeleteall' : 'user.candeleteall') . '\')">';
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
						$img  = $row->canuploadimages ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";
						$href = '';
						if ($row->canuploadimages >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->canuploadimages ? 'user.cannotuploadimages' : 'user.canuploadimages') . '\')">';
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
						$img = $row->canuploadmovies ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->canuploadmovies >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->canuploadmovies ? 'user.cannotuploadmovies' : 'user.canuploadmovies') . '\')">';
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
						$img = $row->cancreateown ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->cancreateown >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->cancreateown ? 'user.cannotcreateown' : 'user.cancreateown') . '\')">';
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
						$img = $row->cancreateglobal ? "<i class='gsl-text-success' gsl-icon='icon:check'></i>" : "<i class='gsl-text-danger' gsl-icon='icon:close'></i>";

						$href = '';
						if ($row->cancreateglobal >= 0)
						{
							$href = '<a href="javascript: void(0);" ';
							$href .= 'onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . ($row->cancreateglobal ? 'user.cannotcreateglobal' : 'user.cancreateglobal') . '\')">';
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
			<?php echo HTMLHelper::_('form.token'); ?>
			<input type="hidden" name="hidemainmenu" value=""/>
			<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
			<input type="hidden" name="task" value='user.overview'/>
			<input type="hidden" name="filter_order" value="<?php echo $input->getCmd("filter_order", "tl.id"); ?>"/>
			<input type="hidden" name="filter_order_Dir"
			       value="<?php echo $input->getCmd("filter_order_Dir", "asc"); ?>"/>
		</div>
	</div>
</form>

<script type="text/javascript">
    function submitbutton(pressbutton) {
        var form = document.getElementsByName('adminForm');
		<?php
		if (isset($editorFields) && is_array($editorFields))
		{
			foreach ($editorFields as $editor)
			{
				// Where editor[0] = your areaname and editor[1] = the field name
				if (version_compare(JVERSION, '4.0', 'lt'))
				{
					echo $wysiwygeditor->save($editor[1]);
				}
			}
		}
		?>
        if (pressbutton == 'cancel') {
            Joomla.submitform(pressbutton);

        } else {
            Joomla.submitform(pressbutton);
        }
    }
</script>
