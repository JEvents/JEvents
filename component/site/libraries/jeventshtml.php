<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including jeventshtml.php is deprecated. Use JEvents\HtmlHelper via PSR-4 autoloading instead.', E_USER_DEPRECATED);

// Class body moved to component/site/libraries/src/HtmlHelper.php (JEvents\HtmlHelper).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\HtmlHelper', false))
{
	require_once __DIR__ . '/src/HtmlHelper.php';
}

if (!class_exists('JEventsHTML', false))
{
	class_alias('JEvents\\HtmlHelper', 'JEventsHTML');
}