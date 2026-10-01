<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including vCal.php is deprecated. Use JEvents\VCal / JEvents\VEvent via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class bodies moved to component/site/libraries/src/VCal.php (JEvents\VCal, JEvents\VEvent).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\VCal', false))
{
	require_once __DIR__ . '/src/VCal.php';
}

if (!class_exists('vCal', false))
{
	class_alias('JEvents\\VCal', 'vCal');
}

if (!class_exists('vEvent', false))
{
	class_alias('JEvents\\VEvent', 'vEvent');
}