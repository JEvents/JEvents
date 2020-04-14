<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$cfg = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDataForAdmin($this->creator_id, $this->limit, $this->limitstart);

$frontendPublish = intval($cfg->get('com_frontendPublish', 0)) > 0;

$num_events = count($data['rows']);
$chdate     = '';

echo '<fieldset><legend class="ev_fieldset">' . Text::_('JEV_ADMINPANEL') . '</legend><br />' . "\n";

$myItemid  = JEVHelper::getAdminItemid();
$form_link = Route::_(
	'index.php?option=' . JEV_COM_COMPONENT
	. '&task=admin.listevents'
	. "&Itemid=" . $myItemid
	, false);

?>
	<form action="<?php echo $form_link; ?>" method="post">
		<?php
		$filters    = jevFilterProcessing::getInstance(array("startdate"));
		$filterHTML = $filters->getFilterHTML();
		foreach ($filterHTML as $filter)
		{
			echo "<div class='jev_adminfilter'>" . $filter["title"] . "<br/>" . $filter["html"] . "</div>";
		}
		?>
	</form>
<?php
echo '<table align="center" width="90%" cellspacing="0" cellpadding="5" class="ev_table">' . "\n";

if ($num_events > 0)
{
	for ($r = 0; $r < $num_events; $r++)
	{
		$row              = $data['rows'][$r];
		$event_month_year = $row->mup() . $row->yup();

		if ($event_month_year <> $chdate && $chdate <> "")
		{
			echo '</ul></td></tr>' . "\n";
		}
		if ($event_month_year <> $chdate)
		{
			echo '<tr><td class="ev_td_left">' . "\n"
				. JEventsHTML::getDateFormat($row->yup(), $row->mup(), '', 3) . '</td>' . "\n";
			echo '<td class="ev_td_right"><ul class="ev_ul">' . "\n";
		}

		$this->viewEventRowAdmin($row);
		$chdate = $event_month_year;
	}
	echo '</ul></td>' . "\n";
}
else
{
	echo '<tr>' . "\n";
	echo '<td align="left" valign="top" class="ev_td_right">' . "\n";
	echo Text::_('JEV_NO_EVENTS');
}
echo '</tr></table><br />' . "\n";
echo '</fieldset><br /><br />' . "\n";


// Create the pagination object
if ($data["total"] > $data["limit"])
{
	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
