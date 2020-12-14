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

?>
icon-<?php echo $displayData['icon']; ?>
