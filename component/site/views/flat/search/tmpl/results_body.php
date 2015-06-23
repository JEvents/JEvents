<?php 
defined('_JEXEC') or die('Restricted access');

$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
$useRegX = intval($params->get("regexsearch",0));
$this->data = $data = $this->datamodel->getKeywordData($this->keyword, $this->limit, $this->limitstart, $useRegX);

$Itemid = JEVHelper::getItemid();

$searchisValid =true;

$chdate	= '';
echo '<fieldset><legend class="ev_fieldset">' . JText::_('JEV_SEARCHRESULTS'). '&nbsp;:&nbsp;</legend><br />'	. "\n";
?>
<div id='jev_maincal' class='jev_listview'>
	<div class="jev_toprow jev_toprowcat">
	    <div class="jev_header jev_headercat">
		  <h2><?php echo JText::_("JEV_SEARCHRESULTS");?></h2>
		</div>
	</div>
    <div class="jev_clear" ></div>

	<div class="jev_listrow">
	   <div class='jev_catdesc'><?php echo $this->keyword  ;?></div>
	</div>
	<?php

	if( $data['num_events'] > 0 ){
		for( $r = 0; $r < $data['num_events']; $r++ ){
			$row = $data['rows'][$r];

			$event_day_month_year 	= $row->dup().$row->mup().$row->yup();

			if(( $event_day_month_year <> $chdate ) && $chdate <> '' ){
				echo '</ul></div>' . "\n";
			}

			if( $event_day_month_year <> $chdate ){
				$date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
				echo '<div class="jev_listrow"><ul class="ev_ul">' . "\n";
			}

			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";
			$this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>\n";

			$chdate = $event_day_month_year;
		}
		echo "</ul></div>\n";
	} else {
		echo '<div class="jev_listrow  jev_noresults">';
		if( $searchisValid ){
			echo JText::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $this->keyword . '</b>';
		}else{
			echo '<b>' . $this->keyword . '</b>';
			$this->keyword = '';
		}
		echo '</div>' . "\n";

	}
?>
</div>
<div class="jev_pagination">
	<form action="<?php echo JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=search.results&Itemid=".$this->Itemid);?>" method="post" style="font-size:1;">
		<input type="text" name="keyword" size="30" maxlength="50" class="inputbox" value="<?php echo $this->keyword;?>" />
		<input type="hidden" name="pop" value="<?php echo JRequest::getInt("pop",0);?>" />
		<?php if (JRequest::getString("tmpl","")=="component"){
			echo '<input type="hidden" name="tmpl" value="component" />';
		} ?>
		<label for="showpast"><?php echo JText::_("JEV_SHOW_PAST");?></label>
		<input type="checkbox" id="showpast" name="showpast" value="1" <?php echo JRequest::getInt('showpast',0)?'checked="checked"':''?> />
		<input class="button" type="submit" name="push" value="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" />
		<br />
		<br />
	</form>
</div>
<?php
// Create the pagination object
if ($data["total"]>$data["limit"]){
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
