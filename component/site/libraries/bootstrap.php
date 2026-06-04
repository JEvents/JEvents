<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;
@trigger_error('Directly including bootstrap.php is deprecated. Use JEvents\Site\Modal via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/HtmlBootstrap.php (JEvents\HtmlBootstrap).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\HtmlBootstrap', false))
{
	require_once __DIR__ . '/src/HtmlBootstrap.php';
}

if (!class_exists('JevHtmlBootstrap', false))
{
	class_alias('JEvents\\HtmlBootstrap', 'JevHtmlBootstrap');
}
