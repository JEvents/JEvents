<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including commonfunctions.php is deprecated. Use JEvents\CommonFunctions via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/site/libraries/src/CommonFunctions.php (JEvents\CommonFunctions).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\CommonFunctions', false))
{
	require_once __DIR__ . '/src/CommonFunctions.php';
}

if (!class_exists('JEV_CommonFunctions', false))
{
	class_alias('JEvents\\CommonFunctions', 'JEV_CommonFunctions');
}