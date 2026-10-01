<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
@trigger_error('Directly including filters.php is deprecated. Use JEvents\FilterProcessing via PSR-4 autoloading instead.', E_USER_DEPRECATED);


// Primary class body moved to src/FilterProcessing.php (JEvents\FilterProcessing).
// Helper classes jevFilter, jevBooleanFilter, jevTitleFilter are also defined there
// in the global namespace so filter plugins that extend jevFilter continue to work.
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\FilterProcessing', false))
{
    require_once __DIR__ . '/src/FilterProcessing.php';
}

if (!class_exists('jevFilterProcessing', false))
{
    class_alias('JEvents\\FilterProcessing', 'jevFilterProcessing');
}
