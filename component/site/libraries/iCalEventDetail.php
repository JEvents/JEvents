<?php
/**
 * @package     JEvents
 */
defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalEventDetail.php is deprecated. Use JEvents\IcalEventDetail via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/IcalEventDetail.php (JEvents\IcalEventDetail).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\IcalEventDetail', false))
{
    require_once __DIR__ . '/src/IcalEventDetail.php';
}

if (!class_exists('iCalEventDetail', false))
{
    class_alias('JEvents\\IcalEventDetail', 'iCalEventDetail');
}
