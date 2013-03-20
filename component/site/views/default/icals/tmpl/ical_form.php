<?php
defined('_JEXEC') or die('Restricted access');

$cfg = & JEVConfig::getInstance();

$view = $this->getViewName();

$script = <<<SCRIPT
function clearIcalCategories(allcats){
	if(allcats.checked){
		document.getElements('input[name=categories[]]:checked').each (function(el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		$('othercats').style.display='none';
	}
	else {
		document.getElements('input[name=categories[]]').each (function(el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		$('othercats').style.display='block';		
	}
}
function clearAllIcalCategories(){
		document.getElements('input[name=categories[]]:checked').each (function(el){
			if (el.value==0){
				el.checked=false;
			}
		});
}
function clearIcalYears(allyears){
	if(allyears.checked){
		document.getElements('input[name=years[]]:checked').each (function(el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		$('otheryears').style.display='none';		
	}
	else {
		document.getElements('input[name=years[]]').each (function(el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		$('otheryears').style.display='block';				
	}
}
function clearAllIcalYears(){
		document.getElements('input[name=years[]]:checked').each (function(el){
			if (el.value==0){
				el.checked=false;
			}
		});
}

SCRIPT;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration($script);
	
$accessiblecats = explode(",", $this->datamodel->accessibleCategoryList());

echo "<h2 id='cal_title'>" . JText::_('JEV_ICAL_EXPORT') . "</h2>\n";

if (JRequest::getString("submit","")!="")
{

	$categories = JRequest::getVar('categories', array(0), 'POST');

	$cats = array();
	foreach ($categories AS $cid)
	{
		$cid = intval($cid);
		// Make sure the user is authorised to view this category and the menu item doesn't block it!
		if (!in_array($cid, $accessiblecats) && $cid = !0)
			continue;
		$cats[] = $cid;
	}
	if (count($cats) == 0)
		$cats[] = 0;


	//$years  = str_replace(",","|",JEVHelper::forceIntegerArray(JRequest::getVar('years','','POST'),true));
	//$cats = implode("|",$cats);
	$years = JEVHelper::forceIntegerArray(JRequest::getVar('years', array(0), 'POST'), true);
	$cats = implode(",", $cats);

	$link = JURI::root() . "index.php?option=com_jevents&task=icals.export&format=ical";
	if (count($cats) > 0)
	{
		$link .="&catids=" . $cats;
	}
	$link .="&years=" . $years;
	if (JRequest::getInt("icalformatted", 0))
	{
		$link .="&icf=1";
	}

	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	if ($params->get("constrained", 0))
	{
		$link .="&Itemid=" . JRequest::getInt("Itemid", 1);
	}

	$icalkey = $params->get("icalkey", "secret phrase");
	$publiclink = $link . "&k=" . md5($icalkey . $cats . $years);

	$user = JFactory::getUser();
	if ($user->id != 0)
	{
		$privatelink = $link . "&pk=" . md5($icalkey . $cats . $years . $user->password . $user->username . $user->id) . "&i=" . $user->id;
	}
	
	
	//Webcal Subscribe button:
	//Replace http with webcal
	
	$webcalurl_pub = str_replace('http:', 'webcal:', $publiclink);
	$webcalurl_priv = str_replace('http:', 'webcal:', $privatelink);
	
	echo "<h3>" . JText::_("JEV_ICAL_GENERATED") . "</h3>";
	if ($params->get("show_webcal_url", 0) == 1){
		echo "<p class='ical_form_button'><a href='$webcalurl_pub'>" . JText::_('JEV_REP_ICAL_PUBLIC_WEBCAL') . "</a></p>";
	}
	if ($params->get("show_ical_download", 1) == 1){
		echo "<p class='ical_form_button'><a href='$publiclink'>" . JText::_('JEV_REP_ICAL_PUBLIC') . "</a></p>";
	}
	if ($user->id != 0)
	{
		if ($params->get("show_ical_download", 1) == 1){
			echo "<p class='ical_form_button'><a href='$privatelink'>" . JText::_('JEV_REP_ICAL_PRIVATE') . "</a></p>";
		}
		if ($params->get("show_webcal_url", 0) == 1){
			echo "<p class='ical_form_button'><a href='$webcalurl_priv'>" . JText::_('JEV_REP_ICAL_PRIVATE_WEBCAL') . "</a></p>";
		}
	}

	if ($cfg->get("outlook2003icalexport", 0))
	{
		echo "<p class='ical_form_button'>" . JText::_('JEV_ICAL_OUTLOOK_SPECIFIC') . "</p>";
		echo "<p class='ical_form_button'><a href='$publiclink&outlook2003=1'>" . JText::_('JEV_REP_ICAL_PUBLIC') . "</a></p>";
		if ($user->id != 0)
		{
			echo "<p class='ical_form_button'><a href='$privatelink&outlook2003='>" . JText::_('JEV_REP_ICAL_PRIVATE') . "</a></p>";
		}
	}
	
	//If non are enabled we don't want to have user thinking the script is buggy as nothing is produced. 
	if ($cfg->get("outlook2003icalexport") == 0 && $cfg->get("show_ical_download") == 0 && $cfg->get("show_webcal_url") == 0) {
		echo "<div style='margin:15px;'>" . JText::_("JEV_ICAL_ALL_DISABLED") . "</div>";
	}
}
if ($cfg->get("outlook2003icalexport") == 0 && $cfg->get("show_ical_download") == 0 && $cfg->get("show_webcal_url") == 0) {
	
} else {
?>

<form name="ical" method="post" class="<?php isset($_POST['submit']) ? 'icalexportresults' : ''; ?>">
	<?php
	$categories = JEV_CommonFunctions::getCategoryData();

	?>
	<div class='choosecat' style='float:left;width:300px;'>
		<?php
		echo "<h3>" . JText::_('JEV_EVENT_CHOOSE_CATEG') . "</h3>\n";
// All categories
		$cb = "<input name=\"categories[]\" value=\"0\" type=\"checkbox\" onclick='clearIcalCategories(this);' ";
		$checked = false;
		if (!JRequest::getVar('categories', 0, 'POST'))
		{
			$cb = $cb . " CHECKED";
			$checked = true;
		}
		else if (JRequest::getVar('categories', 0, 'POST') && in_array(0, JRequest::getVar('categories', '', 'POST')))
		{
			$cb = $cb . " CHECKED";
			$checked = true;
		}
		echo $cb . "><strong>" . JText::_("JEV_EVENT_ALLCAT") . "</strong><br/>\n";
		?>
		<div id='othercats' <?php echo $checked ? 'style="display:none;max-height:100px;overflow-y:auto;"' : ''; ?> >
			<?php
			foreach ($categories AS $c)
			{
				// Make sure the user is authorised to view this category and the menu item doesn't block it!
				if (!in_array($c->id, $accessiblecats))
					continue;
				$cb = "<input name=\"categories[]\" value=\"" . $c->id . "\" type=\"checkbox\" onclick='clearAllIcalCategories(this);' ";
				if (!JRequest::getVar('categories', 0))
				{
					//$cb=$cb." CHECKED";
				}
				else if (JRequest::getVar('categories', 0) && in_array($c->id, JRequest::getVar('categories', '', 'POST')))
				{
					$cb = $cb . " CHECKED";
				}
				$cb = $cb . "><span style=\"background:" . $c->color . "\">&nbsp;&nbsp;&nbsp;&nbsp;</span> " . str_repeat(" - ", $c->level - 1) . $c->title . "<br/>\n";
				echo $cb;
			}
			?>
		</div>
	</div>
	<div class='chooseyear' style='float:left;width:300px;'>
		<?php
		echo "<h3>" . JText::_('JEV_SELECT_REP_YEAR') . "</h3>\n";

// All years
		$yt = "<input name=\"years[]\" type=\"checkbox\" value=\"0\"  onclick='clearIcalYears(this);' ";
		$checked = false;
		if (!JRequest::getVar('years', 0))
		{
			$yt = $yt . " CHECKED";
			$checked = true;
		}
		else if (JRequest::getVar('years', 0) && in_array(0, JRequest::getVar('years', '', 'POST')))
		{
			$yt = $yt . " CHECKED";
			$checked = true;
		}
		$yt = $yt . "><strong>" . JText::_("JEV_EVENT_ALLYEARS") . "</strong><br/>\n";
		echo $yt;
		?>
		<div id='otheryears' <?php echo $checked ? 'style="display:none;max-height:100px;overflow-y:auto;"' : ''; ?> >
			<?php
//consturc years array, easy to add own kind of selection
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$year = array();
			for ($y = $params->get("com_earliestyear", date('Y')); $y <= $params->get("com_latestyear", date('Y')); $y++)
			{
				if (!in_array($y, $year))
					$year[] = $y;
			}

			foreach ($year AS $y)
			{
				$yt = "<input name=\"years[]\" type=\"checkbox\" value=\"" . $y . "\" onclick='clearAllIcalYears(this);' ";
				if (!JRequest::getVar('years', 0))
				{
					//$yt = $yt . " CHECKED";
				}
				else if (JRequest::getVar('years', 0) && in_array($y, JRequest::getVar('years', '', 'POST')))
				{
					$yt = $yt . " CHECKED";
				}
				$yt = $yt . ">" . $y . "<br/>\n";
				echo $yt;
			}
			?>
		</div>
	</div>
	<?php
	echo "<div class='icalformat' style='clear:left; padding-top:5px;'>";
	echo "<h3>" . JText::_('JEV_ICAL_FORMATTING') . "</h3>\n";
	?>
	<label><input name="icalformatted" type="checkbox" value="1" <?php echo JRequest::getInt("icalformatted", 0) ? "checked='checked'" : ""; ?>/><?php echo JText::_("JEV_PRESERVE_HTML_FORMATTING"); ?></label>
	<br/>
	<br/>
</div>

<input class="ical_submit" type="submit" name="submit" value="<?php echo JText::_('JEV_GENERATE_ICALS'); ?>" />
</form>
<?php } ?>
