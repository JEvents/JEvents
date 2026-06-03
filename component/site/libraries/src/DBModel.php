<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// Note: JEventsAdminDBModel extends JEventsDBModel and is loaded separately
// via include_once inside DataModel — it is not affected by this shim.
// TODO: migrate body from component/site/libraries/dbmodel.php
if (!class_exists('JEventsDBModel', false))
{
	require_once \JEV_PATH . 'libraries/dbmodel.php';
}

\class_alias('JEventsDBModel', __NAMESPACE__ . '\\DBModel');
