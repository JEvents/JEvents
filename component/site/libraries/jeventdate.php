<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jeventdate.php is deprecated. Use JEvents\EventDate via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/site/libraries/src/EventDate.php (JEvents\EventDate).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\EventDate', false))
{
	require_once __DIR__ . '/src/EventDate.php';
}

if (!class_exists('JEventDate', false))
{
	class_alias('JEvents\\EventDate', 'JEventDate');
}