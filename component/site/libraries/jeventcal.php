<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jeventcal.php is deprecated. Use JEvents\EventCal via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/EventCal.php (JEvents\EventCal).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\EventCal', false))
{
	require_once __DIR__ . '/src/EventCal.php';
}

if (!class_exists('jEventCal', false))
{
	class_alias('JEvents\\EventCal', 'jEventCal');
}