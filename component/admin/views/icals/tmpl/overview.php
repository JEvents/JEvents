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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('behavior.multiselect');
//HTMLHelper::_('behavior.modal', 'a.modal');

// Load the jQuery plugin && CSS
HTMLHelper::_('stylesheet', 'jui/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'jui/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
$db   = Factory::getDbo();
$user = Factory::getUser();

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

        <div class="mainlistblock">
            <div class="mainlist">

                <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist  table table-striped">
                    <tr>
                        <th width="20" nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th class="title" width="30%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_SUMMARY'); ?></th>
	                    <th width="4%" nowrap="nowrap"><?php echo Text::_('ICAL_EVENTS'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_TYPE'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_CATEGORY_NAME'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_ADMIN_REFRESH'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_PUBLISHED'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_EVENT_ANONREFRESH'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_EVENT_ISDEFAULT'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_ACCESS'); ?></th>
                        <th width="10%" nowrap="nowrap"><?php echo Text::_('JEV_ICAL_ID'); ?></th>
                    </tr>

                    <?php
                    $k        = 0;
                    $nullDate = $db->getNullDate();

                    for ($i = 0, $n = count($this->rows); $i < $n; $i++)
                    {
                        $row = &$this->rows[$i];
                        ?>
                        <tr class="row<?php echo $k; ?>">
                            <td width="20">
                                <input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->ics_id; ?>"
                                       onclick="Joomla.isChecked(this.checked);"/>
                            </td>
                            <td>
                                <a href="#edit" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','icals.edit')"
                                   title="<?php echo Text::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->label; ?></a>
                            </td>
	                        <td>
		                        <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list&filter[icsFile]=" . $row->ics_id,false);?>"
		                           title="<?php echo Text::_('COM_JEVENTS_SEARCH_FILTER'); ?>"><span class="gsl-icon" gsl-icon="icon:calendar;"></span></a>
	                        </td>
                            <td align="center">
                                <?php
                                $types           = array("Remote", "Uploaded File", "Native");
                                $typeTranslation = 'COM_JEVENTS_MANAGE_CALENDARS_OVERVIEW_' . str_replace(' ', '_', strtoupper($types[$row->icaltype]));
                                echo Text::_($typeTranslation);
                                ?>
                            </td>
                            <td align="center"><?php echo $row->category; ?></td>
                            <td align="center">
                                <?php
                                // only offer reload for URL based ICS
                                if ($row->srcURL != "")
                                {
                                    ?>
                                    <a href="javascript: void(0);"
                                       onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','icals.reload')">
                                        <span gsl-icon="icon:refresh"></span>
                                    </a>
                                    <?php
                                }
                                ?>

                            </td>
                            <td align="center">
                                <?php
                                $img = $row->state ? "<i gsl-icon='icon:check' class='gsl-text-success'></i>" : "<i gsl-icon='icon:close' class='gsl-text-danger'></i>";
                                ?>
                                <a href="javascript: void(0);"
                                   onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $row->state ? 'icals.unpublish' : 'icals.publish'; ?>')">
                                    <?php echo $img; ?>
                                </a>
                            </td>
                            <td align="center">
                                <?php
                                if ($row->icaltype == 0)
                                {
                                    $img = $row->autorefresh ? "<i gsl-icon='icon:check'></i>" : "<i gsl-icon='icon:close'></i>";
                                    ?>
                                    <a href="javascript: void(0);"
                                       onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $row->autorefresh ? 'icals.noautorefresh' : 'icals.autorefresh'; ?>')">
                                        <?php echo $img; ?>
                                    </a>
                                    <?php
                                    if ($row->autorefresh)
                                    {
	                                    $icalkey = "";
										if ($params->get("icalkeyimport", 0))
										{
											$icalkey = $params->get("icalkey", "secret phrase");
											$icalkey = "&k=" . md5($row->ics_id . "something really stupid" . $icalkey);
										}
	                                    ?>
                                        <br/><a
                                            href="<?php echo Uri::root() . "index.php?option=" . JEV_COM_COMPONENT . "&icsid=" . $row->ics_id . "&task=icals.reload" . $icalkey; ?>"
                                            title="<?php echo Text::_("JEV_AUTOREFRESH_LINK") ?>"><?php echo Text::_("JEV_AUTOREFRESH_LINK") ?></a>
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
                                    $img = $row->isdefault ? "<i gsl-icon='icon:check'></i>" : "<i gsl-icon='icon:close'></i>";
                                    ?>
                                    <a href="javascript: void(0);"
                                       onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $row->isdefault ? 'icals.notdefault' : 'icals.isdefault'; ?>')">
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
                        <th align="center" colspan="10"><?php echo $this->pagination->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true)); ?></th>
                    </tr>
                </table>
                <?php echo HTMLHelper::_('form.token'); ?>
                <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
                <input type="hidden" name="task" value="icals.list"/>
                <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
            </div>
        </div>
	</div>
</form>

