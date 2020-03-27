<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;


$cfg = JEVConfig::getInstance();

// Note that using a $limit value of -1 the limit is ignored in the query
$this->data = $data = $this->datamodel->getYearData($this->year, $this->limit, $this->limitstart);

echo "<div id='cal_title'>" . Text::_('JEV_EVENTSFOR') . "</div>\n";
//echo '<fieldset id="ev_fieldset"><legend class="ev_fieldset">' . Text::_('JEV_ARCHIVE') . '</legend><br />' . "\n";
?>
	<table align="center" width="90%" cellspacing="0" cellpadding="0" class="ev_table">
	<tr valign="top">
		<td colspan="2" align="center" class="cal_td_daysnames">
			<!-- <div class="cal_daysnames"> -->
			<?php echo $data["year"]; ?>
			<!-- </div> -->
		</td>
	</tr>
<?php
if ($data["total"] <= 0 && $cfg->get('year_show_noev_found', 0))
{

	echo '<tr><td colspan="2" class="no_events_found">' . Text::_('JEV_NO_EVENTS_FOUND') . '</td></tr>';

}
else
{
	for ($month = 1; $month <= 12; $month++)
	{
		$num_events = count($data["months"][$month]["rows"]);
		if ($num_events > 0)
		{
			echo "<tr><td class='ev_td_left'>" . JEventsHTML::getDateFormat($this->year, $month, '', 3) . "</td>\n";
			echo "<td class='ev_td_right'>\n";
			echo "<ul class='ev_ul'>\n";
			for ($r = 0; $r < $num_events; $r++)
			{
				if (!isset($data["months"][$month]["rows"][$r])) continue;
				$row     =& $data["months"][$month]["rows"][$r];
				$listyle = 'style="border-color:' . $row->bgcolor() . ';"';

				echo "<li class='ev_td_li' $listyle>\n";
				$this->loadedFromTemplate('icalevent.list_row', $row, 0);
				echo "</li>\n";
			}
			echo "</ul>\n";
			echo '</td></tr>' . "\n";
		}
		/*
		else {
                        echo "<tr><td class='ev_td_left'>".JEventsHTML::getDateFormat($this->year,$month,'',3)."</td>\n";
                        echo "<td class='ev_td_right'>\n";
                        echo "<ul class='ev_ul'>\n";
                        echo "<li>\n";
                        echo "<br />";
                        //echo Text::_('JEV_NO_EVENTS_FOUND');
                        echo "</li>\n";
                        echo "</ul></td></tr>\n";
                }
		 */

	}
}
echo '</table><br />' . "\n";
//echo '</fieldset><br />' . "\n";

// Create the pagination object
//if ($data["total"]>$data["limit"]){
$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
//}
