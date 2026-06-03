<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Administrator;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// Note: distinct from JEvents\Helper (the shared JEVHelper). This class
// provides admin-specific helpers (version, toolbar, language loading).
// TODO: migrate body from component/admin/helpers/jevents.php
if (!class_exists('JEventsHelper', false))
{
	require_once \JEV_ADMINPATH . 'helpers/jevents.php';
}

\class_alias('JEventsHelper', __NAMESPACE__ . '\\Helper');
