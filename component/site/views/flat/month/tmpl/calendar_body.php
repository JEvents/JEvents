<?php 
defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;

$cfg	 = JEVConfig::getInstance();

if ($cfg->get("tooltiptype",'joomla')=='overlib'){
	JEVHelper::loadOverlib();
}

$view =  $this->getViewName();
echo $this->loadTemplate('cell' );
$eventCellClass = "EventCalendarCell_".$view;

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);

    ?>
	<div class="jev_toprow jev_monthv">
	    <div class="jev_header2">
			<div class="previousmonth" >
		      	<?php echo "<a href='".$precedingMonth["link"]."' title='".$precedingMonth['name']."' style='text-decoration:none;'>".$precedingMonth['name']."</a>";?>
			</div>
			<div class="currentmonth">
				<?php echo $this->data['fieldsetText']; ?>
			</div>
			<div class="nextmonth">
		      	<?php echo "<a href='".$followingMonth["link"]."' title='".$followingMonth['name']."' style='text-decoration:none;'>".$followingMonth['name']."</a>";?>
			</div>
			
		</div>
	</div>

            <table border="0" cellpadding="0" class="cal_top_day_names">
            <tr valign="top">
                <?php 
                foreach ($this->data["daynames"] as $dayname) { 
					$cleaned_day = strip_tags($dayname, '');?>
					<td class="cal_daysnames">
						<span class="<?php echo strtolower($cleaned_day); ?>">
                            <?php echo JString::substr($cleaned_day, 0, 3);?>
                        </span>
					</td>
                    <?php
                } ?>
            </tr>
            </table>
        <table border="0" cellspacing="1" cellpadding="0" class="cal_table">
            <?php
            $datacount = count($this->data["dates"]);
            $dn=0;
            for ($w=0;$w<6 && $dn<$datacount;$w++){
            ?>
			<tr class="cal_cell_rows">
                <?php
                for ($d=0;$d<7 && $dn<$datacount;$d++){
                	$currentDay = $this->data["dates"][$dn];
                	switch ($currentDay["monthType"]){
                		case "prior":
                		case "following":
                		?>
                    <td width="14%" class="cal_daysoutofmonth" valign="top">
                        <?php echo $currentDay["d"]; ?>
                    </td>
                    	<?php
                    	break;
                		case "current":
                			$cellclass = $currentDay["today"]?'class="cal_today"':(count($currentDay["events"])>0?'class="cal_dayshasevents"':'class="cal_daysnoevents"');
						?>
                    <td <?php echo $cellclass;?>>
                     <?php   $this->_datecellAddEvent($this->year, $this->month, $currentDay["d"]);?>
                    	<a class="cal_daylink" href="<?php echo $currentDay["link"]; ?>" title="<?php echo JText::_('JEV_CLICK_TOSWITCH_DAY'); ?>">
			    <span class="calview"><?php echo $currentDay['d']; ?></span>
			    <span class="listview">				
				<?php 
					$format = JText::_("DATE_FORMAT_0");
					if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
						$format = str_replace("%d", "%e",$format);
					}
					echo JevDate::strftime($format, $currentDay["cellDate"]);

				?>
			    </span>
			</a>
                        <?php

                        if (count($currentDay["events"])>0){
                        	foreach ($currentDay["events"] as $key=>$val){
                        		if( $currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay',5)) {
                        			echo '<div class="event_div_1">';
                        		} else {
                        			// float small icons left
                        			echo '<div class="event_div_2">';
                        		}
                        		echo "\n";
                        		$ecc = new $eventCellClass($val,$this->datamodel, $this);
                        		echo $ecc->calendarCell($currentDay,$this->year,$this->month,$key);
                        		echo '</div>' . "\n";
                        		$currentDay['countDisplay']++;
                        	}
                        }
                        echo "</td>\n";
                        break;
                	}
                	$dn++;
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
            $this->eventsLegend();

