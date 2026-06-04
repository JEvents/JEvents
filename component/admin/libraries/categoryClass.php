<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including categoryClass.php is deprecated. Use JEvents\Administrator\Category via PSR-4 autoloading instead.', E_USER_DEPRECATED);

if (!class_exists('JEvents\\Administrator\\Category', false))
{
	require_once __DIR__ . '/src/Administrator/Category.php';
}

if (!class_exists('JEventsCategory', false))
{
	class_alias('JEvents\\Administrator\\Category', 'JEventsCategory');
}