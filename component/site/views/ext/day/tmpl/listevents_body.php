<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDayData( $this->year, $this->month, $this->day );
$this->Redirectdetail();

$cfg = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();
$cfg = JEVConfig::getInstance();
$hasevents = false;

// previous and following month names and links
$followingDay = $this->datamodel->getFollowingDay($this->year, $this->month, $this->day);
$precedingDay = $this->datamodel->getPrecedingDay($this->year, $this->month, $this->day);

?>
<table class="maintable" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="tableh1" colspan="3">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr> 
					<td><h2><?php echo JText::_( 'DAILY_VIEW' );?></h2></td>
					<td class="today" align="right"><?php echo JEventsHTML::getDateFormat( $this->year, $this->month, $this->day, 0) ;?></td>
				</tr>
			</table>
	  </td>
	</tr>
		<tr>
			<td  class="previousmonth" align="center" height="22" nowrap="nowrap" valign="middle" width="33%">&nbsp;
<!-- BEGIN previous_month_link_row -->
      	<?php if ($precedingDay) {echo "<a href='".$precedingDay."' title='".JText::_("PRECEEDING_Day")."' >"?>
      	<?php echo JText::_("PRECEEDING_Day")."</a>";}?>
      	

<!-- END previous_month_link_row -->
			</td>
			<td  class="currentmonth" style="background-color: rgb(208, 230, 246);" align="center" height="22" nowrap="nowrap" valign="middle">
				<?php echo JEventsHTML::getDateFormat( $this->year, $this->month, $this->day, 0) ;?>
			</td>
			<td  class="nextmonth" align="center" height="22" nowrap="nowrap" valign="middle"  width="33%">
      	<?php if ($followingDay){ echo "<a href='".$followingDay."' title='".JText::_("FOLLOWING_Day")."' >"?>
      	<?php echo JText::_("FOLLOWING_Day")."</a>";?>
      	<?php echo "</a>";}?>

			</td>
		</tr>
<?php
// Timeless Events First
if (count($data['hours']['timeless']['events'])>0){
	$hasevents = true;
	$start_time = JText::_( 'TIMELESS' );

	echo '<tr><td class="ev_td_right" colspan="3"><ul class="ev_ul">' . "\n";
	foreach ($data['hours']['timeless']['events'] as $row) {
		$listyle = 'style="border-color:'.$row->bgcolor().';"';
		echo "<li class='ev_td_li' $listyle>\n";

		$this->loadedFromTemplate('icalevent.list_row', $row, 0);
		echo "</li>\n";
	}
	echo "</ul></td></tr>\n";
}

for ($h=0;$h<24;$h++){
	if (count($data['hours'][$h]['events'])>0){
		$hasevents = true;
		$start_time = JEVHelper::getTime($data['hours'][$h]['hour_start']);

		echo '<tr><td class="ev_td_right" colspan="3"><ul class="ev_ul">' . "\n";
		foreach ($data['hours'][$h]['events'] as $row) {
			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";

			$this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>\n";
		}
		echo "</ul></td></tr>\n";
	}
}
if (!$hasevents) {
		echo '<tr><td class="ev_td_right" colspan="3"><ul class="ev_ul" style="list-style: none;">' . "\n";
		echo "<li class='ev_td_li' style='border:0px;'>\n";
		echo JText::_('JEV_NO_EVENTS') ;
		echo "</li>\n";
		echo "</ul></td></tr>\n";
}
?>
</table>