<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// Note: jIcalEventDB extends jEventCal — loading this file also loads EventCal
// via jeventcal.php's own include chain.
// TODO: migrate body from component/site/libraries/jicaleventdb.php
if (!class_exists('jIcalEventDB', false))
{
	require_once \JEV_PATH . 'libraries/jicaleventdb.php';
}

\class_alias('jIcalEventDB', __NAMESPACE__ . '\\IcalEventDB');
