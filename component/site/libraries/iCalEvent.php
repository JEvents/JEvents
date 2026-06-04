<?php
defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalEvent.php is deprecated. Use JEvents\IcalEvent via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to src/IcalEvent.php (JEvents\IcalEvent).
if (!class_exists('JEvents\\IcalEvent', false))
{
    require_once __DIR__ . '/src/IcalEvent.php';
}
if (!class_exists('iCalEvent', false))
{
    class_alias('JEvents\\IcalEvent', 'iCalEvent');
}
