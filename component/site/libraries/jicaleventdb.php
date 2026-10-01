<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jicaleventdb.php is deprecated. Use JEvents\IcalEventDB via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/site/libraries/src/IcalEventDB.php (JEvents\IcalEventDB).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\IcalEventDB', false))
{
	require_once __DIR__ . '/src/IcalEventDB.php';
}

if (!class_exists('jIcalEventDB', false))
{
	class_alias('JEvents\\IcalEventDB', 'jIcalEventDB');
}