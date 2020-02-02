<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: overview.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2019 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;

global $task;
$db   = Factory::getDbo();
$user = Factory::getUser();
HTMLHelper::_('behavior.tooltip');

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


$mainspan       = 10;
$fullspan       = 12;
?>
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
<?php endif; ?>

	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<?php 		// Load search tools
			HTMLHelper::_('searchtools.form', $data['options']['formSelector'], $data['options']);
			?>
			<div class="js-stools clearfix">
				<div class="clearfix">
					<div class="js-stools-container-bar">
						<label for="search" class="element-invisible">
							<?php echo Text::_('JEV_SEARCH'); ?>
						</label>
						<div class="btn-wrapper input-append">
							<input type="text" id="search" name="search" value="<?php echo $this->search; ?>"
							       placeholder="<?php echo Text::_('JEV_SEARCH'); ?>" class="inputbox"
							       onChange="Joomla.submitform()" />
							<button type="submit" class="btn hasTooltip" title="" aria-label="Search"
							        data-original-title="Search">
								<span class="icon-search" aria-hidden="true"></span>
							</button>
						</div>
						<div class="btn-wrapper">
							<button type="button" class="btn hasTooltip js-stools-btn-clear" title=""
							        data-original-title="Clear">
								<?php echo Text::_('JCLEAR'); ?>
							</button>
						</div>
					</div>
					<div class="js-stools-container-list hidden-phone hidden-tablet">
						<div class="hidden-select hidden-phone">
							<div class="js-stools-field-list">
								<?php echo $this->plist; ?>
							</div>
							<div class="js-stools-field-list">
								<?php echo $this->pagination->getLimitBox(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<table cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="100%">
						&nbsp;
					</td>
				</tr>
			</table>

			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist table table-striped">
				<tr>
					<th width="20" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="title" width="60%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_SUMMARY'); ?></th>
					<th width="40%"
					    nowrap="nowrap"><?php echo Text::_('COM_JEVENTS_ICALREPEAT_REPEAT_DATE_TIME'); ?></th>
				</tr>

				<?php
				$k        = 0;
				$nullDate = $db->getNullDate();

				for ($i = 0, $n = count($this->icalrows); $i < $n; $i++)
				{
					$row = &$this->icalrows[$i]; ?>
					<tr class="row<?php echo $k; ?>">
						<td width="20">
							<?php echo HTMLHelper::_('grid.id', $i, $row->rp_id()); ?>
						</td>
						<td width="30%">
							<a href="index.php?option=com_jevents&task=icalrepeat.edit&cid[]=<?php echo $row->rp_id(); ?>" onclick="return listItemTask('cb<?php echo $i; ?>','icalrepeat.edit')"
							   title="<?php echo Text::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
						</td>
						<td width="40%">
							<?php
							$times = '<table style="border: 1px solid #666666; width:100%;">';
							$times .= '<tr><td>' . Text::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(), 0, 16)) . '</td></tr>';
							$times .= '<tr><td>' . Text::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? StringHelper::substr($row->publish_down(), 0, 10) : StringHelper::substr($row->publish_down(), 0, 16)) . '</td></tr>';
							$times .= "</table>";
							echo $times;
							?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				} ?>
				<tr>
					<th align="center" colspan="3" style="text-align:center;"><?php echo $this->pagination->getListFooter(); ?></th>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
			<input type="hidden" name="cid[]" value="0"/>
			<input type="hidden" name="evid" value="<?php echo $this->evid; ?>"/>
			<input type="hidden" name="task" value="icalrepeat.list"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</div>
	</form>

	<br/>
<?php		

