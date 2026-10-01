<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including saveIcalEvent.php is deprecated. Use JEvents\Administrator\SaveIcalEvent via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/admin/libraries/src/Administrator/SaveIcalEvent.php (JEvents\Administrator\SaveIcalEvent).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Administrator\\SaveIcalEvent', false))
{
	require_once __DIR__ . '/src/Administrator/SaveIcalEvent.php';
}

if (!class_exists('SaveIcalEvent', false))
{
	class_alias('JEvents\\Administrator\\SaveIcalEvent', 'SaveIcalEvent');
}