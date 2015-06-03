<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

if ($cfg->get("tooltiptype", 'overlib') == 'overlib')
{
	JEVHelper::loadOverlib();
}

$view = $this->getViewName();
echo $this->loadTemplate('cell');
$eventCellClass = "EventCalendarCell_" . $view;

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);
?>

<div class="cal_div">
	<div class="topleft" ><span></span>
	</div>
	<div  class="cal_div_month cal_div_month_prev">
		<span>
			<a href='<?php echo $precedingMonth["link"]; ?>' title='<?php echo $precedingMonth['name']; ?>' style='text-decoration:none;'><?php echo $precedingMonth['name']; ?></a>
		</span>
	</div>
	<div  class="cal_div_currentmonth">
		<span>
			<?php echo $this->data['fieldsetText']; ?>
		</span>
	</div>
	<div class="cal_div_month  cal_div_month_next">
		<span>
			<a href='<?php echo $followingMonth["link"]; ?>' title='<?php echo $followingMonth['name']; ?>' style='text-decoration:none;'><?php echo $followingMonth['name']; ?></a>
		</span>
	</div>
	<?php
	$count = 0;
	foreach ($this->data["daynames"] as $dayname)
	{
		?>
		<div class="cal_div_daynames cal_div_daynames<?php echo $count; ?>" >
			<span>
				<?php echo $dayname; ?>
			</span>
		</div>
		<?php
		$count ++;
	}
	$datacount = count($this->data["dates"]);
	$dn = 0;
	for ($w = 0; $w < 6 && $dn < $datacount; $w++)
	{
		?>
		<div class="cal_div_weekrow" >
			<div class='cal_div_weeklink'>
				<span>
					<?php
					list($week, $link) = each($this->data['weeks']);
					?>
					<a href='<?php echo $link; ?>'><?php echo $week; ?></a>
				</span>
			</div>
			<?php
			for ($d = 0; $d < 7 && $dn < $datacount; $d++)
			{
				$currentDay = $this->data["dates"][$dn];
				switch ($currentDay["monthType"]) {
					case "prior":
					case "following":
						?>
						<div class="cal_div_daysoutofmonth  <?php echo "cal_div_day".$d;?>" >
							<span>
								<?php echo JEVHelper::getMonthName($currentDay["month"]); ?>
							</span>
						</div>
						<?php
						break;
					case "current":
						$cellclass = $currentDay["today"] ? 'class="cal_div_today cal_div_day'.$d.'"' : 'class="cal_div_daysnoevents cal_div_day'.$d.'"';
						// stating the height here is needed for konqueror and safari
						?>
						<div <?php echo $cellclass; ?>>
							<span>
								<?php $this->_datecellAddEvent($this->year, $this->month, $currentDay["d"]); ?>
								<a class="cal_daylink" href="<?php echo $currentDay["link"]; ?>" title="<?php echo JText::_('JEV_CLICK_TOSWITCH_DAY'); ?>"><?php echo $currentDay['d']; ?></a>
								<?php
								if (count($currentDay["events"]) > 0)
								{
									foreach ($currentDay["events"] as $key => $val)
									{

										if ($currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay', 5))
										{
											$style = "width:100%;padding:2px;";
										}
										else
										{
											$style = "float:left; padding:0px;";
										}
										?>
										<div style="<?php echo $style; ?>">
											<?php
											$ecc = new $eventCellClass($val, $this->datamodel, $this);
											echo $ecc->calendarCell($currentDay, $this->year, $this->month, $key);
											?>
										</div>
										<?php
										$currentDay['countDisplay'] ++;
									}
								}
								?>
							</span>
						</div>
						<?php
						break;
				}
				$dn++;
			}
			?>
			<div class="divclear"></div>
		</div>
		<?php
	}
	?>
</div>
<?php
$this->eventsLegend();

$script = <<<SCRIPT
jQuery(document).ready(function(){
	jQuery(".cal_div_weekrow").each(
	function(idx, el){
		var rowheight = jQuery(el).height();
		jQuery(el).find('div.cal_div_weeklink > span').each (
			function (is, subel){
				jQuery(subel).css("height", rowheight+"px");
				jQuery(subel).css("line-height", rowheight+"px");
			}
		);
		jQuery(el).find('div.cal_div_daysoutofmonth > span').each (
			function (is, subel){
				jQuery(subel).css("height", rowheight+"px");
				jQuery(subel).css("line-height", rowheight+"px");
			}
		);
		jQuery(el).find('div.cal_div_daysnoevents > span').each (
			function (is, subel){
				jQuery(subel).css("height", rowheight+"px");
			}
		);
		jQuery(el).find('div.cal_div_today> span').each (
			function (is, subel){
				jQuery(subel).css("height", rowheight+"px");
			}
		);
		jQuery(el).find('div.cal_div_dayshasevents > span').each (
			function (is,subel){
				jQuery(subel).css("height", rowheight+"px");
			}
		);

	});

});
SCRIPT;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration($script);