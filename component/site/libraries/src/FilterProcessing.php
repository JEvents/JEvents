<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

// Transitional shim: loads the legacy global classes (jevFilterProcessing,
// jevFilter, jevBooleanFilter, jevTitleFilter) and exposes the primary class
// under the JEvents namespace. The helper base classes (jevFilter etc.) remain
// in the global namespace — filter plugins that extend jevFilter continue to
// work unchanged.
// TODO: migrate body from component/site/libraries/filters.php
if (!class_exists('jevFilterProcessing', false))
{
	require_once \JEV_PATH . 'libraries/filters.php';
}

\class_alias('jevFilterProcessing', __NAMESPACE__ . '\\FilterProcessing');
