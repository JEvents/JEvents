<?php
/**
 * @package     JEvents
 */
defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including csvToiCal.php is deprecated. Use JEvents\CsvToIcal via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/CsvToIcal.php (JEvents\CsvToIcal).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\CsvToIcal', false))
{
    require_once __DIR__ . '/src/CsvToIcal.php';
}

if (!class_exists('CsvToiCal', false))
{
    class_alias('JEvents\\CsvToIcal', 'CsvToiCal');
}
