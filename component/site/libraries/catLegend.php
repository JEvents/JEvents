<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
@trigger_error('Directly including catLegend.php is deprecated. Use JEvents\Site\CatLegend via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Class body moved to component/site/libraries/src/Site/CatLegend.php (JEvents\Site\CatLegend).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Site\\CatLegend', false))
{
	require_once __DIR__ . '/src/Site/CatLegend.php';
}

if (!class_exists('catLegend', false))
{
	class_alias('JEvents\\Site\\CatLegend', 'catLegend');
}
