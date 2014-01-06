<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

// Note that using a $limit value of -1 the limit is ignored in the query
$this->data = $data = $this->datamodel->getYearData($this->year,$this->limit, $this->limitstart);

// previous and following month names and links
$followingYear = $this->getFollowingYear($this->year, $this->month, $this->day);
$precedingYear = $this->getPrecedingYear($this->year, $this->month, $this->day);

?>
<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme;?>'>
	<div class="jev_toprow">
	    <div class="jev_header">
		  <h2><?php echo JText::_( 'YEARLY_VIEW' );?></h2>
		  <div class="today" > <?php echo $data["year"] ;?></div>
		</div>
	    <div class="jev_header2">
			<div class="jev_topleft jev_topleft_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>" ></div>
			<div class="previousmonth" >
		      	<?php if ($precedingYear) {
					echo "<a href='".$precedingYear."' title='".JText::_("PRECEEDING_Year")."' >". JText::_("PRECEEDING_Year")."</a>";
			}
			?>
			</div>
			<div class="currentmonth">
				<?php echo $data["year"] ;?>
			</div>
			<div class="nextmonth">
		      	<?php if ($followingYear) {
					echo "<a href='".$followingYear."' title='".JText::_("FOLLOWING_Year")."' >". JText::_("FOLLOWING_Year")."</a>";
				}
			?>
			</div>
			
		</div>
	</div>
    <div class="jev_clear" ></div>

<?php
for($month = 1; $month <= 12; $month++) {
	$num_events = count($data["months"][$month]["rows"]);
	if ($num_events>0){
		?>
		<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
	    <?php echo JEventsHTML::getDateFormat($this->year,$month,'',3);?>
		</div>
		<div class="jev_listrow">
		<?php
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
		echo '</div>';
	}

}
?>
</div>
<div class="jev_clear" ></div>
<?php
$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
