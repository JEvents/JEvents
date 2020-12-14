<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$cfg = JEVConfig::getInstance();

$data       = $this->datamodel->getCatData($this->catids, $cfg->get('com_showrepeats', 0), $this->limit, $this->limitstart);
$this->data = $data;

$Itemid = JEVHelper::getItemid();
?>
	<div id='jev_maincal' class='jev_listview category'>
	<div class="jev_listrow">
		<div class="jev_daysnames category">
			<?php $this->viewNavCatText($this->catids, JEV_COM_COMPONENT, 'cat.listevents', $this->Itemid); ?>
			<?php
			$hasevents = false;

			echo $data ['catname'];
			?>
		</div>
		<?php
		if (\Joomla\String\StringHelper::strlen($data ['catdesc']) > 0)
		{
			echo "<div class='jev_catdesc'>" . HTMLHelper::_('content.prepare', $data ['catdesc']) . "</div>";
		}
		echo "</div>";
		$num_events = count($data ['rows']);
		$chdate     = "";
		if ($num_events > 0)
		{
			$hasevents = true;
			for ($r = 0; $r < $num_events; $r++)
			{
				$row = $data ['rows'] [$r];

				$event_day_month_year = $row->dup() . $row->mup() . $row->yup();

				if (($event_day_month_year != $chdate) && $chdate != '')
				{
					echo '</ul></div>' . "\n";
				}

				if ($event_day_month_year != $chdate)
				{
					$date = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 1);
					echo '<div class="jev_listrow"><ul class="ev_ul">' . "\n";
				}

				$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
				echo "<li class='ev_td_li' $listyle>\n";
				$this->loadedFromTemplate('icalevent.list_row', $row, 0);
				echo "</li>\n";

				$chdate = $event_day_month_year;
			}
			echo "</ul></div>\n";
		}

		if (!$hasevents)
		{
			if (count($this->catids) == 0 || $data ['catname'] == "")
			{
				echo Text::_('JEV_EVENT_CHOOSE_CATEG') . '';
			}
			else
			{
				echo Text::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $data ['catname'] . '</b>';
			}
		}
		?>
		<div class="jev_clear"></div>
	</div>
<?php
// Create the pagination object
if ($data ["total"] > $data ["limit"])
{
	$this->paginationForm($data ["total"], $data ["limitstart"], $data ["limit"]);
}
