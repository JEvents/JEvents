<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: overview.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2019 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;


HTMLHelper::_('behavior.multiselect');
//HTMLHelper::_('behavior.modal', 'a.modal');

// Load the jQuery plugin && CSS
HTMLHelper::_('stylesheet', 'jui/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'jui/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));

// we would use this to add custom data to the output here
//JEVHelper::onDisplayCustomFieldsMultiRow($this->rows);

$app    = Factory::getApplication();
$db     = Factory::getDbo();
$user   = Factory::getUser();
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

// get configuration object
$cfg                 = JEVConfig::getInstance();
$this->_largeDataSet = $cfg->get('largeDataSet', 0);
$orderdir            = $app->getUserStateFromRequest("eventsorderdir", "filter_order_Dir", 'asc');
$order               = $app->getUserStateFromRequest("eventsorder", "filter_order", 'start');
$mainspan            = 10;
$fullspan            = 12;

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
// Add class to hide the active filters if needed.

// Pass custom filters into layout data
$data['filters'] = $this->filters;

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="eventlist">
    <div id="ysts-main-container">
		<?php
            // Search tools bar
            // I need to create and initialise the filter form for this to work!
            echo LayoutHelper::render('joomla.searchtools.jevents', array('view' => $this));
		?>

		<!-- End Filters -->
        <div class="clearfix"></div>

        <div class="mainlistblock">
            <div class="mainlist">
                <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist table table-striped">
                    <tr>
                        <th width="20" nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th class="title"  nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.sort', 'JEV_ICAL_SUMMARY', 'title', $orderdir, $order, "icalevent.list"); ?>
                        </th>
                        <th  nowrap="nowrap" class="center"><?php echo Text::_('ICAL_EVENT_REPEATS'); ?></th>
                        <th  nowrap="nowrap" class="center"><?php echo Text::_('JEV_EVENT_CREATOR'); ?></th>
                        <?php if (count($this->languages) > 1) { ?>
                            <th  nowrap="nowrap" class="center"><?php echo Text::_('JEV_EVENT_TRANSLATION'); ?></th>
                        <?php } ?>
                        <th  nowrap="nowrap" class="center"><?php echo Text::_('JSTATUS'); ?></th>
                        <th  nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.sort', 'JEV_TIME_SHEET', 'starttime', $orderdir, $order, "icalevent.list"); ?>
                        </th>
                        <th  nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.sort', 'JEV_FIELD_CREATIONDATE', 'created', $orderdir, $order, "icalevent.list"); ?>
                        </th>
                        <th  nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.sort', 'JEV_MODIFIED', 'modified', $orderdir, $order, "icalevent.list"); ?>
                        </th>
                        <th  nowrap="nowrap"><?php echo Text::_('JEV_ACCESS'); ?></th>
                    </tr>

                    <?php
                    $k        = 0;
                    $nullDate = $db->getNullDate();
                    $itemId = $params->get('default_itemid', 0);
                    $itemId = $itemId ? $itemId : $params->get('permatarget', 0);

                    for ($i = 0, $n = count($this->rows); $i < $n; $i++)
                    {
                        $row = &$this->rows[$i];
                        ?>
                        <tr class="row<?php echo $k; ?>">
                            <td width="20" style="background-color:<?php echo JEV_CommonFunctions::setColor($row); ?>">
                                <?php echo HTMLHelper::_('grid.id', $i, $row->ev_id()); ?>
                            </td>
                            <td>
                                <span gsl-lightbox >
                                <a href="<?php  echo Uri::root() . $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false, $itemId);?>"
                                   id="modal_preview" title="Preview"
                                   data-caption="Preview"
                                   data-type="iframe"
                                   >
                                    <span class="icon-out-2 small"></span>
                                </a>
                                </span>
                                <a href="index.php?option=com_jevents&task=icalevent.edit&cid=<?php echo $row->ev_id(); ?>"
                                   onclick="return listItemTask('cb<?php echo $i; ?>','icalevent.edit')"
                                   title="<?php echo Text::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
                            </td>
                            <td class="center">
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
                            <td class="center"><?php echo $row->creatorName(); ?></td>
                            <?php if (count($this->languages) > 1) { ?>
                                <td class="center"><?php echo $this->translationLinks($row); ?>    </td>
                            <?php } ?>

                            <td class="center">
                                <?php
                                if ($row->state() == 1)
                                {
                                    $img = HTMLHelper::_('image', 'admin/tick.png', '', array('title' => ''), true);
                                }
                                else if ($row->state() == 0)
                                {
                                    $img = HTMLHelper::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
                                }
                                else
                                {
                                    $img = HTMLHelper::_('image', 'admin/trash.png', '', array('title' => ''), true);
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
                                    echo Text::_('JEV_FROM') . ' : ' . $row->publish_up();
                                }
                                else
                                {
                                    $firstRepeat = $row->getFirstRepeat();

                                    $times = '<table style="border: 1px solid #666666; width:100%;">';
                                    $times .= '<tr><td>' . Text::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(),0,16)) . '</td></tr>';
                                    $times .= '<tr><td>' . Text::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? StringHelper::substr($row->publish_down(), 0, 10) : StringHelper::substr($row->publish_down(),0,16)) . '</td></tr>';
                                    if ($row->hasrepetition() && $firstRepeat->publish_up() !== $row->publish_up()) {
                                        $times .= '<tr><td>' . Text::_('JEV_NEXT_REPEAT') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(),0,16)) . '</td></tr>';
                                    }
                                    $times .="</table>";
                                    echo $times;
                                }
                                ?>
                            </td>
                            <td><?php echo str_replace(" ", "<span class='createdseconds'> ", $row->created()) . "<span>"; ?> </td>
                            <td><?php echo StringHelper::substr($row->modified, 0, 10); ?> </td>
                            <td><?php echo $row->_groupname; ?></td>
                        </tr>
                        <?php
                        $k = 1 - $k;
                    }

                    if (count($this->rows) === 0) {
                        echo '<tr class="row0"><td colspan="9">' . Text::_("JEV_NO_EVENTS_FOUND") . '</td></tr>';
                    } ?>
                </table>
                <?php echo $this->pagination->getListFooter(); ?>
                <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
                <input type="hidden" name="task" value="icalevent.list"/>
                <input type="hidden" name="boxchecked" value="0"/>
                <input type="hidden" name="filter_order" value="<?php echo $order; ?>"/>
                <input type="hidden" name="filter_order_Dir" value="<?php echo $orderdir; ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
