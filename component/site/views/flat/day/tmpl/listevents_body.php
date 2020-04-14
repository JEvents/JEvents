<?php
defined('_JEXEC') or die ('Restricted access');

use Joomla\CMS\Language\Text;


$cfg = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDayData($this->year, $this->month, $this->day);
$this->Redirectdetail();

$cfg    = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();

// previous and following month names and links
$followingDay = $this->datamodel->getFollowingDay($this->year, $this->month, $this->day);
$precedingDay = $this->datamodel->getPrecedingDay($this->year, $this->month, $this->day);

?>

<div class="jev_toprow">
	<!-- <div class="jev_header">
		  <h2 class="gsl-h2"><?php echo Text::_('DAILY_VIEW'); ?></h2>
		  <div class="today" ><?php echo JEventsHTML::getDateFormat($this->year, $this->month, $this->day, 0); ?></div>
		</div> -->
	<div class="jev_header2">
		<div class="previousmonth">
			<?php if ($precedingDay) echo "<a href='" . $precedingDay . "' title='" . Text::_("PRECEEDING_Day") . "' >" . Text::_("PRECEEDING_Day") . "</a>"; ?>
		</div>
		<div class="currentmonth">
			<?php echo JEventsHTML::getDateFormat($this->year, $this->month, $this->day, 0); ?>
		</div>
		<div class="nextmonth">
			<?php if ($followingDay) echo "<a href='" . $followingDay . "' title='" . Text::_("FOLLOWING_Day") . "' >" . Text::_("FOLLOWING_Day") . "</a>"; ?>
		</div>

	</div>
</div>
<div id='jev_maincal' class='jev_listview'>
	<div class="jev_listrow">

		<?php
		$hasevents = false;

		// // Timeless Events First
		if (count($data ['hours'] ['timeless'] ['events']) > 0)
		{
			$hasevents  = true;
			$start_time = Text::_('TIMELESS');

			echo '<ul class="ev_ul">' . "\n";
			foreach ($data ['hours'] ['timeless'] ['events'] as $row)
			{

				$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
				echo "<li class='ev_td_li' $listyle>\n";
				$this->loadedFromTemplate('icalevent.list_row', $row, 0);
				echo "</li>\n";
			}
			echo "</ul>\n";
		}

		for ($h = 0; $h < 24; $h++)
		{
			if (count($data ['hours'] [$h] ['events']) > 0)
			{
				$hasevents  = true;
				$start_time = JEVHelper::getTime($data ['hours'] [$h] ['hour_start']);

				echo '<ul class="ev_ul">' . "\n";
				foreach ($data ['hours'] [$h] ['events'] as $row)
				{
					$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
					echo "<li class='ev_td_li' $listyle>\n";
					$this->loadedFromTemplate('icalevent.list_row', $row, 0);
					echo "</li>\n";
				}
				echo "</ul>\n";
			}
		}

		if (!$hasevents)
		{
			echo '<div class="list_no_e">' . "\n";
			echo Text::_('JEV_NO_EVENTS_FOUND');
			echo "</div>\n";
		}

		?>
	</div>
	<div class="jev_clear"></div>
</div>
