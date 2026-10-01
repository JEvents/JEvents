<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalException.php is deprecated. Use JEvents\IcalException via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/IcalException.php (JEvents\IcalException).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\IcalException', false))
{
	require_once __DIR__ . '/src/IcalException.php';
}

if (!class_exists('iCalException', false))
{
	class_alias('JEvents\\IcalException', 'iCalException');
}
