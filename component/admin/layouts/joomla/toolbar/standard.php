<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

if (GSLMSIE10)
{
	include (JPATH_SITE . "/layouts/joomla/toolbar/" .  basename(__FILE__));
	return;
}

JHtml::_('behavior.core');

$doTask   = $displayData['doTask'];
$class    = $displayData['class'];
$text     = $displayData['text'];
$btnClass = $displayData['btnClass'];

$displayData['gslicon'] = str_replace("icon-", "", $class);

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
