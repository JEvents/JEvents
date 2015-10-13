<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

$cfg = JEVConfig::getInstance();
$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/" . $viewname . "/assets";
$viewimages = $viewpath . "/images";

$view = $this->getViewName();

$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);
?>

<div class="jev_toprow">
    <div class="jev_header2">
        <div class="previousmonth">
            <?php if ($precedingWeek) echo "<a href='" . $precedingWeek . "' title='" . JText::_("PRECEEDING_Week") . "' >" . JText::_("PRECEEDING_Week") . "</a>"; ?>
        </div>
        <div class="currentmonth">
            <?php
            $week_start = $data ['days'] ['0'];
            $week_end = $data ['days'] ['6'];

            $starttime = JevDate::mktime(0, 0, 0, $week_start ['week_month'], $week_start ['week_day'], $week_start ['week_year']);
            $endtime = JevDate::mktime(0, 0, 0, $week_end ['week_month'], $week_end ['week_day'], $week_end ['week_year']);

            if ($week_start ['week_month'] == $week_end ['week_month']) {
                $startformat = "%d";
                $endformat = "%d %B, %Y";
            } else if ($week_start ['week_year'] == $week_end ['week_year']) {
                $startformat = "%d %B";
                $endformat = "%d %B, %Y";
            } else {
                $startformat = "%d. %B  %Y";
                $endformat = "%d. %B %Y";
            }
            echo JEV_CommonFunctions::jev_strftime($startformat, $starttime) . ' - ' . JEV_CommonFunctions::jev_strftime($endformat, $endtime);
            ?>
        </div>
        <div class="nextmonth">
            <?php if ($followingWeek) echo "<a href='" . $followingWeek . "' title='" . JText::_("FOLLOWING_Week") . "' >" . JText::_("FOLLOWING_Week") . "</a>"; ?>
        </div>

    </div>
</div>
<div id='jev_maincal' class='jev_listview'>

    <?php
    $hasevents = false;
    for ($d = 0; $d < 7; $d ++) {
        $num_events = count($data ['days'] [$d] ['rows']);
        if ($num_events == 0)
            continue;

        echo '<a class="ev_link_weekday" href="' . $data ['days'] [$d] ['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">' . "\n";
        ?>
        <div class="jev_daysnames">
            <?php echo JEventsHTML::getDateFormat($data ['days'] [$d] ['week_year'], $data ['days'] [$d] ['week_month'], $data ['days'] [$d] ['week_day'], 2); ?>
        </div>
    </a>
    <div class="jev_listrow">
        <?php
        if ($num_events > 0) {
            $hasevents = true;
            echo "<ul class='ev_ul'>\n";

            for ($r = 0; $r < $num_events; $r ++) {
                $row = $data ['days'] [$d] ['rows'] [$r];

                $listyle = 'style="border-color:' . $row->bgcolor() . ';"';
                echo "<li class='ev_td_li' $listyle>\n";
                $this->loadedFromTemplate('icalevent.list_row', $row, 0);
                echo "</li>\n";
            }
            echo "</ul>\n";
        }
        ?>
    </div>
        <?php
    } // end for days
    if (!$hasevents) {
        echo '<div class="list_no_e">' . "\n";
        echo JText::_('JEV_NO_EVENTS_FOUND');
        echo "</div>\n";
    }
    ?>
<div class="jev_clear"></div>
</div>