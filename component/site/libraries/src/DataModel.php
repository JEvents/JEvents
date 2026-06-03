<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// Note: when constructed with a $dbmodel string argument (e.g.
// new DataModel("JEventsAdminDBModel")), the original datamodel.php code
// performs its own include_once to load that class — this shim does not
// interfere with that mechanism.
// TODO: migrate body from component/site/libraries/datamodel.php
if (!class_exists('JEventsDataModel', false))
{
	require_once \JEV_PATH . 'libraries/datamodel.php';
}

\class_alias('JEventsDataModel', __NAMESPACE__ . '\\DataModel');
