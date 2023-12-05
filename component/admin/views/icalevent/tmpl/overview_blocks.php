<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: overview.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
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
                <div class="adminlist">

                    <?php
                    $k        = 0;
                    $nullDate = $db->getNullDate();
                    $itemId = $params->get('default_itemid', 0);
                    $itemId = $itemId ? $itemId : $params->get('permatarget', 0);

                    for ($i = 0, $n = count($this->rows); $i < $n; $i++)
                    {
                        $row = &$this->rows[$i];
                        ?>
                        <div class="gsl-grid " >
                            <div class="gsl-width-1-12 gsl-text-center gsl-child-width-1-1">
	                            <div class="gsl-card gsl-card-default gsl-card-body  gsl-padding-remove-left"
	                                 style="background-color:<?php echo JEV_CommonFunctions::setColor($row); ?>">
                                <?php echo HTMLHelper::_('grid.id', $i, $row->ev_id()); ?>
	                            </div>
	                            <div class="gsl-card gsl-card-default gsl-card-body  gsl-padding-remove-bottom" >
		                            <?php
		                            if ($row->hasrepetition())
		                            {
			                            ?>
			                            <a href="javascript: void(0);"
			                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','icalrepeat.list')"
			                            >
				                            <span class="icon-list"> </span>
			                            </a>
		                            <?php } ?>
	                            </div>
	                            <div class="gsl-card gsl-card-default gsl-card-body  gsl-padding-remove-bottom" >
		                            <?php
		                            if ($row->state() == 1)
		                            {
			                            $img = "<i gsl-icon='icon:check' class='gsl-text-success'></i>";
		                            }
		                            else if ($row->state() == 0)
		                            {
			                            $img = "<i gsl-icon='icon:close' class='gsl-text-danger'></i>";
		                            }
		                            else
		                            {
			                            $img = "<i gsl-icon='icon:trash'></i>";
		                            }
		                            ?>
		                            <a href="javascript: void(0);"
		                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $row->state() ? 'icalevent.unpublish' : 'icalevent.publish'; ?>')"
		                            >
			                            <?php echo $img; ?>
		                            </a>
	                            </div>
                            </div>

                            <div class="gsl-width-5-12 gsl-padding-remove gsl-grid">
	                            <div class="gsl-width-1-1 gsl-card gsl-card-default gsl-card-body gsl-padding-remove-bottom">
		                            <span gsl-icon="icon:calendar;ratio:1"></span>
		                            <a href="index.php?option=com_jevents&task=icalevent.edit&cid=<?php echo $row->ev_id(); ?>"
		                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','icalevent.edit')"
		                               title="<?php echo Text::_('JEV_CLICK_TO_EDIT'); ?>"
		                            ><?php echo $row->title(); ?></a>

	                                <span gsl-lightbox >
	                                <a href="<?php  echo Uri::root() . $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false, $itemId);?>"
	                                   id="modal_preview"
	                                   title="Preview"
	                                   data-caption="Preview"
	                                   data-type="iframe"
	                                   >
		                                <span class="gsl-icon-link" gsl-icon="icon:desktop;ratio:1"></span>
	                                </a>
	                                </span>
	                            </div>
	                            <?php if (count($this->languages) > 1) { ?>
	                                <div class="gsl-width-1-3 gsl-card gsl-card-default gsl-card-body  gsl-padding-remove-bottom">
		                                <span gsl-icon="icon:world;ratio:1"></span>
			                            <?php
			                            ob_start();
			                            $this->translationLinks($row);
			                            $translationlinks = ob_get_clean();
			                            echo str_replace("item-associations", "item-associations gsl-margin-remove  gsl-padding-remove gsl-inline", $translationlinks);
			                            ?>
		                            </div>
	                            <?php } ?>
	                            <div class="gsl-width-1-3 gsl-card gsl-card-default gsl-card-body  gsl-padding-remove-bottom">
		                            <span gsl-icon="icon:user;ratio:1"></span>
		                            <?php echo $row->creatorName(); ?>
	                            </div>
	                            <div class="gsl-card gsl-card-default gsl-card-body  gsl-display-inline">
		                            <span gsl-icon="icon:lock;ratio:1"></span>
		                            <?php echo $row->_groupname; ?>
	                            </div>
                            </div>
                            <div class="gsl-width-1-6   gsl-card gsl-card-default gsl-card-body  gsl-padding-remove-bottom">
	                            <div class="gsl-inline gsl-text-top" gsl-icon="icon:calendar;ratio:1"></div>
	                            <div class="gsl-inline">
	                            <?php
                                if ($this->_largeDataSet)
                                {
                                	?>
	                                <?php
	                                echo Text::_('JEV_FROM') . ' : ' . $row->publish_up();
                                }
                                else
                                {
	                                $firstRepeat = $row->getFirstRepeat();

	                                $times = '<table class="gsl-table gsl-table-small gsl-inline">';
	                                $times .= '<tr><td>' . Text::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(),0,16)) . '</td></tr>';
	                                $times .= '<tr><td>' . Text::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? StringHelper::substr($row->publish_down(), 0, 10) : StringHelper::substr($row->publish_down(),0,16)) . '</td></tr>';
	                                if ($row->hasrepetition() && $firstRepeat->publish_up() !== $row->publish_up()) {
		                                $times .= '<tr><td>' . Text::_('JEV_NEXT_REPEAT') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(),0,16)) . '</td></tr>';
	                                }
	                                $times .="</table>";
	                                echo $times;
                                }
                                ?>
	                            </div>
                            </div>
	                        <div class="gsl-width-1-6  gsl-child-width-1-1 gsl-padding-remove" >
	                            <div class="gsl-card gsl-card-default gsl-card-body" >
	                            <?php
	                            echo Text::_('JEV_FIELD_CREATIONDATE') . ' : <br><br>';
	                            echo str_replace(" ", "<span class='createdseconds'> ", $row->created()) . "<span>";
	                            ?>
	                            </div>
	                            <div class="gsl-card gsl-card-default gsl-card-body" >
	                            <?php
	                            echo Text::_('JEV_MODIFIED') . ' : <br><br>';
                                echo str_replace(" ", "<span class='modifiedseconds'> ", $row->modified()) . "<span>";
	                            ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $k = 1 - $k;
                    }

                    if (count($this->rows) === 0) {
                        echo '<div class="gsl-width-1-1">' . Text::_("JEV_NO_EVENTS_FOUND") . '</div>';
                    } ?>
	                <div class="gsl-width-1-1" style="text-align:center"><?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true));?></div>
                </div>
                <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
                <input type="hidden" name="task" value="icalevent.list"/>
                <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
                <input type="hidden" name="filter_order" value="<?php echo $order; ?>"/>
                <input type="hidden" name="filter_order_Dir" value="<?php echo $orderdir; ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
