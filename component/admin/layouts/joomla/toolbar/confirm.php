<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
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

$class      = $displayData['class'];
$btnClass   = isset($displayData['btnClass']) ? $displayData['btnClass'] : "";
$text       = $displayData['text'];
$doTask     = $displayData['doTask'];
$dataTarget = isset($displayData['data-target']) ? $displayData['data-target'] : "";

$displayData['gslicon'] = str_replace("icon-", "", $class);
$class="gsl-icon";
$gslIcon  = isset($displayData['gslicon']) ? ' gsl-icon="icon:'.$displayData['gslicon'].'"' : '';

if (!empty($dataTarget))
{
	$dataTarget = " data-target='#" . $dataTarget . "' data-toggle='modal' ";
	$dataTarget .= " data-bs-target='#" . $dataTarget . "' data-bs-toggle='modal' ";
}
?>
<button onclick="<?php echo $doTask; ?>" class="<?php echo $btnClass; ?>" <?php echo $dataTarget;?> >
    <span class="<?php echo $class; ?>" aria-hidden="true"  <?php echo $gslIcon; ?>></span>
	<?php echo $text; ?>
</button>
