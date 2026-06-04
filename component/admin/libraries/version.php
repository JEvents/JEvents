<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

// Class bodies moved to component/site/libraries/src/Version.php
// (JEvents\Version and JEvents\JevJoomlaVersion).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Version', false))
{
	require_once JPATH_SITE . '/components/com_jevents/libraries/src/Version.php';
}

if (!class_exists('JEventsVersion', false))
{
	class_alias('JEvents\\Version', 'JEventsVersion');
}

if (!class_exists('JevJoomlaVersion', false))
{
	class_alias('JEvents\\JevJoomlaVersion', 'JevJoomlaVersion');
}
