<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();
$hasevents = false;

echo '<fieldset><legend class="ev_fieldset">' . JText::_('JEV_EVENTSFOR') . '&nbsp;' . JText::_('JEV_WEEK')
. ' : </legend><br />' . "\n";
echo '<table align="center" width="90%" cellspacing="0" cellpadding="5" class="ev_table">' . "\n";
?>
    <tr valign="top">
        <td colspan="2"  align="center" class="cal_td_daysnames">
           <!-- <div class="cal_daysnames"> -->
            <?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?>
            <!-- </div> -->
        </td>
    </tr>
<?php
for( $d = 0; $d < 7; $d++ ){

	$day_link = '<a class="ev_link_weekday" href="' . $data['days'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
	. JEV_CommonFunctions::jev_strftime("%A", JevDate::mktime(3,0,0,$data['days'][$d]['week_month'], $data['days'][$d]['week_day'], $data['days'][$d]['week_year']))."<br/>"
	. JEventsHTML::getDateFormat( $data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 2 ).'</a>'."\n";

	if( $data['days'][$d]['today'])	$bg = 'class="ev_td_today"';
	else $bg = 'class="ev_td_left"';

	echo '<tr><td ' . $bg . '>' . $day_link . '</td>' . "\n";
	echo '<td class="ev_td_right">' . "\n";

	$num_events		= count($data['days'][$d]['rows']);
	if ($num_events>0) {
            $hasevents = true;
		echo "<ul class='ev_ul'>\n";

		for( $r = 0; $r < $num_events; $r++ ){
			$row = $data['days'][$d]['rows'][$r];

			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";
			$this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>\n";
		}
		echo "</ul>\n";
	} else {
            echo "<ul class='ev_ul'>\n";
            echo "<li class='b0'>".JText::_('JEV_NOEVENT_ON_THIS_DATE')."</li>";
            echo "</ul>\n";
        }
	echo '</td></tr>' . "\n";
} // end for days
//if (!$hasevents)
//{
//       echo '<tr><td class="ev_td_right" colspan="3"><ul class="ev_ul">' . "\n";
//       echo "<li class='ev_td_li b0'>\n";
//        echo JText::_('JEV_NO_EVENTS');
//        echo "</li>\n";
//        echo "</ul></td></tr>\n";
//}

echo '</table><br />' . "\n";
echo '</fieldset><br /><br />' . "\n";


