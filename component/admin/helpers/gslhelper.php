<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
@trigger_error('Directly including gslhelper.php is deprecated. Use JEvents\Administrator\GslHelper via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/admin/helpers/src/GslHelper.php (JEvents\Administrator\GslHelper).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Administrator\\GslHelper', false))
{
	require_once __DIR__ . '/../libraries/src/Administrator/GslHelper.php';
}

if (!class_exists('GslHelper', false))
{
	class_alias('JEvents\\Administrator\\GslHelper', 'GslHelper');
}