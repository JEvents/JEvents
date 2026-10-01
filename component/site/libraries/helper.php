<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including helper.php is deprecated. Use JEvents\Helper via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to src/Helper.php (JEvents\Helper).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Helper', false))
{
	require_once __DIR__ . '/src/Helper.php';
}

if (!class_exists('JEVHelper', false))
{
	class_alias('JEvents\\Helper', 'JEVHelper');
}