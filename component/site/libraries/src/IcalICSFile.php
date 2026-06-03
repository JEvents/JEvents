<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// TODO: migrate body from component/site/libraries/iCalICSFile.php
if (!class_exists('iCalICSFile', false))
{
	require_once \JEV_PATH . 'libraries/iCalICSFile.php';
}

\class_alias('iCalICSFile', __NAMESPACE__ . '\\IcalICSFile');
