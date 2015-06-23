<?php 
defined('_JEXEC') or die('Restricted access');

// Note that using a $limit value of -1 the limit is ignored in the query
$this->data = $data = $this->datamodel->getYearData($this->year,$this->limit, $this->limitstart);

$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
$row = $params->get("row","");
$rowstripped = strip_tags($row);

$regex = "#{(.*?)}#i";
preg_match_all($regex, $rowstripped, $matches);
$cols = count($matches);
if ($cols==0) return;

echo "<div id='cal_title'>". JText::_('JEV_EVENTSFOR') ."</div>\n";
?>
<table align="center" width="90%" cellspacing="0" cellpadding="0" class="ev_table">
    <tr valign="top">
        <td colspan="2"  align="center" class="cal_td_daysnames">
            <?php echo $data["year"] ;?>
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
			$this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>\n";
		}
		echo "</ul>\n";
		echo '</td></tr>' . "\n";
	}

}
echo '</table><br />' . "\n";

// Create the pagination object
//if ($data["total"]>$data["limit"]){
$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
//}
