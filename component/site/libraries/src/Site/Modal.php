<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Site;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
// TODO: migrate body from libraries/jevmodal/jevmodal.php
if (!class_exists('JevModal', false))
{
	require_once \JPATH_LIBRARIES . '/jevents/jevmodal/jevmodal.php';
}

\class_alias('JevModal', __NAMESPACE__ . '\\Modal');
