<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$data = $this->data;

$Itemid = JEVHelper::getItemid ();
$hasevents = false;
?>
<div class="jev_toprow">
	<div class="jev_header2">
		<div class="previousmonth"></div>
		<div class="currentmonth">
				<?php echo $this->dateFormattedDateRange();?>
			</div>
		<div class="nextmonth"></div>

	</div>
</div>
<div class="jev_clear"></div>
<div id='jev_maincal' class='jev_listview'>

	<?php
	$num_events = count ( $data ['rows'] );
	$chdate = "";
	if ($num_events > 0) {
		$hasevents = true;
		for($r = 0; $r < $num_events; $r ++) {
			$row = $data ['rows'] [$r];
			
			$event_day_month_year = $row->dup () . $row->mup () . $row->yup ();
			// Ensure we reflect multiday setting
			if (! $row->eventOnDate ( JevDate::mktime ( 0, 0, 0, $row->mup (), $row->dup (), $row->yup () ) ))
				continue;
			
			$date = JEventsHTML::getDateFormat ( $row->yup (), $row->mup (), $row->dup (), 1 );
			?>
			<div class="jev_listrow">
		<ul class='ev_ul'>

					<?php
			$listyle = 'style="border-color:' . $row->bgcolor () . ';"';
			echo "<li class='ev_td_li' $listyle>\n";
			
                        $this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>";
			?>	
				</ul>
	</div>
		<?php
		}
	}
if (! $hasevents) {
	echo '<div class="list_no_e">' . "\n";
	echo JText::_ ( 'JEV_NO_EVENTS_FOUND' );
	echo "</div>\n";
}
?>
    
<div class="jev_clear"></div>

</div>
<?php
// Create the pagination object
if ($data ["total"] > $data ["limit"]) {
	$this->paginationForm ( $data ["total"], $data ["limitstart"], $data ["limit"] );
}
