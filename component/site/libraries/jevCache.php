<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jevCache.php is deprecated. Use JEvents\Cache via PSR-4 autoloading instead.', E_USER_DEPRECATED);

if (!class_exists('JEvents\\Cache', false))
{
	require_once __DIR__ . '/src/Cache.php';
}

if (!class_exists('jevCache', false))
{
	class_alias('JEvents\\Cache', 'jevCache');
}