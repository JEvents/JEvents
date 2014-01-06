<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();


$cfg = JEVConfig::getInstance();
$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/".$viewname."/assets";
$viewimages = $viewpath . "/images";

$view =  $this->getViewName();

$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

?>
<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme;?>'>
	<div class="jev_toprow">
	    <div class="jev_header">
		  <h2><?php echo JText::_( 'WEEKLY_VIEW' );?></h2>
		  <div class="today" ><?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?></div>
		</div>
	    <div class="jev_header2">
			<div class="jev_topleft jev_topleft_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>" ></div>
			<div class="previousmonth" >
		      	<?php if($precedingWeek) echo "<a href='".$precedingWeek."' title='".JText::_("PRECEEDING_Week")."' >".JText::_("PRECEEDING_Week")."</a>";?>
			</div>
			<div class="currentmonth">
				<?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?>
			</div>
			<div class="nextmonth">
		      	<?php if ($followingWeek) echo "<a href='".$followingWeek."' title='".JText::_("FOLLOWING_Week")."' >". JText::_("FOLLOWING_Week");?></a>
			</div>
			
		</div>
	</div>
    <div class="jev_clear" ></div>
		
<?php
for( $d = 0; $d < 7; $d++ ){
	$num_events	= count($data['days'][$d]['rows']);
	if ($num_events==0) continue;

	$day_link = '<a class="ev_link_weekday" href="' . $data['days'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
	. JEventsHTML::getDateFormat( $data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 2 ).'</a>'."\n";
	?>
	<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
	    <?php echo $day_link;?>
	</div>
	<div class="jev_listrow">
	<?php

	if ($num_events>0) {
		echo "<ul class='ev_ul'>\n";

		for( $r = 0; $r < $num_events; $r++ ){
			$row = $data['days'][$d]['rows'][$r];

			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";
			if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
				$this->viewEventRowNew ( $row);
				echo "&nbsp;::&nbsp;";
				$this->viewEventCatRowNew($row);
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	echo '</div>' . "\n";
} // end for days
?>
</div>