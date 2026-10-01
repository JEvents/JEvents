<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
@trigger_error('Directly including gslmenuhelper.php is deprecated. Use JEvents\Administrator\GslJEventsMenuHelper via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/admin/helpers/src/GslJEventsMenuHelper.php (JEvents\Administrator\GslJEventsMenuHelper).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Administrator\\GslJEventsMenuHelper', false))
{
	require_once __DIR__ . '/../libraries/src/Administrator/GslJEventsMenuHelper.php';
}

if (!class_exists('GslJEventsMenuHelper', false))
{
	class_alias('JEvents\\Administrator\\GslJEventsMenuHelper', 'GslJEventsMenuHelper');
}