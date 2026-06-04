<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including datamodel.php is deprecated. Use JEvents\DataModel via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/DataModel.php (JEvents\DataModel).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\DataModel', false))
{
	require_once __DIR__ . '/src/DataModel.php';
}

if (!class_exists('JEventsDataModel', false))
{
	class_alias('JEvents\\DataModel', 'JEventsDataModel');
}