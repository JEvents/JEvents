<?php 
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;


$cfg	 = JEVConfig::getInstance();

if ($cfg->get("tooltiptype",'joomla')=='overlib'){
	JEVHelper::loadOverlib();
}

$view = $this->getViewName();
echo $this->loadTemplate('cell' );
$eventCellClass = "EventCalendarCell_".$view;

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);

?>

<table width="100%" align="center" cellpadding="0" cellspacing="0" class="cal_table">
    <tr valign="top" style="height:25px!important;line-height:25px;font-weight:bold;">
    	<td width="2%" rowspan="2" />
        <td colspan="2" class="cal_td_month" style="text-align:center;">                
           <?php echo "<a href='".$precedingMonth["link"]."' title='".$precedingMonth['name']."' style='text-decoration:none;'>".$precedingMonth['name']."</a>";?>
        </td>
        <td colspan="3" class="cal_td_currentmonth" style="text-align:center;"><?php echo $this->data['fieldsetText']; ?></td>
        <td colspan="2" class="cal_td_month" style="text-align:center;">                
           <?php echo "<a href='".$followingMonth["link"]."' title='".$followingMonth['name']."' style='text-decoration:none;'>".$followingMonth['name']."</a>";?>
        </td>
    </tr>
    <tr valign="top">
         <?php foreach ($this->data["daynames"] as $dayname) { ?>
            <td width="14%" align="center" style="height:25px!important;line-height:25px;font-weight:bold;">
                <?php 
                echo $dayname;?>
            </td>
            <?php
        } ?>
    </tr>            
    <?php
    $datacount = count($this->data["dates"]);
    $dn=0;
    foreach ($this->data['weeks'] AS $wkn => $week) {
    ?>           
	<tr valign="top" style="height:80px;">				
        <?php
        echo "<td width='2%' class='cal_td_weeklink'>";
		echo "<a href='".$week."'>$wkn</a></td>\n";

		for ($d=0;$d<7 && $dn<$datacount;$d++){

	    $currentDay = $this->data["dates"][$dn];
        	switch ($currentDay["monthType"]){
        		case "prior":
        		case "following":
        		?>
            <td width="14%" class="cal_td_daysoutofmonth" valign="middle">
                <?php echo JEVHelper::getMonthName($currentDay["month"]); ?>
            </td>
            	<?php
            	break;
        		case "current":
        			$cellclass = $currentDay["today"]?'class="cal_td_today"':'class="cal_td_daysnoevents"';
        			// stating the height here is needed for konqueror and safari
				?>
            <td <?php echo $cellclass;?> width="14%" valign="top" style="height:80px;">
                <?php   $this->_datecellAddEvent($this->year, $this->month, $currentDay["d"]);?>
            	<a class="cal_daylink" href="<?php echo $currentDay["link"]; ?>" title="<?php echo Text::_('JEV_CLICK_TOSWITCH_DAY'); ?>"><?php echo $currentDay['d']; ?></a>
                <?php
                
                if (count($currentDay["events"])>0){
                	foreach ($currentDay["events"] as $key=>$val){
						if( $currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay',5)) {
                			echo '<div style="width:100%; border:0;padding:2px;">' . "\n";
						} else {
							echo '<div style="float:left; border:0;padding:0px;">' . "\n";
						}
                		$ecc = new $eventCellClass($val, $this->datamodel,$this);
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
