<?php 
defined('_JEXEC') or die('Restricted access');
/*
if (JRequest::getInt("category_fv",-1)==-1) {	
	// get the cat name from the database
	$db	=& JFactory::getDBO();
	$user = JFactory::getUser();
	$catsql = 'SELECT c.id FROM #__categories AS c' .
	' WHERE c.access  ' . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user)) .
	' AND c.'.(JVersion::isCompatible("1.6.0")?'extension':'section').' = '.$db->Quote(JEV_COM_COMPONENT).
	" ORDER BY c.level asc, c.title LIMIT 1"	;
	$db->setQuery($catsql);
	$catid = $db->loadResult();

	JRequest::setVar("category_fv",$catid);
	$this->catids = array($catid);
}
 */
$cfg	 = & JEVConfig::getInstance();
$data = $this->datamodel->getCatData( $this->catids,$cfg->get('com_showrepeats',0), $this->limit, $this->limitstart);
$this->data = $data;
$Itemid = JEVHelper::getItemid();

?>
<div class="jev_catselect" ><?php echo $data['catname']; $this->viewNavCatText( $this->catids, JEV_COM_COMPONENT, 'cat.listevents', $this->Itemid );?></div><?php
/*
if (strlen($data['catdesc'])>0){
	$document = &JFactory::getDocument();
	$catid=0;
	if (count($data['catids'])>0 && $data['catids'][0]>0){
		$catid = $data['catids'][0];
	}
	else if (count($data['rows'])>9999){
		$catid = $data['rows'][0]->catid();
	}
	else {
		$filters = jevFilterProcessing::getInstance(array("category"));
		$filter = $filters->filters[0];
		$cats = explode(",",$filter->accessibleCategories);
		if (count ($cats)>1){
			$catid = intval($cats[1]);
		}
	}
	if ($catid>0){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__categories where id=".$catid);
		$metadata= $db->loadObject();
		if(!is_null($metadata) && isset($metadata->metadesc) && $metadata->metadesc!=""){
		$document->setDescription($metadata->metadesc);
		}
		if(!is_null($metadata) && isset($metadata->metakey) && $metadata->metakey!=""){
		$document->setMetaData('keywords', $metadata->metakey);
		}
	}
	echo "<div class='jev_catdesc'>".$data['catdesc']."</div>";
}
*/
if (strlen($data['catdesc'])>0){
	echo "<div class='jev_catdesc'>".$data['catdesc']."</div>";
}
?>
<table align="center" width="90%" cellspacing="0" cellpadding="5" class="ev_table">
<?php
$num_events = count($data['rows']);
$chdate ="";
if( $num_events > 0 ){
	for( $r = 0; $r < $num_events; $r++ ){
		$row = $data['rows'][$r];

		$event_day_month_year 	= $row->dup() . $row->mup() . $row->yup();

		if(( $event_day_month_year <> $chdate ) && $chdate <> '' ){
			echo '</ul></td></tr>' . "\n";
		}

		if( $event_day_month_year <> $chdate ){
			$date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
			echo '<tr><td class="ev_td_left">'.$date.'</td>' . "\n";
			echo '<td align="left" valign="top" class="ev_td_right"><ul class="ev_ul">' . "\n";
		}

		$listyle = 'style="border-color:'.$row->bgcolor().';"';
		echo "<li class='ev_td_li' $listyle>\n";
		if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
			$this->viewEventRowNew ( $row,'view_detail',JEV_COM_COMPONENT, $Itemid);
		}
		echo "</li>\n";

		$chdate = $event_day_month_year;
	}
	echo "</ul></td>\n";
} else {
	echo '<tr>';
	echo '<td align="left" valign="top" class="ev_td_right">' . "\n";

	if( count($this->catids)==0 || $data['catname']==""){
		echo JText::_('JEV_EVENT_CHOOSE_CATEG') . '</td>';
	} else {
		echo JText::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $data['catname']. '</b></td>';
	}
}
?>
</tr></table><br />
<br /><br />
<?php

// Create the pagination object
if ($data["total"]>$data["limit"]){
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
