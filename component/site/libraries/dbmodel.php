<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including dbmodel.php is deprecated. Use JEvents\DBModel via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/DBModel.php (JEvents\DBModel).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\DBModel', false))
{
	require_once __DIR__ . '/src/DBModel.php';
}

if (!class_exists('JEventsDBModel', false))
{
	class_alias('JEvents\\DBModel', 'JEventsDBModel');
}