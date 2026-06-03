<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// Note: jIcalEventRepeat extends jIcalEventDB extends jEventCal — the full
// inheritance chain is loaded by jicaleventrepeat.php's own dependencies.
// This is the primary domain object returned by all JEventsDBModel queries.
// TODO: migrate body from component/site/libraries/jicaleventrepeat.php
if (!class_exists('jIcalEventRepeat', false))
{
	require_once \JEV_PATH . 'libraries/jicaleventrepeat.php';
}

\class_alias('jIcalEventRepeat', __NAMESPACE__ . '\\IcalEventRepeat');
