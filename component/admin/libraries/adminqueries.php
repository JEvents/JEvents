<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including adminqueries.php is deprecated. Use JEvents\Administrator\AdminDBModel via PSR-4 autoloading instead.', E_USER_DEPRECATED);

JEVHelper::loadLanguage('admin');

// Class body moved to component/admin/libraries/src/Administrator/AdminDBModel.php (JEvents\Administrator\AdminDBModel).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Administrator\\AdminDBModel', false))
{
	require_once __DIR__ . '/src/Administrator/AdminDBModel.php';
}

if (!class_exists('JEventsAdminDBModel', false))
{
	class_alias('JEvents\\Administrator\\AdminDBModel', 'JEventsAdminDBModel');
}