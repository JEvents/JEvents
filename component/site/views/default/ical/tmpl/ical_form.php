<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();


$view =  $this->getViewName();

echo "<div id='cal_title'>".JText::_('JEV_ICAL_EXPORT')."</div>\n";

?>

<form name="ical" method="post">
<?php
$categories = JEV_CommonFunctions::getCategoryData();

echo "<p>".JText::_('JEV_EVENT_CHOOSE_CATEG')."<br>\n";
foreach ($categories AS $c) {
	$cb="<input name=\"categories[]\" value=\"".$c->id."\" type=\"checkbox\"";
	if (isset($_POST['categories']) && in_array($c->id,JRequest::getVar('categories','','POST'))) {$cb=$cb." CHECKED";}
	$cb= $cb."><span style=\"background:".$c->color."\">&nbsp;&nbsp;&nbsp;&nbsp;</span> ".$c->title."<br>\n";
	echo $cb;
}

echo "</p><p>".JText::_('JEV_REP_YEAR')."<br>\n";
//consturc years array, easy to add own kind of selection
$year = array(date('Y'),date('Y')+1);

foreach ($year AS $y) {
	$yt="<input name=\"years[]\" type=\"checkbox\" value=\"".$y."\"";
	if (isset($_POST['years']) && in_array($y,JRequest::getVar('years','','POST'))) {$yt=$yt." CHECKED";}
	$yt= $yt.">".$y."<br>\n";
	echo $yt;
}

?>
<label><input name="years[]" type="checkbox" value="1"/><?php echo JText::_("JEV PRESERVE HTML FORMATTING");?></label><br/>

<input type="submit" name="submit" value="<?php echo JText::_('JEV SELECT');?>" />

</form>

<?php 
if (isset($_POST['submit'])) {
	//print_r(JRequest::getVar('categories','','POST'));
	//echo "\n".explode("|",JRequest::getVar('categories','','POST'));
	//echo "\n".JEVHelper::forceIntegerArray(explode("|",JRequest::getVar('categories','','POST')),true);
	$catids  = str_replace(",","|",JEVHelper::forceIntegerArray(JRequest::getVar('categories','','POST'),true));
	$years  = str_replace(",","|",JEVHelper::forceIntegerArray(JRequest::getVar('years','','POST'),true));
	//echo "<br>catid ".$catids;
	//echo "<br>years ".$years;
	$URL = "<a href=\"".JURI::root()."index.php?option=com_jevents&task=ical.ical&format=ical";
	if ($catids != 0) {$URL=$URL."&catids=".$catids;}
	if ($years != 0) {$URL=$URL."&years=".$years;}
	$URL=$URL."\">".JText::_('JEV_REP_ICAL_OWN')."</a>";

	echo "<p>".$URL."</p>";
}

?>