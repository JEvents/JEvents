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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;

global $task;
$db   = Factory::getDbo();
$user = Factory::getUser();


// Receive overridable options for Filters
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$selectorFieldName = isset($data['options']['selectorFieldName']) ? $data['options']['selectorFieldName'] : 'client_id';
$showSelector = true;
// Set some basic options.
$customOptions = array(
	'defaultLimit'        => 20,
	'searchFieldSelector' => '#search',
	'formSelector'        => !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm',
);
// Merge custom options in the options array Filters
$data['options'] = array_merge($customOptions, $data['options']);

?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<div id="ysts-main-container">
			<?php
			// Search tools bar
			// I need to create and initialise the filter form for this to work!
			echo LayoutHelper::render('joomla.searchtools.jevents', array('view' => $this));
			?>
			<!-- End Filters -->
			<div class="clearfix"></div>

			<table class="adminlist gsl-table gsl-table-striped gsl-table-hover">
				<tr>
					<th width="20" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="title" width="60%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_SUMMARY'); ?></th>
					<th width="30%"
					    nowrap="nowrap"><?php echo Text::_('COM_JEVENTS_ICALREPEAT_REPEAT_DATE_TIME'); ?></th>
					<th width="10%" style="text-align:center"
					    nowrap="nowrap"><?php echo Text::_('COM_JEVENTS_ICALREPEAT_REPEAT_IS_EXCEPTION'); ?></th>
				</tr>

				<?php
				$k        = 0;
				$nullDate = $db->getNullDate();

				for ($i = 0, $n = count($this->icalrows); $i < $n; $i++)
				{
					$row = &$this->icalrows[$i]; ?>
					<tr class="row<?php echo $k; ?>">
						<td >
							<?php echo HTMLHelper::_('grid.id', $i, $row->rp_id()); ?>
						</td>
						<td >
							<a href="index.php?option=com_jevents&task=icalrepeat.edit&cid[]=<?php echo $row->rp_id(); ?>" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','icalrepeat.edit')"
							   title="<?php echo Text::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
						</td>
						<td >
							<?php
							$times = '<table class="gsl-table gsl-table-small gsl-margin-remove" >';
							$times .= '<tr><td>' . Text::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(), 0, 16)) . '</td></tr>';
							$times .= '<tr><td>' . Text::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? StringHelper::substr($row->publish_down(), 0, 10) : StringHelper::substr($row->publish_down(), 0, 16)) . '</td></tr>';
							$times .= "</table>";
							echo $times;
							?>
						</td>
						<td style="text-align:center">
							<?php
							$exception_type = 0;
							if (isset($row->_exception_type) && !is_null($row->_exception_type))
							{
								$exception_type = (int) $row->_exception_type;
							}
							if ($exception_type)
							{
								?>
								<span class="gsl-icon" data-gsl-icon="icon:cog" ></span>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				} ?>
				<tr>
					<th align="center" colspan="3" style="text-align:center;"><?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true)); ?></th>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
			<input type="hidden" name="cid[]" value="0"/>
			<input type="hidden" name="evid" value="<?php echo $this->evid; ?>"/>
			<input type="hidden" name="task" value="icalrepeat.list"/>
			<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		</div>
	</form>

	<br/>
<?php

