<?php
defined('_JEXEC') or die('Restricted access');

$data = $this->data;

$Itemid = JEVHelper::getItemid();
?>
<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme; ?>'>
	<div class="jev_toprow">
		<div class="jev_header">
			<h2><?php echo JText::_("JEV_DATE_RANGE_VIEW"); ?></h2>
			<div class="today" >
				<?php echo $this->dateFormattedDateRange();?>
			</div>
		</div>
		<div class="jev_header2">
			<div class="jev_topleft jev_topleft_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>" ></div>
			<div class="previousmonth" >
			</div>
			<div class="currentmonth">
				<?php echo $this->dateFormattedDateRange();?>
			</div>
			<div class="nextmonth">
			</div>

		</div>
	</div>
    <div class="jev_clear" ></div>

	<?php
	$num_events = count($data['rows']);
	$chdate = "";
	if ($num_events > 0)
	{

		for ($r = 0; $r < $num_events; $r++)
		{
			$row = $data['rows'][$r];
			$event_month_year = $row->dup() . $row->mup() . $row->yup();

			$event_day_month_year = $row->dup() . $row->mup() . $row->yup();
			// Ensure we reflect multiday setting
			if (!$row->eventOnDate(JevDate::mktime(0, 0, 0, $row->mup(), $row->dup(), $row->yup())))
				continue;

			if ($event_month_year <> $chdate && $chdate <> "")
			{
				?>
			</ul>
			</div>
			<?php
		}
		if ($event_month_year <> $chdate)
		{
			?>
			<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>">
					<?php echo JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 0); ?>
			</div>
			<div class="jev_listrow">
				<ul class='ev_ul'>	
					<?php
				}
				$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
				echo "<li class='ev_td_li' $listyle>\n";

				$this->loadedFromTemplate('icalevent.list_row', $row, 0);
				echo "</li>";
				$chdate = $event_month_year;
			}
			?>	
		</ul>
	</div>
	<?php
}
?>
</div>
<div class="jev_clear" ></div>
<?php
// Create the pagination object
if ($data["total"] > $data["limit"])
{
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
