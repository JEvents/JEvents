<?php
$datacount = count($this->data["dates"]);
for( $d = 0; $d < $datacount; $d++ ){
   if ($this->data["dates"][$d]["monthType"]!="current"){
      continue;      
   }
$num_events = count($data['rows']);
$chdate ="";
if( $num_events > 0 ){


$day_link = '<a class="ev_link_weekday" href="' . $this->data['dates'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">' . JEventsHTML::getDateFormat( $this->data['dates'][$d]['year'], $this->data['dates'][$d]['month'], $this->data['dates'][$d]['d'], 2 ).'</a>'."\n";
   ?>
   <div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
       <?php echo $day_link;?>
   </div>





   <div class="jev_listrow">
   <?php
   echo "<ul class='ev_ul'>\n";






   for( $r = 0; $r < $num_events; $r++ ){
      $row = $data['rows'][$r];

      $event_day_month_year    = $row->dup() . $row->mup() . $row->yup();

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
      
      
} // end for days 

?>
   </div>      
      
      
      
      ?>
   
   <?php 

}
?>
</div>
<div class="jev_clear" ></div>
