<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;

function DefaultViewNavCatText($view, $catid, $option, $task, $Itemid)
{

	$filters    = jevFilterProcessing::getInstance(array("category"));
	$filterHTML = $filters->getFilterHTML();
	?>

	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="center" width="100%">
				<form action="<?php echo Route::_("index.php"); ?>" method="get">
					<input type="hidden" name="option" value="<?php echo $option; ?>"/>
					<input type="hidden" name="task" value="<?php echo $task; ?>"/>
					<input type="hidden" name="offset" value="1"/>
					<?php
					foreach ($filterHTML as $filter)
					{
						echo $filter["html"];
					}
					?>

					<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
				</form>
			</td>
		</tr>
	</table>
	<?php
}
