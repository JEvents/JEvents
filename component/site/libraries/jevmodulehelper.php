<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jevmodulehelper.php is deprecated. Use JEvents\ModuleHelper via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/ModuleHelper.php (JEvents\ModuleHelper).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\ModuleHelper', false))
{
	require_once __DIR__ . '/src/ModuleHelper.php';
}

if (!class_exists('JevModuleHelper', false))
{
	class_alias('JEvents\\ModuleHelper', 'JevModuleHelper');
}
