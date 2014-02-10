<?php
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDataForAdmin( $this->creator_id, $this->limit, $this->limitstart );

$frontendPublish = intval($cfg->get('com_frontendPublish', 0)) > 0;

$num_events = count( $data['rows'] );
$chdate 	= '';

$myItemid = JEVHelper::getAdminItemid();
$form_link = JRoute::_(
'index.php?option=' . JEV_COM_COMPONENT
. '&task=admin.listevents'
. "&Itemid=".$myItemid
,false);

?>
<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme;?>'>
	<div class="jev_toprow"  style="height:85px;">
	    <div class="jev_header">
		  <h2><?php echo JText::_("JEV_ADMINPANEL");?></h2>
 	       <div class="today" ><?php echo JEventsHTML::getDateFormat( $this->year, $this->month, $this->day, 0) ;?></div>
		</div>
		<div class="jev_header2" style="height:55px;">
			<form action="<?php echo $form_link;?>"  method="post">
			<?php
			$filters = jevFilterProcessing::getInstance(array("startdate"));
			$filterHTML = $filters->getFilterHTML();
			foreach ($filterHTML as $filter){
				echo "<div class='jev_adminfilter'>".$filter["title"]."<br/>".$filter["html"]."</div>";
			}
			?>
			</form>
			 <div class="jev_clear" ></div>
		</div>
	</div>
	 <div class="jev_clear" ></div>
<?php

if( $num_events > 0 ){
	for( $r = 0; $r < $num_events; $r++ ) {
		$row = $data['rows'][$r];
		$event_month_year 	= $row->mup().$row->yup();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
   		if ($params->get("colourbar",0)) $listyle = 'style="border-color:'.$row->bgcolor().';"';
   		else $listyle = 'style="border:none"';

		if( $event_month_year <> $chdate && $chdate <> ""){
			?>
			</ul>
		</div>
	</div>
	<?php
		}
		if( $event_month_year <> $chdate ){
			?>
	<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
	    <?php	echo  JEventsHTML::getDateFormat( $row->yup(), $row->mup(), '', 3 ) ;		?>
	</div>
	<div class="jev_listrow">
		<div  class='jevright' <?php echo $listyle;?>>
			<ul>
			<?php
		}
		$this->viewEventRowAdmin($row);
		$chdate = $event_month_year;
	}
			?>
			</ul>
		</div>
	</div>
	<?php
} else {
	echo '<div class="jev_listrow">' ;
	echo JText::_('JEV_NO_EVENTS');
	echo '</div >' ;
}

// Create the pagination object
if ($data["total"]>$data["limit"]){
	echo '<div class="jev_listrow">';
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
	echo '</div >' ;
}
?>
	</div>
			