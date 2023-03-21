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
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;



$db     = Factory::getDbo();
$user   = Factory::getUser();
$app    = Factory::getApplication();
$input = $app->input;
// get configuration object
$cfg                 = JEVConfig::getInstance();
$this->_largeDataSet = $cfg->get('largeDataSet', 0);
$orderdir            = $input->getCmd("filter_order_Dir", 'asc');
$order               = $input->getCmd("filter_order", 'start');
$editor              = $input->getString('editor');

$document            = Factory::getDocument();
$document->addStyleDeclaration("body, input, select, table {font-size:11px;}
	table.filters, table.filters tr,table.filters td {border-width:0px!important;font-size:11px;}
	table.filters {margin-bottom:10px}");
$function = $input->getCmd('function', 'jSelectEvent');
?>
<form action="<?php echo Route::_('index.php?option=com_jevents&task=icalevent.select&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1'); ?>"
      method="post" name="adminForm" id="adminForm">
	<table cellpadding="4" cellspacing="0" class="filters">
		<tr>
			<?php if (!$this->_largeDataSet)
			{
				?>
				<td align="right" width="100%"><?php echo Text::_('JEV_HIDE_OLD_EVENTS'); ?> </td>
				<td align="right"><?php echo $this->plist; ?></td>
			<?php } ?>
			<td align="right"><?php echo $this->clist; ?> </td>
			<td align="right"><?php echo $this->icsList; ?> </td>
			<td align="right"><?php echo $this->userlist; ?> </td>
			<td><?php echo Text::_('JEV_SEARCH'); ?>&nbsp;</td>
			<td>
				<input type="text" name="search" value="<?php echo htmlspecialchars($this->search); ?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		<?php if (!$input->getInt("nomenu", null))
		{ ?>
			<tr>
				<td colspan="2" align="right"><?php echo Text::_('JEV_TARGET_MENU'); ?> </td>
				<td colspan="3"><?php echo $this->menulist; ?> </td>
			</tr>
		<?php } ?>
	</table>

	<table class="adminlist   table table-striped jevbootstrap">
		<thead>
		<tr>
			<th class="title" width="40%" nowrap="nowrap">
				<?php echo HTMLHelper::_('grid.sort', 'JEV_ICAL_SUMMARY', 'title', $orderdir, $order, "icalevent.list"); ?>
			<th width="10%" nowrap="nowrap"><?php echo Text::_('REPEATS'); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_EVENT_CREATOR'); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_PUBLISHED'); ?></th>
			<th width="20%" nowrap="nowrap">
				<?php echo HTMLHelper::_('grid.sort', 'JEV_TIME_SHEET', 'starttime', $orderdir, $order, "icalevent.list"); ?>
			</th>
			<th width="20%" nowrap="nowrap">
				<?php echo HTMLHelper::_('grid.sort', 'JEV_FIELD_CREATIONDATE', 'created', $orderdir, $order, "icalevent.list"); ?>
			</th>
			<th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_ACCESS'); ?></th>
		</tr>
		</thead>

		<tbody>
		<?php
		$k        = 0;
		$nullDate = $db->getNullDate();

		for ($i = 0, $n = count($this->rows); $i < $n; $i++)
		{
			$row    = &$this->rows[$i];
			$repeat = $row->getNextRepeat();

			/*
			  $Itemid	= JEVHelper::getItemid(false, false);
			  $link = $repeat->viewDetailLink($repeat->yup(),$repeat->mup(),$repeat->dup(),false, $Itemid);

			  // generate SEF url = see http://forum.joomla.org/viewtopic.php?f=544&t=454993#p2013006 thanks
			  jimport( 'joomla.application.router' );
			  require_once (JPATH_ROOT . '/' . 'includes' . '/' . 'router.php');
			  require_once (JPATH_ROOT . '/' . 'includes' . '/' . 'application.php');
			  // better will be check if SEF option is enable!
			  $router = new SiteRouter(array('mode'=>JROUTER_MODE_SEF));

			  $link = $router->build($link)->toString(array('path', 'query', 'fragment'));

			  // SEF URL !
			  $link = JURI::root().str_replace('/administrator/', '', $link);
			 */

			// non-sef URL
			//$Itemid	= JEVHelper::getItemid(false, false);
			// use dummy Itemid of 1 which we will replace
			$link = $repeat->viewDetailLink($repeat->yup(), $repeat->mup(), $repeat->dup(), false, 1);
			?>
			<tr class="row<?php echo $k; ?>">
				<td>
					<a href="#select"
					   onclick="return window.parent.<?php echo $function; ?>('<?php echo $link; ?>','<?php echo addslashes(htmlspecialchars($repeat->title())); ?>' , (jQuery('#Itemid').length?jQuery('#Itemid').val():0) , <?php echo $repeat->ev_id(); ?>, <?php echo $repeat->rp_id(); ?>, '<?php echo $editor; ?>')"
					   title="<?php echo Text::_('JEV_SELECT_EVENT'); ?>"><?php echo $row->title(); ?></a>
				</td>
				<td align="center">
					<?php
					if ($row->hasrepetition())
					{
						if ($app->isClient('administrator'))
						{
							$img = '<span class="icon-list"> </span>';
						}
						else
						{
							$img = HTMLHelper::_('image', 'system/calendar.png', '', array('title' => ''), true);
						}
						?>
						<a href="<?php echo Route::_("index.php?option=com_jevents&tmpl=component&task=icalrepeat.select&evid=" . $row->ev_id() . "&function=" . $function . "&" . Session::getFormToken() . '=1&nomenu=' . $input->getInt("nomenu")); ?>"
						   title="<?php echo Text::_("JEV_SELECT_REPEAT"); ?>">
							<?php echo $img; ?>
						</a>
					<?php } ?>
				</td>
				<td align="center"><?php echo $row->creatorName(); ?></td>
				<td align="center">
					<?php
					$img = $row->state() ? "<i gsl-icon='icon:check'></i>" : "<i gsl-icon='icon:close'></i>";
					?>
					<?php echo $img; ?>
				</td>
				<td>
					<?php
					if ($this->_largeDataSet)
					{
						echo Text::_('JEV_FROM') . ' : ' . $row->publish_up();
					}
					else
					{
						$times = '<table style="border: 1px solid #666666; width:100%;">';
						$times .= '<tr><td>' . Text::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : $row->publish_up()) . '</td></tr>';
						$times .= '<tr><td>' . Text::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? StringHelper::substr($row->publish_down(), 0, 10) : $row->publish_down()) . '</td></tr>';
						$times .= "</table>";
						echo $times;
					}
					?>
				</td>
				<td align="center"><?php echo $row->created(); ?></td>
				<td align="center"><?php echo $row->_groupname; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<th align="center" colspan="10"><?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true)); ?></th>
		</tr>
		</tfoot>
	</table>
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	<input type="hidden" name="function" value="<?php echo $function; ?>"/>
	<input type="hidden" name="task" value="icalevent.select"/>
	<input type="hidden" name="tmpl" value="component"/>
	<input type="hidden" name="filter_order" value="asc"/>
	<input type="hidden" name="filter_order_Dir" value="asc"/>
</form>
<br/>
