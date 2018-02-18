<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: overview.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;

JHTML::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Load the jQuery plugin && CSS
JHtml::_('stylesheet', 'jui/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'jui/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));

// we would use this to add custom data to the output here
//JEVHelper::onDisplayCustomFieldsMultiRow($this->rows);

$db   = JFactory::getDbo();
$user = JFactory::getUser();

// get configuration object
$cfg                 = JEVConfig::getInstance();
$this->_largeDataSet = $cfg->get('largeDataSet', 0);
$orderdir            = JFactory::getApplication()->getUserStateFromRequest("eventsorderdir", "filter_order_Dir", 'asc');
$order               = JFactory::getApplication()->getUserStateFromRequest("eventsorder", "filter_order", 'start');
$pathIMG             = JURI::root() . 'administrator/images/';
$mainspan            = 10;
$fullspan            = 12;

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
	<?php endif; ?>

    <div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
        <div class="js-stools clearfix">
            <div class="clearfix">
                <div class="js-stools-container-bar">
                    <label for="search" class="element-invisible">
                        Search </label>
                    <div class="btn-wrapper input-append">
                        <input type="text" id="search" name="search" value="<?php echo $this->search; ?>"
                               placeholder="<?php echo JText::_('JEV_SEARCH'); ?>" class="inputbox"
                               onChange="Joomla.submitform();"/>
                        <button type="submit" class="btn hasTooltip" title="" aria-label="Search"
                                data-original-title="Search">
                            <span class="icon-search" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="btn-wrapper hidden-phone">
                        <button type="button" class="btn hasTooltip js-stools-btn-filter" title=""
                                data-original-title="Filter the list items.">
                                <?php echo JText::_('JSEARCH_TOOLS'); ?>  <span class="caret"></span>
                        </button>
                    </div>
                    <div class="btn-wrapper">
                        <button type="button" class="btn hasTooltip js-stools-btn-clear" title=""
                                data-original-title="Clear">
	                        <?php echo JText::_('JCLEAR'); ?>
                        </button>
                    </div>
                </div>
                <div class="js-stools-container-list hidden-phone hidden-tablet">
                    <div class="hidden-select hidden-phone">
                        <div class="js-stools-field-list">
							<?php echo $this->plist; ?>
                        </div>
                        <div class="js-stools-field-list">
							<?php echo $this->pageNav->getLimitBox(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filters div -->
            <div class="js-stools-container-filters hidden-phone clearfix">
				<?php foreach ($this->filters AS $filter) { ?>
                    <div class="js-stools-field-filter">
						<?php echo $filter; ?>
                    </div>
				<?php } ?>
            </div>
        </div>

        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist  table table-striped">
            <tr>
                <th width="20" nowrap="nowrap">
					<?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th class="title" width="40%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'JEV_ICAL_SUMMARY', 'title', $orderdir, $order, "icalevent.list"); ?>
                </th>
                <th width="10%" nowrap="nowrap"><?php echo JText::_('REPEATS'); ?></th>
                <th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_EVENT_CREATOR'); ?></th>
				<?php
				if (count($this->languages) > 1)
				{
					?>
                    <th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_EVENT_TRANSLATION'); ?></th>
				<?php } ?>
                <th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_PUBLISHED'); ?></th>
                <th width="20%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'JEV_TIME_SHEET', 'starttime', $orderdir, $order, "icalevent.list"); ?>
                </th>
                <th width="20%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'JEV_FIELD_CREATIONDATE', 'created', $orderdir, $order, "icalevent.list"); ?>
                </th>
                <th width="20%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'JEV_MODIFIED', 'modified', $orderdir, $order, "icalevent.list"); ?>
                </th>
                <th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_ACCESS'); ?></th>
            </tr>

			<?php
			$k        = 0;
			$nullDate = $db->getNullDate();

			for ($i = 0, $n = count($this->rows); $i < $n; $i++)
			{
				$row = &$this->rows[$i];
				?>
                <tr class="row<?php echo $k; ?>">
                    <td width="20" style="background-color:<?php echo JEV_CommonFunctions::setColor($row); ?>">
						<?php echo JHtml::_('grid.id', $i, $row->ev_id()); ?>
                    </td>
                    <td>
                        <a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','icalevent.edit')"
                           title="<?php echo JText::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
                    </td>
                    <td align="center">
						<?php
						if ($row->hasrepetition())
						{
							?>
                            <a href="javascript: void(0);"
                               onclick="return listItemTask('cb<?php echo $i; ?>','icalrepeat.list')"
                               class="btn btn-micro">
                                <span class="icon-list"> </span>
                            </a>
						<?php } ?>
                    </td>
                    <td align="center"><?php echo $row->creatorName(); ?></td>
					<?php if (count($this->languages) > 1) { ?>
                        <td align="center"><?php echo $this->translationLinks($row); ?>    </td>
					<?php } ?>

                    <td align="center">
						<?php
						if ($row->state() == 1)
						{
							$img = JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true);
						}
						else if ($row->state() == 0)
						{
							$img = JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
						}
						else
						{
							$img = JHTML::_('image', 'admin/trash.png', '', array('title' => ''), true);
						}
						?>
                        <a href="javascript: void(0);"
                           onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->state() ? 'icalevent.unpublish' : 'icalevent.publish'; ?>')"
                           class="btn btn-micro">
							<?php echo $img; ?>
                        </a>
                    </td>
                    <td>
						<?php
						if ($this->_largeDataSet)
						{
							echo JText::_('JEV_FROM') . ' : ' . $row->publish_up();
						}
						else
						{
							$times = '<table style="border: 1px solid #666666; width:100%;">';
							$times .= '<tr><td>' . JText::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? JString::substr($row->publish_up(), 0, 10) : JString::substr($row->publish_up(), 0, 16)) . '</td></tr>';
							$times .= '<tr><td>' . JText::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? JString::substr($row->publish_down(), 0, 10) : JString::substr($row->publish_down(), 0, 16)) . '</td></tr>';
							$times .= "</table>";
							echo $times;
						}
						?>
                    </td>
                    <td align="center"><?php echo $row->created(); ?></td>
                    <td align="center"><?php echo $row->modified; ?></td>
                    <td align="center"><?php echo $row->_groupname; ?></td>
                </tr>
				<?php
				$k = 1 - $k;
			}
			?>
        </table>
		<?php echo $this->pageNav->getListFooter(); ?>
        <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
        <input type="hidden" name="task" value="icalevent.list"/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $order; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $orderdir; ?>"/>
		<?php echo JHtml::_('form.token'); ?>

    </div>
</form>