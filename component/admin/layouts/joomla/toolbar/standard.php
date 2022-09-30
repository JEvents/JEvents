<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;

if (GSLMSIE10)
{
	include (JPATH_SITE . "/layouts/joomla/toolbar/" .  basename(__FILE__));
	return;
}

HTMLHelper::_('behavior.core');

// Joomla 4 switched to task from doTask
$doTask   = isset($displayData['doTask']) ? $displayData['doTask'] : false;
$task     = isset($displayData['task'])   ? $displayData['task'] : false;
$class    = $displayData['class'];
$text     = $displayData['text'];
$btnClass = $displayData['btnClass'];
$listCheck = isset($displayData['listCheck'])   ? $displayData['listCheck'] : false;

if (!$doTask && $task)
{
	$doTask = "Joomla.submitbutton('" . $task . "')";
	if ($listCheck)
	{
		$doTask = "if ( document.getElementById('boxchecked').value > 0 ) { " . $doTask . " } else alert ('" . JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST', true) . "');";

	}
}

$displayData['gslicon'] = str_replace("icon-", "", $class);

if (strpos($displayData['gslicon'], " ") > 0)
{
	$displayData['gslicon'] = explode(" ", $displayData['gslicon'])[0];
}
$mapping = array(
        "edit" => "file-edit",
        "new" => "plus-circle",
        "publish" => "check",
        "unpublish" => "close",
        "checkin" => "unlock",
        "apply" => "pencil",
        "save" => "check",
        "cancel" => "reply",
    );

if (array_key_exists($displayData['gslicon'], $mapping))
{
	$displayData['gslicon'] = $mapping[$displayData['gslicon']];
}
$class="gsl-icon";

$gslIcon  = isset($displayData['gslicon']) ? ' gsl-icon="icon:'.$displayData['gslicon'].'"' : '';

?>
<button onclick="<?php echo $doTask; ?>" class="<?php echo $btnClass; ?>">
	<span class="<?php echo trim($class); ?>" aria-hidden="true" <?php echo $gslIcon; ?>></span>
	<?php echo $text; ?>
</button>
