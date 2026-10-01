<?php
/**
 * @package     JEvents
 */
defined('_JEXEC') or die('Restricted access');

// Class body moved to component/admin/libraries/src/Administrator/Helper.php
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Administrator\\Helper', false))
{
    require_once JPATH_ADMINISTRATOR . '/components/com_jevents/libraries/src/Administrator/Helper.php';
}

if (!class_exists('JEventsHelper', false))
{
    class_alias('JEvents\\Administrator\\Helper', 'JEventsHelper');
}
