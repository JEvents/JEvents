<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including compatwin.php is deprecated. Use JEvents\CompatWin via PSR-4 autoloading instead.', E_USER_DEPRECATED);

if (!class_exists('JEvents\\CompatWin', false))
{
	require_once __DIR__ . '/src/CompatWin.php';
}

if (!class_exists('JEV_CompatWin', false))
{
	class_alias('JEvents\\CompatWin', 'JEV_CompatWin');
}