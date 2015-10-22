<?php
defined('_JEXEC') or die('Restricted access');

JevHtmlBootstrap::framework();
if (JevJoomlaVersion::isCompatible("3.0") || JComponentHelper::getParams(JEV_COM_COMPONENT)->get("fixjquery", 1))
{
	JEVHelper::script("components/com_jevents/assets/js/jQnc.js");
	// this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
	JFactory::getDocument()->addScriptDeclaration("checkJQ();");
}

JHTML::stylesheet("com_jevents/bootstrap.css", array(), true);
JHTML::stylesheet("com_jevents/bootstrap-responsive.css", array(), true);
JHTML::stylesheet("components/com_jevents/assets/css/jquery.resizableColumns.css");
JEVHelper::script("components/com_jevents/assets/js/store.min.js");
//JEVHelper::script("components/com_jevents/assets/js/jquery.resizableColumns.min.js");
JEVHelper::script("components/com_jevents/assets/js/jquery.resizableColumns.js");

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

<form action="<?php echo JRoute::_("index.php?option=com_jevents&task=list.events&layout=events&Itemid=$Itemid"); ?>" method="post" name="adminForm" id="adminForm" >

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
						$this->loadedFromTemplate('icalevent.list_row', $row, 0, $template);
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

