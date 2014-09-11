<?php
defined('_JEXEC') or die('Restricted access');

if (JevJoomlaVersion::isCompatible("3.0"))
{
	JHtml::_('jquery.framework');
	JHtml::_('behavior.framework', true);
	JHtml::_('bootstrap.framework');
	JHTML::script("components/com_jevents/assets/js/jQnc.js");
	// this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
	JFactory::getDocument()->addScriptDeclaration("checkJQ();");
}
else if (JComponentHelper::getParams(JEV_COM_COMPONENT)->get("fixjquery", 1))
{
	// Make loading this conditional on config option
	JFactory::getDocument()->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js");
	JHTML::script("components/com_jevents/assets/js/jQnc.js");
	JHTML::script("components/com_jevents/assets/js/bootstrap.min.js");
	// this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
	JFactory::getDocument()->addScriptDeclaration("checkJQ();");
}
JHTML::stylesheet("components/com_jevents/assets/css/bootstrap.css");
JHTML::stylesheet("components/com_jevents/assets/css/jquery.resizableColumns.css");
JHTML::script("components/com_jevents/assets/js/store.min.js");
//JHTML::script("components/com_jevents/assets/js/jquery.resizableColumns.min.js");
JHTML::script("components/com_jevents/assets/js/jquery.resizableColumns.js");

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

$infields = explode("||", $compparams->get("columns", "TITLE_LINK|Title Link|Title"));
$cols = array();
$titles = array();
foreach ($infields as $infield)
{
	$parts = explode("|", $infield);
	$cols[] = $parts[0];
	$titles[] = $parts[2];
}
?>

<form action="<?php echo JRoute::_("index.php?option=com_jevlocations&task=locations.locations&layout=locations&Itemid=$Itemid"); ?>" method="post" name="adminForm" id="adminForm" >

	<div id="eventlist">
		<table class="eventlist table table-bordered" data-resizable-columns-id="demo-table">
			<thead>
				<tr>
					<?php
					$i = 0;
					foreach ($titles as $title)
					{
						?>
						<th data-resizable-column-id="<?php echo $titles[$i];
						$i++; ?>">
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
				foreach ($cols as $col)
				{
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
<script>
	(function ($) {
		$("table.eventlist").resizableColumns(
			{store: window.store}
		);
	})(jevjq);
</script>

