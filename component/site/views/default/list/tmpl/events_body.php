<?php
defined('_JEXEC') or die('Restricted access');

$data = $this->data;

$Itemid = JEVHelper::getItemid();
$compparams = JComponentHelper::getParams("com_jevents");

$app = JFactory::getApplication('site');
$params = $app->getParams();
$active = $app->getMenu()->getActive();
if ($active)
{
	$params->merge($active->params);
}

$infields = explode("||", $compparams->get("columns","TITLE_LINK|Title Link|Title"));
$cols = array();
$titles = array();
foreach ($infields as $infield){
	$parts = explode("|", $infield);
	$cols[] = $parts[0];
	$titles[] = $parts[2];
}
?>

<form action="<?php echo JRoute::_("index.php?option=com_jevlocations&task=locations.locations&layout=locations&Itemid=$Itemid"); ?>" method="post" name="adminForm" id="adminForm" >

	<div id="eventlist">
		<table class="eventlist adminlist">
			<thead>
				<tr>
					<?php
					foreach ($titles as $title){
						?>
					<th>
						<?php
						echo $title;
						?>
					</th>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$template = "<tr>";
				foreach ($cols as $col){
					$template .= "<td class='eventlist_col'>{{xx:$col}}</td>";
				}
				$template .= "</tr>";

				$num_events = count($data['rows']);
				$chdate = "";
				if ($num_events > 0)
				{
					for ($r = 0; $r < $num_events; $r++)
					{
						$row = $data['rows'][$r];
						$this->loadedFromTemplate('icalevent.list_event', $row, 0, $template);
						//($view, $template_name, $event, $mask, $template_value = false)

					}
				}
				?>
			</tbody>
		</table>
		<?php
			// Create the pagination object
			if ($data["total"] > $data["limit"])
			{
				$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
			}
		?>
	</div>
</form>

