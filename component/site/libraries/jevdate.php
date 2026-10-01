<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('JPATH_BASE') or die;
@trigger_error('Directly including jevdate.php is deprecated. Use JEvents\Date via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/Date.php (JEvents\Date).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Date', false))
{
	require_once __DIR__ . '/src/Date.php';
}

if (!class_exists('JevDate', false))
{
	class_alias('JEvents\\Date', 'JevDate');
}
