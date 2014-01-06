<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/".$viewname."/assets";
$viewimages = $viewpath . "/images";

?>

<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme;?>'>
	<div class="jev_toprow">
	    <div class="jev_header">
		  <h2><?php echo JText::_( 'MONTHLY_VIEW' );?></h2>
		  <div class="today" ><?php echo $this->data['fieldsetText']; ?></div>
		</div>
	    <div class="jev_header2">
			<div class="jev_topleft jev_topleft_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>" ></div>
			
			<div class="previousmonth" >
				<span>
			  	<?php if ($precedingMonth) echo "<a href='".$precedingMonth["link"]."' title='".$precedingMonth["link"]."' >" . $precedingMonth['name']."</a>";?>
			  	</span>
			</div>
			<div class="currentmonth">
				<span>
				<?php echo $this->data['fieldsetText']; ?>
				</span>
			</div>
			<div class="nextmonth">
				<span>
			  	<?php if ($followingMonth) echo "<a href='".$followingMonth["link"]."' title='".$followingMonth["link"]."' >" . $followingMonth['name']."</a>";?>
			  	</span>
			</div>
			
		</div>
	</div>
    <div class="jev_clear" ></div>
		
<?php
// version with separate day icons per event
$datacount = count($this->data["dates"]);
for( $d = 0; $d < $datacount; $d++ ){
	if ($this->data["dates"][$d]["monthType"]!="current"){
		continue;		
	}
	$num_events	= count($this->data['dates'][$d]['events']);
	if ($num_events==0) continue;

	$day_link = '<a class="ev_link_weekday" href="' . $this->data['dates'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
	. JEventsHTML::getDateFormat( $this->data['dates'][$d]['year'], $this->data['dates'][$d]['month'], $this->data['dates'][$d]['d'], 2 ).'</a>'."\n";
	?>
	<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
	    <?php echo $day_link;?>
	</div>
	<div class="jev_listrow">
	<?php
	echo "<ul class='ev_ul'>\n";
	for( $r = 0; $r < $num_events; $r++ ){
		$row = $this->data['dates'][$d]['events'][$r];
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
	echo '</div>' . "\n";
} // end for days 

?>
	</div>