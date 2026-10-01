<?php
defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalICSFile.php is deprecated. Use JEvents\IcalICSFile via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to src/IcalICSFile.php (JEvents\IcalICSFile).
if (!class_exists('JEvents\\IcalICSFile', false))
{
    require_once __DIR__ . '/src/IcalICSFile.php';
}
if (!class_exists('iCalICSFile', false))
{
    class_alias('JEvents\\IcalICSFile', 'iCalICSFile');
}
