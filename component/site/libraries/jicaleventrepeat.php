<?php
/**
 * @package     JEvents
 */
defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jicaleventrepeat.php is deprecated. Use JEvents\IcalEventRepeat via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/IcalEventRepeat.php (JEvents\IcalEventRepeat).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\IcalEventRepeat', false))
{
    require_once __DIR__ . '/src/IcalEventRepeat.php';
}

if (!class_exists('jIcalEventRepeat', false))
{
    class_alias('JEvents\\IcalEventRepeat', 'jIcalEventRepeat');
}
