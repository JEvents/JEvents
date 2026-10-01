<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including registry.php is deprecated. Use JEvents\Site\Registry via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/Site/Registry.php (JEvents\Site\Registry).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Site\\Registry', false))
{
	require_once __DIR__ . '/src/Site/Registry.php';
}

if (!class_exists('JevRegistry', false))
{
	class_alias('JEvents\\Site\\Registry', 'JevRegistry');
}
