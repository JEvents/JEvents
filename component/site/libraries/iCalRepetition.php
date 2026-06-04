<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including iCalRepetition.php is deprecated. Use JEvents\IcalRepetition via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/IcalRepetition.php (JEvents\IcalRepetition).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\IcalRepetition', false))
{
	require_once __DIR__ . '/src/IcalRepetition.php';
}

if (!class_exists('iCalRepetition', false))
{
	class_alias('JEvents\\IcalRepetition', 'iCalRepetition');
}
