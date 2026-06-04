<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

// Class body moved to component/site/libraries/src/Config.php (JEvents\Config).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Config', false))
{
	require_once JPATH_SITE . '/components/com_jevents/libraries/src/Config.php';
}

if (!class_exists('JEVConfig', false))
{
	class_alias('JEvents\\Config', 'JEVConfig');
}
