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
echo $this->loadTemplate('cell' );
$eventCellClass = "EventCalendarCell_".$view;

if ($cfg->get("tooltiptype",'joomla')=='overlib'){
	JEVHelper::loadOverlib();
}

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);

    ?>
<table class="maintable" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="tableh1" colspan="8">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr> 
					<td><h2><?php echo JText::_( 'MONTHLY_VIEW' );?></h2></td>
					<td class="today" align="right"><?php echo $this->data['fieldsetText']; ?></td>				
				</tr>
			</table>
	  </td>
	</tr>
		<tr>
<!-- BEGIN weeknumber_row -->
			<td rowspan="2" class="tablev1">&nbsp;&nbsp;</td>
<!-- END weeknumber_row -->
			<td colspan="2" class="previousmonth" align="center" height="22" nowrap="nowrap" valign="middle">&nbsp;
<!-- BEGIN previous_month_link_row -->
      	<?php if ($precedingMonth) { echo "<a href='".$precedingMonth["link"]."' title='".$precedingMonth['name']."' >"?>
      	<img src="<?php echo $viewimages;?>/mini_arrowleft.gif" alt="<?php echo $precedingMonth['name'];?>" align="middle" border="0" hspace="5"/>
      	<?php echo $precedingMonth['name']."</a>";}?>
      	

<!-- END previous_month_link_row -->
			</td>
			<td colspan="3" class="currentmonth" style="background-color: rgb(208, 230, 246);" align="center" height="22" nowrap="nowrap" valign="middle">
				<?php echo $this->data['fieldsetText']; ?>
			</td>
			<td colspan="2" class="nextmonth" align="center" height="22" nowrap="nowrap" valign="middle">
      	<?php if ($followingMonth) { echo "<a href='".$followingMonth["link"]."' title='".$followingMonth['name']."' >"?>
      	<?php echo $followingMonth['name'];?>
      	<img src="<?php echo $viewimages;?>/mini_arrowright.gif" alt="<?php echo $followingMonth['name'];?>" align="middle" border="0" hspace="5"/>
      	<?php echo "</a>"; }?>

			</td>
		</tr>
            <tr valign="top">
                <?php foreach ($this->data["daynames"] as $dayname) { ?>
                	<td class="weekdaytopclr" align="center" height="18" valign="middle" width="14%">
                        <?php 
                        echo $dayname;?>
					</td>
                    <?php
                } ?>
            </tr>
            <?php
            $datacount = count($this->data["dates"]);
            $dn=0;
            for ($w=0;$w<6 && $dn<$datacount;$w++){
            ?>
			<tr valign="top" style="height:80px;">				
                <td class='tablev1' align='center'>
                <?php
                list($week,$link) = each($this->data['weeks']);
                echo "<a href='".$link."'>$week</a></td>\n";
                for ($d=0;$d<7 && $dn<$datacount;$d++){
                	$currentDay = $this->data["dates"][$dn];
                	switch ($currentDay["monthType"]){
                		case "prior":
                		case "following":
                		?>
                    <td class="weekdayemptyclr" align="center" height="50" valign="middle">
                        <?php echo $currentDay["d"]; ?>
                    </td>
                    	<?php
                    	break;

                		case "current":
                			//Current month
                			$dayOfWeek = JevDate::strftime("%w",$currentDay["cellDate"]);
                			$style=($dayOfWeek==0)?"sundayemptyclr":"weekdayclr";
                			if ($currentDay['today']) $style="todayclr"
					?>
                    <td class="<?php echo $style;?>" width="14%" align="center" height="50" valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr class="caldaydigits">
						<td class="caldaydigits">&nbsp;
						<strong><a href="<?php echo $currentDay["link"]; ?>" title="<?php echo JText::_('JEV_CLICK_TOSWITCH_DAY'); ?>"><?php echo $currentDay['d']; ?></a></strong>
						
						</td>
						<td >
                       <?php   $this->_datecellAddEvent($this->year, $this->month, $currentDay["d"]);?>
						</td>
						</tr>
					</table>
                        <?php

                        if (count($currentDay["events"])>0){
                        	foreach ($currentDay["events"] as $key=>$val){
                        		$ecc = new $eventCellClass($val, $this->datamodel, $this);
                        		if( $currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay',5)){
                        			echo '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>' . "\n";
                        			echo $ecc->calendarCell($currentDay,$this->year,$this->month,$key);
                        			echo '</td></tr></table>' . "\n";
                        		} else {
                        			echo '<div style="padding:0;margin:0;width:10px;float:left">';
                        			echo $ecc->calendarCell($currentDay,$this->year,$this->month,$key);
                        			echo '</div>';
                        		}
                        		$currentDay['countDisplay']++;
                        	}
                        }
					?>
                    </td>
                    <?php
                    break;
                	}
                	$dn++;
                }
                ?>
            </tr>
            <?php
            }
         ?>   
         <tr>
	<td colspan="8" class="tablec">
<?php   		
$this->eventsLegend();
	?>
	</td>
</tr>
</table>
