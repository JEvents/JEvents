<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;


$data = $this->data;

$Itemid = JEVHelper::getItemid();

echo "<div id='cal_title'>" . Text::_('JEV_EVENTSFOR') . "</div>\n";
?>
	<table align="center" width="90%" cellspacing="0" cellpadding="5" class="ev_table">
	<tr valign="top">
		<td colspan="2" align="center" class="cal_td_daysnames">
			<!-- <div class="cal_daysnames"> -->
			<?php echo $this->dateFormattedDateRange(); ?>
			<!-- </div> -->
		</td>
	</tr>
<?php
$num_events = count($data['rows']);
$chdate     = "";
if ($num_events > 0)
{
	echo "<tr>\n";

	for ($r = 0; $r < $num_events; $r++)
	{
		$row = $data['rows'][$r];

		$event_day_month_year = $row->dup() . $row->mup() . $row->yup();
		// Ensure we reflect multiday setting
		if (!$row->eventOnDate(JevDate::mktime(0, 0, 0, $row->mup(), $row->dup(), $row->yup()))) continue;

		if (($event_day_month_year <> $chdate) && $chdate <> '')
		{
			echo '</ul></td></tr>' . "\n";
		}

		$datenow = JEVHelper::getNow();
		$class   = $datenow->toFormat('%Y-%m-%d') > $row->startDate() ? "class='pastevent'" : "";
		if ($event_day_month_year <> $chdate)
		{
			$date = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 1);
			echo '<tr ' . $class . '><td class="ev_td_left">' . $date . '</td>' . "\n";
			echo '<td align="left" valign="top" class="ev_td_right"><ul class="ev_ul">' . "\n";
		}
		if ($event_day_month_year <> $chdate)
		{
//    			$date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
			//  			echo '<tr><td class="ev_td_left">'.$date.'</td>' . "\n";
			//			echo '<td align="left" valign="top" class="ev_td_right"><ul class="ev_ul">' . "\n";
		}

		$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
		echo "<li class='ev_td_li' $listyle>\n";
		$this->loadedFromTemplate('icalevent.list_row', $row, 0);
		echo "</li>\n";

		$chdate = $event_day_month_year;
	}
	echo "</ul></td>\n";
}
else
{
	echo '<tr>';
	echo '<td align="left" valign="top" class="ev_td_right">' . "\n";

	echo Text::_('JEV_NO_EVENTS') . '</td>';
}

echo '</tr></table><br />' . "\n";
echo '</fieldset><br /><br />' . "\n";

// Create the pagination object
if ($data["total"] > $data["limit"])
{
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
