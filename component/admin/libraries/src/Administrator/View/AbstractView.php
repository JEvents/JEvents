<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Administrator\View;

// Transitional shim: loads the legacy global class and exposes it under the
// JEvents namespace. Move the class body here when ready to fully migrate.
//
// This is the base view class for both admin and site themes. Every theme
// abstract (JEventsDefaultView, JEventsExtView, etc.) extends this class.
// Addon view overrides that do `extends JEventsAbstractView` continue to work
// via the class alias created after this shim loads.
//
// TODO: migrate body from component/admin/views/abstract/abstract.php
if (!class_exists('JEventsAbstractView', false))
{
	require_once \JEV_ADMINPATH . 'views/abstract/abstract.php';
}

\class_alias('JEventsAbstractView', __NAMESPACE__ . '\\AbstractView');
