<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

JevHtmlBootstrap::framework();

JEVHelper::script("components/com_jevents/assets/js/jQnc.js");

HTMLHelper::stylesheet("com_jevents/bootstrap.css", array(), array());
HTMLHelper::stylesheet("com_jevents/bootstrap-responsive.css", array(), array());
HTMLHelper::stylesheet("components/com_jevents/assets/css/jquery.resizableColumns.css");
JEVHelper::script("components/com_jevents/assets/js/store.min.js");
//JEVHelper::script("components/com_jevents/assets/js/jquery.resizableColumns.min.js");
JEVHelper::script("components/com_jevents/assets/js/jquery.resizableColumns.js");

$data = $this->data;

$Itemid     = JEVHelper::getItemid();
$compparams = ComponentHelper::getParams("com_jevents");

$app    = Factory::getApplication('site');
$params = $app->getParams();
$active = $app->getMenu()->getActive();
if ($active)
{
	$params->merge($active->getParams());
}

$infields = explode("||", $compparams->get("columns", "TITLE_LINK|Title Link|Title"));
$cols     = array();
$titles   = array();
foreach ($infields as $infield)
{
	$parts    = explode("|", $infield);
	$cols[]   = $parts[0];
	$titles[] = $parts[2];
}
?>

<form action="<?php echo Route::_("index.php?option=com_jevents&task=list.events&layout=events&Itemid=$Itemid"); ?>"
      method="post" name="adminForm" id="adminForm">

	<div id="eventlist">
		<table class="eventlist table table-bordered" data-resizable-columns-id="demo-table">
			<thead>
			<tr>
				<?php
				$i = 0;
				foreach ($titles as $title)
				{
					if ($i > 0 && $titles[$i] == $titles[$i-1])
					{
						$i++;
						continue;
					}
					?>
					<th data-resizable-column-id="<?php echo $titles[$i];?>">
						<?php
						$i++;
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
			$style = '';
			$bgStyle = '';
			if ($params->get('setrowbgcolor', 0)) {
				$style = "style='background-color:{{Event background colour:COLOUR}}'";
			}

			$template = "<tr $style>";

			$i = 0;
			$closed = true;
			foreach ($cols as $col)
			{
				$bgStyle = '';

				if ($i > 0 && $titles[$i] == $titles[$i-1])
				{
					$template .= "{{xx:$col}}";
					$closed = false;
					$i ++;
					continue;
				}

				if (!$closed)
				{
					$template .= "</td>";
					$closed = true;
				}

				if ($col === 'COLOUR')
				{
					$bgStyle  = "style='background-color:{{xx:$col}}'";
					$template .= "<td class='eventlist_col' $bgStyle> ";
				}
				else
				{
					$template .= "<td class='eventlist_col' $bgStyle>{{xx:$col}}";
				}
				$closed = false;

				$i ++;
			}
			if (!$closed)
			{
				$template .= "</td>";
			}

			$template .= "</tr>";

			$num_events = count($data['rows']);
			$chdate     = "";
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
		if ($("table.eventlist th").length > 1) {
			$("table.eventlist").resizableColumns(
				{store: window.store}
			);
		}
	})(jevjq);
</script>

