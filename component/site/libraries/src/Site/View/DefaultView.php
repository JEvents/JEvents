<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Site\View;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
//
// The source file also defines the MASK_* global constants at file scope —
// these are emitted as a side-effect of the require_once below and remain
// available globally after the first load.
//
// Dependency: JEventsDefaultView extends JEventsAbstractView. If AbstractView
// is not yet loaded when this shim runs, JLoader's alias mechanism triggers
// loading JEvents\Administrator\View\AbstractView automatically.
//
// TODO: migrate body from component/site/views/default/abstract/abstract.php
if (!class_exists('JEventsDefaultView', false))
{
	require_once \JEV_PATH . 'views/default/abstract/abstract.php';
}

\class_alias('JEventsDefaultView', __NAMESPACE__ . '\\DefaultView');
