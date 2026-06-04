<?php
defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalImport.php is deprecated. Use JEvents\IcalImport via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to src/IcalImport.php (JEvents\IcalImport).
if (!class_exists('JEvents\\IcalImport', false))
{
    require_once __DIR__ . '/src/IcalImport.php';
}
if (!class_exists('iCalImport', false))
{
    class_alias('JEvents\\IcalImport', 'iCalImport');
}
