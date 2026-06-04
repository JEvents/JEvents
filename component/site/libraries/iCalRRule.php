<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalRRule.php is deprecated. Use JEvents\IcalRRule via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/site/libraries/src/IcalRRule.php (JEvents\IcalRRule).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\IcalRRule', false))
{
	require_once __DIR__ . '/src/IcalRRule.php';
}

if (!class_exists('iCalRRule', false))
{
	class_alias('JEvents\\IcalRRule', 'iCalRRule');
}