<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

$cfg    = JEVConfig::getInstance();
$app    = Factory::getApplication();
$input  = $app->input;

$view   = $this->getViewName();

$script = <<<SCRIPT
function clearIcalCategories(allcats){
	if(allcats.checked){
		jevjq('input[name="categories[]"]:checked').each (function(i, el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		jevjq('#othercats').css('display','none');
	}
	else {
		jevjq('input[name="categories[]"]').each (function(i, el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		jevjq('#othercats').css('display','block');
	}
}
function clearAllIcalCategories(){
		jevjq('input[name="categories[]"]:checked').each (function(i, el){
			if (el.value==0){
				el.checked=false;
			}
		});
}
function clearIcalYears(allyears){
	if(allyears.checked){
		jevjq('input[name="years[]"]:checked').each (function(i, el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		jevjq('#otheryears').css('display','none');
	}
	else {
		jevjq('input[name="years[]"]').each (function(i, el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		jevjq('#otheryears').css('display','block');
	}
}
function clearAllIcalYears(){
		jevjq('input[name="years[]"]:checked').each (function(i, el){
			if (el.value==0){
				el.checked=false;
			}
		});
}

SCRIPT;
$doc    = Factory::getDocument();
$doc->addScriptDeclaration($script);

$accessiblecats = explode(",", $this->datamodel->accessibleCategoryList());

echo "<h2 id='cal_title'>" . Text::_('JEV_ICAL_EXPORT') . "</h2>\n";

if ($input->getString("submit", "") != "")
{

	$categories = $input->get('categories', array(0), 'ARRAY');

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

	$jr_years = $input->post->get('years', array(0), 'RAW');
	$years    = JEVHelper::forceIntegerArray($jr_years, true);
	$cats     = implode(",", $cats);

	$link = Uri::root() . "index.php?option=com_jevents&task=icals.export&format=ical";
	if (count($cats) > 0)
	{
		$link .= "&catids=" . $cats;
	}
	$link .= "&years=" . $years;
	if ($input->getInt("icalformatted", 0))
	{
		$link .= "&icf=1";
	}

	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	if ($params->get("constrained", 0))
	{
		$link .= "&Itemid=" . $input->getInt("Itemid", 1);
	}

	$icalkey    = $params->get("icalkey", "secret phrase");
	$publiclink = $link . "&k=" . md5($icalkey . $catsImploded . $years);

	$user = Factory::getUser();
	if ($user->id != 0)
	{
		$privatelink = $link . "&pk=" . md5($icalkey . $catsImploded . $years . $user->password . $user->username . $user->id) . "&i=" . $user->id;
	}

	echo "<p><a href='$publiclink'>" . Text::_('JEV_REP_ICAL_PUBLIC') . "</a></p>";
	if ($user->id != 0)
	{
		echo "<p><a href='$privatelink'>" . Text::_('JEV_REP_ICAL_PRIVATE') . "</a></p>";
	}

	if ($cfg->get("outlook2003icalexport", 1))
	{
		echo "<p>" . Text::_('Outlook 2003 specific links') . "</p>";
		echo "<p><a href='$publiclink&outlook2003=1'>" . Text::_('JEV_REP_ICAL_PUBLIC') . "</a></p>";
		if ($user->id != 0)
		{
			echo "<p><a href='$privatelink&outlook2003='>" . Text::_('JEV_REP_ICAL_PRIVATE') . "</a></p>";
		}
	}
}
?>

<form name="ical" method="post" class="<?php isset($_POST['submit']) ? 'icalexportresults' : ''; ?>">
	<?php
	$categories = JEV_CommonFunctions::getCategoryData();

	?>
	<div class='choosecat' style='float:left;width:300px;'>
		<?php
		echo "<h3>" . Text::_('JEV_EVENT_CHOOSE_CATEG') . "</h3>\n";
		// All categories
		$cb      = "<input name=\"categories[]\" value=\"0\" type=\"checkbox\" onclick='clearIcalCategories(this);' ";
		$checked = false;
		if (!$input->post->get('categories', 0, 'ARRAY'))
		{
			$cb      = $cb . " CHECKED";
			$checked = true;
		}
		else if ($input->post->get('categories', 0, 'ARRAY') && in_array(0, $input->post->get('categories', '', 'ARRAY')))
		{
			$cb      = $cb . " CHECKED";
			$checked = true;
		}
		echo $cb . "><strong>" . Text::_("JEV_EVENT_ALLCAT") . "</strong><br/>\n";
		?>
		<div id='othercats' <?php echo $checked ? 'style="display:none;max-height:100px;overflow-y:auto;"' : 'style="display:block;"'; ?> >
			<?php
			foreach ($categories AS $c)
			{
				// Make sure the user is authorised to view this category and the menu item doesn't block it!
				if (!in_array($c->id, $accessiblecats))
					continue;
				$cb = "<input name=\"categories[]\" value=\"" . $c->id . "\" type=\"checkbox\" onclick='clearAllIcalCategories(this);' ";
				if (!$input->get('categories', 0, 'ARRAY'))
				{
					//$cb=$cb." CHECKED";
				}
				else if ($input->get('categories', 0) && in_array($c->id, $input->post->get('categories', '')))
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
		echo "<h3>" . Text::_('JEV_SELECT_REP_YEAR') . "</h3>\n";

		// All years
		$yt      = "<input name=\"years[]\" type=\"checkbox\" value=\"0\"  onclick='clearIcalYears(this);' ";
		$checked = false;
		if (!$input->get('years', 0))
		{
			$yt      = $yt . " CHECKED";
			$checked = true;
		}
		else if ($input->get('years', 0) && in_array(0, $input->post->get('years', '')))
		{
			$yt      = $yt . " CHECKED";
			$checked = true;
		}
		$yt = $yt . "><strong>" . Text::_("JEV_EVENT_ALLYEARS") . "</strong><br/>\n";
		echo $yt;
		?>
		<div id='otheryears' <?php echo $checked ? 'style="display:none;max-height:100px;overflow-y:auto;"' : ''; ?> >
			<?php
			// Consturct years array, easy to add own kind of selection
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$year   = array();
			for ($y = JEVHelper::getMinYear(); $y <= JEVHelper::getMaxYear(); $y++)
			{
				if (!in_array($y, $year))
					$year[] = $y;
			}

			foreach ($year AS $y)
			{
				$yt = "<input name=\"years[]\" type=\"checkbox\" value=\"" . $y . "\" onclick='clearAllIcalYears(this);' ";
				 if ($input->get('years', 0) && in_array($y, $input->post->get('years', '')))
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
	if ($params->get("icalformatted", 1) == 1)
	{
	echo "<div class='icalformat' style='clear:left; padding-top:5px;'>";
	echo "<h3>" . Text::_('JEV_ICAL_FORMATTING') . "</h3>\n";
	?>
	<label><input name="icalformatted" type="checkbox"
	              value="1" <?php echo $input->getInt("icalformatted", 0) ? "checked='checked'" : ""; ?>/><?php echo Text::_("JEV_PRESERVE_HTML_FORMATTING"); ?>
	</label>
	<br/>
	<br/>
	</div>
    <?php
    }
    ?>
	<input type="submit" name="submit" value="<?php echo Text::_('JEV_SELECT'); ?>"/>
</form>

