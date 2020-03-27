<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$cfg        = JEVConfig::getInstance();
$data       = $this->datamodel->getCatData($this->catids, $cfg->get('com_showrepeats', 0), $this->limit, $this->limitstart);
$this->data = $data;
$Itemid     = JEVHelper::getItemid();

$input = Factory::getApplication()->input;

?>
	<div class="jev_catselect"><?php echo $data['catname'];
$this->viewNavCatText($this->catids, JEV_COM_COMPONENT, 'cat.listevents', $this->Itemid); ?></div><?php

if (\Joomla\String\StringHelper::strlen($data['catdesc']) > 0)
{
	echo "<div class='jev_catdesc'>" . $data['catdesc'] . "</div>";
}
?>
	<table align="center" width="90%" cellspacing="0" cellpadding="5" class="ev_table">
		<?php
		$num_events = count($data['rows']);
		$chdate     = "";
		if ($num_events > 0)
		{
			for ($r = 0; $r < $num_events; $r++)
			{
				$row = $data['rows'][$r];

				$event_day_month_year = $row->dup() . $row->mup() . $row->yup();

				if (($event_day_month_year <> $chdate) && $chdate <> '')
				{
					echo '</ul></td></tr>' . "\n";
				}

				if ($event_day_month_year <> $chdate)
				{
					$date = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 1);
					echo '<tr><td class="ev_td_left">' . $date . '</td>' . "\n";
					echo '<td align="left" valign="top" class="ev_td_right"><ul class="ev_ul">' . "\n";
				}

				$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
				echo "<li class='ev_td_li' $listyle>\n";
				$this->loadedFromTemplate('icalevent.list_row', $row, 0);
				echo "</li>\n";

				$chdate = $event_day_month_year;
			}
			echo "</ul></td>\n";
		}
		else
		{
			echo '<tr>';
			echo '<td align="left" valign="top" class="ev_td_right  jev_noresults">' . "\n";

			if (count($this->catids) == 0 || $data['catname'] == "")
			{
				echo Text::_('JEV_EVENT_CHOOSE_CATEG') . '</td>';
			}
			else
			{
				echo Text::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $data['catname'] . '</b></td>';
			}
		}
		?>
		</tr></table><br/>
	<br/><br/>
<?php

// Create the pagination object
if ($data["total"] > $data["limit"])
{
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
