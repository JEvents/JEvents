<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
if (!JEVHelper::isEventCreator()) {
    return;
}
if ($params->get("displaytitle", 0)) {
    ?><div id='CREATE_fvs3' style='line-height: 15pt;'> <?php echo $createtitle ?> </div>
    <?php
}
if ($params->get("displaydesc", 0)) {
    ?> <div id='CREATE_fvs4'> <?php echo $createdesc ?> </div>
<?php } ?>
<input type='hidden' name='NAME' value='1'/>
<?php
$minyear = $params->get("com_earliestyear", 1970);
$maxyear = $params->get("com_latestyear", 2150);
$document = & JFactory::getDocument();
$calendar = (JVersion::isCompatible("3.0")) ? 'calendar14.js' : 'calendar12.js';
$document->addScript(Juri::base() . "/components/com_jevents/assets/js/" . $calendar);
$document->addStyleSheet(Juri::base() . "/components/com_jevents/assets/css/dashboard.css");
$document->addScriptDeclaration('
			var dashfilterCREATE=false;
			window.addEvent(\'domready\', function() {
				if (dashfilterCREATE) return;
				dashfilterCREATE=true;
				new NewCalendar({ CREATE_fvs1 : "Y-m-d"},{
					direction:0, 
					readonly:true,
					classes: ["dashboard"],
					draggable:true,
					navigation:2,
					tweak:{x:0,y:-75},
					offset:1,
					range:{min:' . $minyear . ',max:' . $maxyear . '},
					months:["' . JText::_("JEV_JANUARY") . '",
					"' . JText::_("JEV_FEBRUARY") . '",
					"' . JText::_("JEV_MARCH") . '",
					"' . JText::_("JEV_APRIL") . '",
					"' . JText::_("JEV_MAY") . '",
					"' . JText::_("JEV_JUNE") . '",
					"' . JText::_("JEV_JULY") . '",
					"' . JText::_("JEV_AUGUST") . '",
					"' . JText::_("JEV_SEPTEMBER") . '",
					"' . JText::_("JEV_OCTOBER") . '",
					"' . JText::_("JEV_NOVEMBER") . '",
					"' . JText::_("JEV_DECEMBER") . '"
					],
					days :["' . JText::_("JEV_SUNDAY") . '",
					"' . JText::_("JEV_MONDAY") . '",
					"' . JText::_("JEV_TUESDAY") . '",
					"' . JText::_("JEV_WEDNESDAY") . '",
					"' . JText::_("JEV_THURSDAY") . '",
					"' . JText::_("JEV_FRIDAY") . '",
					"' . JText::_("JEV_SATURDAY") . '"
					] 
                                      ' . $createauto . '  ,  onHideComplete : function () { createevent()},					
				});
			});
			');
$createvalues = "";
$dataModel = new JEventsDataModel("JEventsAdminDBModel");
$nativeCals = $dataModel->queryModel->getNativeIcalendars();
$jevuser = & JEVHelper::getAuthorisedUser();
if ($jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all") {
    $cals = array_keys($nativeCals);
    $allowedcals = explode("|", $jevuser->calendars);
    foreach ($cals as $calid) {
        if (!in_array($calid, $allowedcals))
            unset($nativeCals[$calid]);
    }
}
$cal = current($nativeCals);
$icsid = $cal->ics_id;
?>
<input type="text" name="CREATE_fvs1" id="CREATE_fvs1" value="<?php echo $createvalues ?>" readonly="readonly" maxlength="10" size="12" style='margin-bottom:5px;'/>
<input type='hidden' name='CREATE_fvs2' value='1'/>
<?php if ($createauto) { ?>
    <br/><input type="submit" name="Create_Submit" class="button" value="Submit" onclick="createevent();" />
<?php } ?>
<form action = "<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.save"); ?>" method = "post" name = "adminFormcreate" enctype = "multipart/form-data" id = "adminFormcreate" style="height:20px">
    <input type="hidden" name="title" id = "title" value =" <?php echo $createtitle ?>" >
    <input type="hidden" name="catid" id = "catid" value="<?php echo $createcat ?>">
    <input type="hidden" name="jevcontent" id = "jevcontent" value="<?php echo $createdesc ?>">
    <input type="hidden" name="allDayEvent" id = "allDayEvent"  value="on">
    <input type="hidden" name="publish_up2" id = "publish_up2" value = "">
    <input type="hidden" name="publish_down2" id = "publish_down2" value = "">
    <input type="hidden" name="ics_id" value=" <?php echo $icsid ?>">
    <input type="hidden" name="extra_info" value="">
    <input type="hidden" name="freq" value="none">
    <input type="hidden" name="jevtype" value="icaldb">
    <input type="hidden" name="updaterepeats" value="0">
    <input type="hidden" name="option" value="com_jevents">
    <input type="hidden" name="rp_id" value="0"> 
    <input type="hidden" name="year" value="2013"> 
    <input type="hidden" name="month" value="08"> 
    <input type="hidden" name="day" value="14"> 
    <input type="hidden" name="state" id="state" value="1">
    <input type="hidden" name="evid" id="evid" value="0">
    <input type="hidden" name="valid_dates" id="valid_dates" value="1">
    <script type="text/javascript">
        createevent = function () {
    
            if(document.getElementById('CREATE_fvs1').value){
                document.getElementById('publish_up2').value=document.getElementById('CREATE_fvs1').value;
                document.getElementById('publish_down2').value=document.getElementById('CREATE_fvs1').value;
                document.adminFormcreate.submit();
            }
            else{alert("<?php echo JText::_("MOD_JEVENTS_CREATE_ENTER_DATE") ?>");}	
        }
    </script>
</form>
