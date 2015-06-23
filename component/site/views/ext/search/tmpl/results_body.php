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
<table align="center" width="90%" cellspacing="0" cellpadding="5" class="ev_table">
   <tr valign="top">
       <td colspan="2"  align="center" class="cal_td_daysnames">
            <?php echo $this->keyword  ;?>
        </td>
    </tr>
	<?php

	if( $data['num_events'] > 0 ){
		echo '<tr>';
		for( $r = 0; $r < $data['num_events']; $r++ ){
			$row = $data['rows'][$r];

			$event_day_month_year 	= $row->dup().$row->mup().$row->yup();

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
			$this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>\n";

			$chdate = $event_day_month_year;
		}
		echo "</ul></td>\n";
	} else {
		echo "<tr>";
		echo "<td align='left' valign='top' class='ev_td_right jev_noresults'>\n";
		// new by mic
		if( $searchisValid ){
			echo JText::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $this->keyword . '</b>';
		}else{
			echo '<b>' . $this->keyword . '</b>';
			$this->keyword = '';
		}
		echo "</td >";
	}
?>
	</tr>
</table>
<br />
</fieldset>
<br />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td align="center" width="100%">
			<form action="<?php echo JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=search.results&Itemid=".$this->Itemid);?>" method="post" style="font-size:1;">
				<input type="text" name="keyword" size="30" maxlength="50" class="inputbox" value="<?php echo $this->keyword;?>" />
				<input type="hidden" name="pop" value="<?php echo JRequest::getInt("pop",0);?>" />
				<?php if (JRequest::getString("tmpl","")=="component"){
					echo '<input type="hidden" name="tmpl" value="component" />';
				} ?>
				<label for="showpast"><?php echo JText::_("JEV_SHOW_PAST");?></label>
				<input type="checkbox" id="showpast" name="showpast" value="1" <?php echo JRequest::getInt('showpast',0)?'checked="checked"':''?> />
				<br />
				<input class="button" type="submit" name="push" value="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" />
			</form>
		</td>
	</tr>
</table>

<br />
<?php
// Create the pagination object
if ($data["total"]>$data["limit"]){
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
