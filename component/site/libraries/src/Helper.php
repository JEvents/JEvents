<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim — highest-usage class (1,152 refs across 280 files).
// Loads the legacy global class and exposes it under the JEvents namespace.
// Move the class body here when ready to fully migrate.
//
// Note: helper.php contains its own jevents.defines.php self-bootstrap guard
// (lines ~34 and ~1695). This is safe: by the time PSR-4 triggers this shim
// the defines file has already run, so the re-include is a no-op.
//
// TODO: migrate body from component/site/libraries/helper.php
if (!class_exists('JEVHelper', false))
{
	require_once \JEV_PATH . 'libraries/helper.php';
}

\class_alias('JEVHelper', __NAMESPACE__ . '\\Helper');
