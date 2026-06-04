<?php
/**
 * @package     JEvents
 */
defined('_JEXEC') or die('Restricted access');

// Class body moved to component/site/libraries/src/Site/Modal.php (JEvents\Site\Modal).
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Site\\Modal', false))
{
    require_once JPATH_SITE . '/components/com_jevents/libraries/src/Site/Modal.php';
}

if (!class_exists('JevModal', false))
{
    class_alias('JEvents\\Site\\Modal', 'JevModal');
}
