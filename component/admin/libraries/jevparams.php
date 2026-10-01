<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die();
@trigger_error('Directly including jevparams.php is deprecated. Use JEvents\Administrator\Parameter via PSR-4 autoloading instead.', E_USER_DEPRECATED);

if (!class_exists('JEvents\\Administrator\\Parameter', false))
{
	require_once __DIR__ . '/src/Administrator/Parameter.php';
}

if (!class_exists('JevParameter', false))
{
	class_alias('JEvents\\Administrator\\Parameter', 'JevParameter');
}