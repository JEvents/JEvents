<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$data = $this->datamodel->getCatData( $this->catids,$cfg->get('com_showrepeats',0), $this->limit, $this->limitstart);
$this->data = $data;

$Itemid = JEVHelper::getItemid();
?>
<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme;?>'>
	<div class="jev_toprow jev_toprowcat">
	    <div class="jev_header jev_headercat">
		  <h2><?php echo JText::_( 'CATEGORY_VIEW' );?></h2>
		  <div class="today" > <?php $this->viewNavCatText( $this->catids, JEV_COM_COMPONENT, 'cat.listevents', $this->Itemid );?></div>
		</div>
	</div>
    <div class="jev_clear" ></div>

	<div class="jev_listrow">
<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
<?php echo $data['catname'];
echo "</div>";
if (strlen($data['catdesc'])>0){
	echo "<div class='jev_catdesc'>".$data['catdesc']."</div>";
}
echo "</div>";
$num_events = count($data['rows']);
$chdate ="";
if( $num_events > 0 ){

	for( $r = 0; $r < $num_events; $r++ ){
		$row = $data['rows'][$r];

		$event_day_month_year 	= $row->dup() . $row->mup() . $row->yup();

		if(( $event_day_month_year <> $chdate ) && $chdate <> '' ){
			echo '</ul></div>' . "\n";
		}

		if( $event_day_month_year <> $chdate ){
			$date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
			echo '<div class="jev_listrow"><ul class="ev_ul">' . "\n";
		}

		$listyle = 'style="border-color:'.$row->bgcolor().';"';
		echo "<li class='ev_td_li' $listyle>\n";
		if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
			$this->viewEventRowNew ( $row,'view_detail',JEV_COM_COMPONENT, $Itemid);
		}
		echo "</li>\n";

		$chdate = $event_day_month_year;
	}
	echo "</ul></div>\n";
} else {
	echo '<div class="jev_listrow jev_noresults">';

	if( count($this->catids)==0 || $data['catname']==""){
		echo JText::_('JEV_EVENT_CHOOSE_CATEG') . '';
	} else {
		echo JText::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $data['catname']. '</b>';
	}
	echo '</div>' . "\n";
}

?>
<div class="jev_clear" ></div>
</div>
<?php
// Create the pagination object
if ($data["total"]>$data["limit"]){
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
