<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including csvLine.php is deprecated. Use JEvents\CsvLine via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/site/libraries/src/CsvLine.php (JEvents\CsvLine).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\CsvLine', false))
{
	require_once __DIR__ . '/src/CsvLine.php';
}

if (!class_exists('CsvLine', false))
{
	class_alias('JEvents\\CsvLine', 'CsvLine');
}