<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();

// Note that using a $limit value of -1 the limit is ignored in the query
$this->data = $data = $this->datamodel->getYearData($this->year,$this->limit, $this->limitstart);

echo "<div id='cal_title'>". JText::_('JEV_EVENTSFOR') ."</div>\n";
//echo '<fieldset id="ev_fieldset"><legend class="ev_fieldset">' . JText::_('JEV_ARCHIVE') . '</legend><br />' . "\n";
?>
<table align="center" width="90%" cellspacing="0" cellpadding="0" class="ev_table">
    <tr valign="top">
        <td colspan="2"  align="center" class="cal_td_daysnames">
           <!-- <div class="cal_daysnames"> -->
            <?php echo $data["year"] ;?>
            <!-- </div> -->
        </td>
    </tr>
<?php
for($month = 1; $month <= 12; $month++) {
	$num_events = count($data["months"][$month]["rows"]);
	if ($num_events>0){
		echo "<tr><td class='ev_td_left'>".JEventsHTML::getDateFormat($this->year,$month,'',3)."</td>\n";
		echo "<td class='ev_td_right'>\n";
		echo "<ul class='ev_ul'>\n";
		for ($r = 0; $r < $num_events; $r++) {
			if (!isset($data["months"][$month]["rows"][$r])) continue;
			$row =& $data["months"][$month]["rows"][$r];
			$listyle = 'style="border-color:'.$row->bgcolor().';"';

			echo "<li class='ev_td_li' $listyle>\n";
			if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
				$this->viewEventRowNEW ($row);
				echo "&nbsp;::&nbsp;";
				$this->viewEventCatRowNEW ($row);
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
		echo '</td></tr>' . "\n";
	}

}
echo '</table><br />' . "\n";
//echo '</fieldset><br />' . "\n";

// Create the pagination object
//if ($data["total"]>$data["limit"]){
$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
//}
