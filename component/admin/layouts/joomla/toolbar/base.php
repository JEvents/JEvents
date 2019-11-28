<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

if (GSLMSIE10)
{
    include (JPATH_SITE . "/layouts/joomla/toolbar/" .  basename(__FILE__));
    return;
}

$registry = JevRegistry::getinstance('yoursites');
$toolbarid = $registry->get("toolbarid" , "");
if ($toolbarid == "toolbar" || $toolbarid == "toolbar2" )
{
	echo  $displayData['action'];
    return;
	?>
	<div class="btn-wrapper" <?php echo $displayData['id']; ?>>
		<?php echo $displayData['action']; ?>
	</div>
	<?php
}
else
{
	echo $displayData['action'];
}
