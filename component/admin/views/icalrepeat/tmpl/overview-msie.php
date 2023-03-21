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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;

JEventsHelper::addSubmenu();
$this->sidebar = JHtmlSidebar::render();

global   $task;
$db	= Factory::getDbo();
$user = Factory::getUser();

$pathIMG = JURI::Root() . 'administrator/images/';
$pathJeventsIMG = JURI::Root() . "administrator/components/".JEV_COM_COMPONENT."/images/";
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
                </tr>
            </table>

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist table table-striped">
                <tr>
                    <th width="20" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th class="title" width="60%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_SUMMARY'); ?></th>
                    <th width="40%" nowrap="nowrap"><?php echo Text::_('COM_JEVENTS_ICALREPEAT_REPEAT_DATE_TIME'); ?></th>
                </tr>

				<?php
				$k 	= 0;
				$nullDate 	= $db->getNullDate();

				for( $i=0, $n=count( $this->icalrows ); $i < $n; $i++ ){
					$row = &$this->icalrows[$i]; ?>
                    <tr class="row<?php echo $k; ?>">
                        <td width="20">
							<?php echo HTMLHelper::_('grid.id', $i, $row->rp_id()); ?>
                        </td>
                        <td width="30%">
                            <a href="#edit" onclick="return Joomla.listItemTask('cb<?php echo $i;?>','icalrepeat.edit')" title="<?php echo Text::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
                        </td>
                        <td width="40%">
							<?php
							$times = '<table style="border: 1px solid #666666; width:100%;">';
							$times .= '<tr><td>' . Text::_('JEV_FROM') . ' : ' . ($row->alldayevent() ? StringHelper::substr($row->publish_up(), 0, 10) : StringHelper::substr($row->publish_up(),0,16)) . '</td></tr>';
							$times .= '<tr><td>' . Text::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent()) ? StringHelper::substr($row->publish_down(), 0, 10) : StringHelper::substr($row->publish_down(),0,16)) . '</td></tr>';
							$times .="</table>";
							echo $times;
							?>
                        </td>
                    </tr>
					<?php
					$k = 1 - $k;
				} ?>
                <tr>
                    <th align="center" colspan="3"><?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true)); ?></th>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
            <input type="hidden" name="cid[]" value="0" />
            <input type="hidden" name="evid" value="<?php echo $this->evid;?>" />
            <input type="hidden" name="task" value="icalrepeat.list" />
            <input type="hidden" name="boxchecked" id="boxchecked" value="0" />
        </div>
    </form>

    <br />
<?php

